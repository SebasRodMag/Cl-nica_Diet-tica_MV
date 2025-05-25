<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Cita;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class CitasController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('role:administrador|especialista');
    }

    public function index()
    {
        $user = Auth::user();

        if ($user->hasRole('administrador')) {
            $citas = Cita::with(['paciente', 'especialista'])->get();
        } elseif ($user->hasRole('especialista')) {
            $citas = Cita::with(['paciente', 'especialista'])
                ->where('especialista_id', $user->id)
                ->get();
        } else {
            return response()->json(['message' => 'No autorizado'], 403);
        }

        return response()->json($citas);
    }

    //Crear cita
    public function store(Request $request)
    {
        $user = Auth::user();

        if ($user->rol !== 'paciente') {
            return response()->json(['error' => 'Solo los pacientes pueden solicitar citas.'], 403);
        }

        $validated = $request->validate([
            'id_especialista' => 'required|exists:especialistas,id_especialista',
            'fecha_hora_cita' => 'required|date',
            'tipo_cita' => 'required|in:presencial,telemática',
        ]);

        // Verificar si ya existe una cita inicial
        $yaTienePrimera = Cita::where('id_paciente', $user->paciente->id_paciente)
            ->where('es_primera', true)
            ->exists();

        $cita = Cita::create([
            'id_paciente' => $user->paciente->id_paciente,
            'id_especialista' => $validated['id_especialista'],
            'fecha_hora_cita' => $validated['fecha_hora_cita'],
            'tipo_cita' => $validated['tipo_cita'],
            'estado' => 'pendiente',
            'es_primera' => !$yaTienePrimera,
        ]);

        return response()->json($cita, 201);
    }

    //Actualizar cita
    public function update(Request $request, $id)
    {
        $cita = Cita::findOrFail($id);

        $request->validate([
            'fecha' => 'date',
            'hora' => 'string',
            'estado' => 'in:pendiente,realizada,no realizada,cancelada',
            'comentarios' => 'nullable|string',
        ]);

        $cita->update($request->all());

        return response()->json([
            'message' => 'Cita actualizada correctamente',
            'cita' => $cita
        ]);
    }

    //Ver una cita específica
    public function show($id)
    {
        $cita = Cita::with(['paciente', 'especialista'])->find($id);

        if (!$cita) {
            return response()->json(['error' => 'Cita no encontrada'], 404);
        }

        return response()->json($cita);
    }

    //Cancelar una cita (solo cambia el estado)
    public function cancelar($id)
    {
        $cita = Cita::findOrFail($id);
        $user = Auth::user();

        // Solo paciente o especialista asignado puede cancelar su propia cita
        if (
            !($user->rol === 'administrador') &&
            !($user->rol === 'paciente' && $cita->id_paciente === $user->paciente->id_paciente) &&
            !($user->rol === 'especialista' && $cita->id_especialista === $user->especialista->id_especialista)
        ) {
            return response()->json(['error' => 'No autorizado.'], 403);
        }

        $cita->estado = 'cancelada';
        $cita->save();

        return response()->json(['message' => 'Cita cancelada.']);
    }

    //Eliminar (SoftDelete)
    public function destroy($id)
    {
        $cita = Cita::findOrFail($id);
        $cita->delete();

        return response()->json(['message' => 'Cita eliminada correctamente']);
    }


    public function finalizarPrimeraCita(Request $request, $id)
    {
        $user = Auth::user();

        if ($user->rol !== 'especialista') {
            return response()->json(['error' => 'Solo especialistas pueden finalizar citas.'], 403);
        }

        $cita = Cita::findOrFail($id);

        if (!$cita->es_primera || $cita->estado !== 'pendiente') {
            return response()->json(['error' => 'No es una cita inicial pendiente.'], 400);
        }

        if ($cita->id_especialista !== $user->especialista->id_especialista) {
            return response()->json(['error' => 'No tienes permiso para esta cita.'], 403);
        }

        $request->validate([
            'mantener_como_paciente' => 'required|boolean',
            'comentario' => 'nullable|string',
        ]);

        $cita->estado = 'realizada';
        $cita->comentario = $request->input('comentario');
        $cita->save();

        // Cambiar rol si es rechazado
        if (!$request->input('mantener_como_paciente')) {
            $paciente = $cita->paciente;
            $usuario = $paciente->usuario;
            $usuario->rol = 'usuario';
            $usuario->save();
        }

        return response()->json(['message' => 'Cita finalizada y paciente evaluado.']);
    }
}
