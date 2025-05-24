<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class CheckAdminRole
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next)
    {
        $user = $request->user();

        if (!$user || !$user->hasRole('Administrador')) {
            return response()->json(['error' => 'No autorizado, se requiere rol Administrador'], 403);
        }

        return $next($request);
    }
}
