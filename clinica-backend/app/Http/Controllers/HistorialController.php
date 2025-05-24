<?php

namespace App\Http\Controllers;

use App\Models\Historial;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class HistorialController extends Controller
{
    //Listar todos los historiales (solo admin)
    public function index()
    {
        if (!Auth::user()->hasRole('admin')) {
            return response()->json(['error' => 'No autorizado'], 403);
        }

        return response()->json(Historial::with(['paciente', 'especialista', 'documentos'])->get());
    }

    //Ver historial específico
    public function show($id)
    {
        $historial = Historial::with('documentos')->findOrFail($id);
        $user = Auth::user();

        if ($user->hasRole('admin')) {
            return response()->json($historial);
        }

        if ($user->hasRole('paciente') && $historial->paciente_id == $user->id) {
            return response()->json($historial);
        }

        if ($user->hasRole('especialista') && $historial->especialista_id == $user->id) {
            return response()->json($historial);
        }

        return response()->json(['error' => 'No autorizado'], 403);
    }

    //Crear historial (solo especialista)
    public function store(Request $request)
    {
        if (!Auth::user()->hasRole('especialista')) {
            return response()->json(['error' => 'No autorizado'], 403);
        }

        $request->validate([
            'paciente_id' => 'required|exists:users,id',
            'descripcion' => 'nullable|string'
        ]);

        $historial = Historial::create([
            'paciente_id' => $request->paciente_id,
            'especialista_id' => Auth::id(),
            'descripcion' => $request->descripcion,
        ]);

        return response()->json($historial, 201);
    }

    public function update(Request $request, $id)
    {
        $historial = Historial::findOrFail($id);

        if (!(Auth::user()->hasRole('especialista') && $historial->especialista_id == Auth::id())) {
            return response()->json(['error' => 'No autorizado'], 403);
        }

        $request->validate([
            'descripcion' => 'nullable|string',
        ]);

        $historial->update([
            'descripcion' => $request->descripcion,
        ]);

        return response()->json($historial);
    }

    //Eliminar historial (admin)
    public function destroy($id)
    {
        if (!Auth::user()->hasRole('admin')) {
            return response()->json(['error' => 'No autorizado'], 403);
        }

        $historial = Historial::findOrFail($id);
        $historial->delete();

        return response()->json(['message' => 'Historial eliminado']);
    }
}
