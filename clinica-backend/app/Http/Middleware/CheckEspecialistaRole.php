<?php
namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class CheckEspecialistaRole
{
    public function handle(Request $request, Closure $next)
    {
        $user = $request->user();

        if (!$user || !$user->hasRole('Especialista')) {
            return response()->json(['error' => 'No autorizado, se requiere rol Especialista'], 403);
        }

        return $next($request);
    }
}
