<?php
namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class CheckUsuarioRole
{
    public function handle(Request $request, Closure $next)
    {
        $user = $request->user();

        if (!$user || !$user->hasRole('Usuario')) {
            return response()->json(['error' => 'No autorizado, se requiere rol Usuario'], 403);
        }

        return $next($request);
    }
}
