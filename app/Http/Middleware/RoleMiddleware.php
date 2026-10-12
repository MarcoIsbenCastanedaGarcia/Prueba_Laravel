<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class RoleMiddleware
{
    public function handle(Request $request, Closure $next, string $role): Response
    {
        if (!Auth::check()) {
            return redirect()->route('login')->with('error', 'Debe iniciar sesión primero.');
        }

        $user = Auth::user();

        if (!$user->activo) {
            Auth::logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();
            return redirect()->route('login')->with('error', 'Su cuenta se encuentra desactivada.');
        }

        if ($user->role !== $role) {
            abort(403, 'Acceso denegado: No cuenta con los permisos necesarios para esta sección.');
        }

        return $next($request);
    }
}