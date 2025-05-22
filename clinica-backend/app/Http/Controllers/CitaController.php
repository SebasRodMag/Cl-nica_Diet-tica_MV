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
    // Listar citas según rol
    public function index()
    {
        $user = Auth::user();

        if ($user->hasRole('admin') || $user->hasRole('especialista')) {
            $citas = Cita::with(['paciente', 'especialista'])->get();
        } elseif ($user->hasRole('paciente')) {
            $citas = Cita::with(['especialista'])->where('paciente_id', $user->id)->get();
        } else {
            return response()->json(['error' => 'No autorizado'], 403);
        }

        return response()->json($citas);
    }

    // Crear una nueva cita
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'paciente_id' => 'required|exists:users,id',
            'especialista_id' => 'required|exists:users,id',
            'fecha' => 'required|date',
            'hora' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        $cita = Cita::create([
            'paciente_id' => $request->paciente_id,
            'especialista_id' => $request->especialista_id,
            'fecha' => $request->fecha,
            'hora' => $request->hora,
            'estado' => 'pendiente',
        ]);

        return response()->json(['mensaje' => 'Cita creada con éxito', 'cita' => $cita], 201);
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
        $cita = Cita::find($id);

        if (!$cita) {
            return response()->json(['error' => 'Cita no encontrada'], 404);
        }

        $cita->delete();

        return response()->json(['mensaje' => 'Cita eliminada']);
    }
}
