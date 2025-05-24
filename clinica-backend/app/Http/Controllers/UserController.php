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
        $this->middleware('role:administrador');
    }

    public function index(Request $request)
    {
        $user = $request->user();
        if (!$user) {
            return response()->json(['error' => 'No autenticado'], 401);
        }

        if (!$user->hasRole('Administrador')) {
            return response()->json(['error' => 'No autorizado'], 403);
        }

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

    public function show($id)
    {
        $user = User::with('roles')->findOrFail($id);

        if (Auth::user()->hasRole('admin') || Auth::user()->hasRole('especialista')) {
            return response()->json($user);
        }

        return response()->json(['error' => 'No autorizado'], 403);
    }

    //Perfil del usuario autenticado
    public function me()
    {
        return response()->json(Auth::user()->load('roles'));
    }

    //Cambiar rol
    public function updateRole(Request $request, $id)
    {
        $request->validate([
            'role' => 'required|string|exists:roles,name',
        ]);

        $user = User::findOrFail($id);
        $auth = Auth::user();
        $newRole = $request->input('role');

        if ($auth->hasRole('admin') || ($auth->hasRole('especialista') && $user->hasRole('paciente') && $newRole === 'usuario')) {
            $user->syncRoles([$newRole]);
            return response()->json(['message' => 'Rol actualizado correctamente']);
        }

        return response()->json(['error' => 'No autorizado'], 403);
    }

    //Soft delete
    public function destroy($id)
    {
        $this->authorize('admin-only'); // Solo admin puede borrar
        $user = User::findOrFail($id);
        $user->delete();

        return response()->json(['message' => 'Usuario eliminado correctamente']);
    }
}
