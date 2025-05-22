<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\Auth;
use Spatie\Permission\Models\Role;
use App\Models\Log;

class AuthController extends Controller
{
    // Registro de usuario
    public function register(Request $request)
    {
        $request->validate([
            'nombre' => 'required|string|max:255',
            'email' => 'required|email|unique:users',
            'password' => 'required|string|confirmed|min:6',
        ]);

        $user = User::create([
            'nombre' => $request->nombre,
            'email' => $request->email,
            'password' => bcrypt($request->password),
        ]);

        $user->assignRole('usuario'); // por defecto

        $this->registrarLog($user->id, 'Registro', 'users', $user->id);

        return response()->json(['mensaje' => 'Usuario registrado correctamente'], 201);
    }

    // Login
    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required'
        ]);

        $user = User::where('email', $request->email)->first();

        if (! $user || ! Hash::check($request->password, $user->password)) {
            throw ValidationException::withMessages([
                'email' => ['Las credenciales son incorrectas'],
            ]);
        }

        $token = $user->createToken('auth_token')->plainTextToken;

        $this->registrarLog($user->id, 'Login', 'users', $user->id);

        return response()->json([
            'token' => $token,
            'user' => $user->only('id', 'nombre', 'email'),
            'roles' => $user->getRoleNames()
        ]);
    }

    // Logout
    public function logout(Request $request)
    {
        $user = $request->user();

        $user->currentAccessToken()->delete();

        $this->registrarLog($user->id, 'Logout', 'users', $user->id);

        return response()->json(['mensaje' => 'Sesión cerrada']);
    }

    // Perfil autenticado
    public function perfil(Request $request)
    {
        return response()->json([
            'user' => $request->user(),
            'roles' => $request->user()->getRoleNames(),
        ]);
    }

    private function registrarLog($userId, $accion, $tabla, $registroId)
    {
        Log::create([
            'user_id' => $userId,
            'accion' => $accion,
            'tabla_afectada' => $tabla,
            'registro_id' => $registroId,
        ]);
    }
}
