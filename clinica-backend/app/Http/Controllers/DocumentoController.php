<?php

namespace App\Http\Controllers;

use App\Models\Documento;
use App\Models\Historial;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class DocumentoController extends Controller
{
    //Ver todos los documentos de un historial (según permisos)
    public function index($historial_id)
    {
        $historial = Historial::findOrFail($historial_id);
        $user = Auth::user();

        if (
            $user->hasRole('admin') ||
            ($user->hasRole('paciente') && $historial->paciente_id == $user->id) ||
            ($user->hasRole('especialista') && $historial->especialista_id == $user->id)
        ) {
            return response()->json($historial->documentos);
        }

        return response()->json(['error' => 'No autorizado'], 403);
    }

    //Subir documento al historial
    public function store(Request $request, $historial_id)
    {
        $request->validate([
            'nombre' => 'required|string',
            'archivo' => 'required|file|mimes:pdf,jpg,jpeg,png|max:10240',
        ]);

        $historial = Historial::findOrFail($historial_id);
        $user = Auth::user();

        if (
            !($user->hasRole('admin') ||
            ($user->hasRole('paciente') && $historial->paciente_id == $user->id) ||
            ($user->hasRole('especialista') && $historial->especialista_id == $user->id))
        ) {
            return response()->json(['error' => 'No autorizado'], 403);
        }

        $ruta = $request->file('archivo')->store('documentos', 'public');

        $documento = Documento::create([
            'historial_id' => $historial_id,
            'nombre' => $request->nombre,
            'archivo' => $ruta,
        ]);

        return response()->json($documento, 201);
    }

    //Descargar archivo
    public function descargar($id)
    {
        $documento = Documento::findOrFail($id);
        $historial = $documento->historial;
        $user = Auth::user();

        if (
            $user->hasRole('admin') ||
            ($user->hasRole('paciente') && $historial->paciente_id == $user->id) ||
            ($user->hasRole('especialista') && $historial->especialista_id == $user->id)
        ) {
            return Storage::disk('public')->download($documento->archivo);
        }

        return response()->json(['error' => 'No autorizado'], 403);
    }

    //Eliminar (soft) documento admin o dueño
    public function destroy($id)
    {
        $documento = Documento::findOrFail($id);
        $historial = $documento->historial;
        $user = Auth::user();

        if (
            $user->hasRole('admin') ||
            ($user->hasRole('paciente') && $historial->paciente_id == $user->id) ||
            ($user->hasRole('especialista') && $historial->especialista_id == $user->id)
        ) {
            Storage::disk('public')->delete($documento->archivo);
            $documento->delete();

            return response()->json(['message' => 'Documento eliminado']);
        }

        return response()->json(['error' => 'No autorizado'], 403);
    }
}
