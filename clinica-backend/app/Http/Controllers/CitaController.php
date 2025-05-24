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


    // Listar citas según rol
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
            // Podrías retornar vacío o lanzar error si paciente no autorizado
            return response()->json(['message' => 'No autorizado'], 403);
        }

        return response()->json($citas);
    }

    // Crear una nueva cita
    public function store(Request $request)
    {
        $request->validate([
            'paciente_id' => 'required|exists:users,id',
            'especialista_id' => 'required|exists:users,id',
            'fecha' => 'required|date',
            'hora' => 'required',
            'estado' => 'in:pendiente,realizada,no realizada,cancelada',
            'comentarios' => 'nullable|string',
        ]);

        $cita = Cita::create($request->all());

        return response()->json([
            'message' => 'Cita creada con éxito',
            'cita' => $cita
        ]);
    }

    // Actualizar una cita
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

    // Ver una cita específica
    public function show($id)
    {
        $cita = Cita::with(['paciente', 'especialista'])->find($id);

        if (!$cita) {
            return response()->json(['error' => 'Cita no encontrada'], 404);
        }

        return response()->json($cita);
    }

    // Cancelar una cita (solo cambia el estado)
    public function cancelar($id)
    {
        $cita = Cita::find($id);

        if (!$cita) {
            return response()->json(['error' => 'Cita no encontrada'], 404);
        }

        $cita->estado = 'cancelada';
        $cita->save();

        return response()->json(['mensaje' => 'Cita cancelada']);
    }

    // Eliminar (SoftDelete)
    public function destroy($id)
    {
        $cita = Cita::findOrFail($id);
        $cita->delete();

        return response()->json(['message' => 'Cita eliminada correctamente']);
    }
}
