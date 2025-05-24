<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Spatie\Permission\Models\Role;

class UserController extends Controller
{
    public function __construct()
    {
        // Solo admins pueden gestionar usuarios
        $this->middleware('role:administrador');
    }
    // Listar todos los usuarios (admin)
    public function index(Request $request)
{
    // Comprobar si el usuario está autenticado (aunque el middleware debería hacerlo)
    $user = $request->user();
    if (!$user) {
        return response()->json(['error' => 'No autenticado'], 401);
    }

    // Comprobar que el usuario tiene rol Administrador
    if (!$user->hasRole('Administrador')) {
        return response()->json(['error' => 'No autorizado'], 403);
    }

    // Obtener usuarios con campos específicos
    $usuarios = User::select('id', 'name', 'dni_usuario', 'telefono', 'email')
        ->with('roles:name')
        ->get();

    if ($usuarios->isEmpty()) {
        return response()->json(['message' => 'No se encontraron usuarios'], 404);
    }

    $usuariosFormateados = $usuarios->map(function ($usuario) {
        return [
            'id' => $usuario->id,
            'nombre' => $usuario->name,
            'dni' => $usuario->dni_usuario,
            'telefono' => $usuario->telefono,
            'email' => $usuario->email,
            'rol' => $usuario->roles->pluck('name')->first() ?? 'Usuario',
        ];
    });

    return response()->json($usuariosFormateados);
}

    // Cambiar el rol de un usuario
    public function updateRoles(Request $request, $id)
    {
        $user = User::findOrFail($id);
        $roles = $request->input('roles', []);

        // Limpia roles previos
        $user->syncRoles($roles);

        return response()->json([
            'message' => 'Roles actualizados correctamente',
            'user' => $user->load('roles')
        ]);
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
