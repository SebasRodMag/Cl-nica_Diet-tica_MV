<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Spatie\Permission\Models\Role;

class UserController extends Controller
{
    // Listar todos los usuarios (admin)
    public function index()
    {
        $this->authorize('admin-only'); // Asegúrate de tener una policy o usa middleware

        return response()->json(User::with('roles')->get());
    }

    // Ver detalles de un usuario
    public function show($id)
    {
        $user = User::with('roles')->findOrFail($id);

        // Solo Admin o Especialista puede ver otros usuarios
        if (Auth::user()->hasRole('admin') || Auth::user()->hasRole('especialista')) {
            return response()->json($user);
        }

        return response()->json(['error' => 'No autorizado'], 403);
    }

    // Mostrar perfil del usuario autenticado
    public function me()
    {
        return response()->json(Auth::user()->load('roles'));
    }

    // Cambiar rol
    public function updateRole(Request $request, $id)
    {
        $request->validate([
            'role' => 'required|string|exists:roles,name',
        ]);

        $user = User::findOrFail($id);
        $auth = Auth::user();
        $newRole = $request->input('role');

        // Solo admin o especialista pueden cambiar roles
        if ($auth->hasRole('admin') || ($auth->hasRole('especialista') && $user->hasRole('paciente') && $newRole === 'usuario')) {
            $user->syncRoles([$newRole]);
            return response()->json(['message' => 'Rol actualizado correctamente']);
        }

        return response()->json(['error' => 'No autorizado'], 403);
    }

    // Soft delete
    public function destroy($id)
    {
        $this->authorize('admin-only'); // Solo admin puede borrar
        $user = User::findOrFail($id);
        $user->delete();

        return response()->json(['message' => 'Usuario eliminado correctamente']);
    }
}
