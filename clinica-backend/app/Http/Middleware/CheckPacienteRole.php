<?php
namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class CheckPacienteRole
{
    public function handle(Request $request, Closure $next)
    {
        $user = $request->user();

        if (!$user || !$user->hasRole('Paciente')) {
            return response()->json(['error' => 'No autorizado, se requiere rol Paciente'], 403);
        }

        return $next($request);
    }
}
