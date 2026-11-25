<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ApiIdentity
{
    public function handle(Request $request, Closure $next)
    {
        $request->headers->set('Accept', 'application/json');
        Auth::shouldUse('sanctum');
        if ($request->bearerToken() && ! Auth::user()) {
            abort(401, 'Token inválido o expirado.');
        }

        return $next($request);
    }
}
