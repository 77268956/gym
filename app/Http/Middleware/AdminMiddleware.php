<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class AdminMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  Closure(Request): (Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (! Auth::check() || Auth::user()->rol !== 'admin') {
            if ($request->expectsJson()) {
                return response()->json(['ok' => false, 'mensaje' => 'Acceso denegado. Se requieren permisos de administrador.'], 403);
            }

            return redirect()->route('dashboard')->with('error', 'No tienes permisos para acceder a este módulo de administración.');
        }

        return $next($request);
    }
}
