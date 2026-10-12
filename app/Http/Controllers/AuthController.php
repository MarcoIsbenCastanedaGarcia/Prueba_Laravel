<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    public function showLogin()
    {
        if (Auth::check()) {
            /** @var User $user */
            $user = Auth::user();

            return $user->esDocente()
                ? redirect()->route('docente.dashboard')
                : redirect()->route('alumno.dashboard');
        }
        return view('auth.login');
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => 'required|email',
            'password' => 'required|string',
        ]);

        if (Auth::attempt($credentials, $request->boolean('remember'))) {
            $request->session()->regenerate();

            /** @var User $user */
            $user = Auth::user();

            if (!$user->activo) {
                Auth::logout();
                return back()->withErrors(['email' => 'Esta cuenta se encuentra desactivada.']);
            }

            if ($user->esDocente()) {
                return redirect()->intended(route('docente.dashboard'));
            }

            return redirect()->intended(route('alumno.dashboard'));
        }

        return back()->withErrors([
            'email' => 'Las credenciales especificadas no coinciden con nuestros registros.',
        ])->onlyInput('email');
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login')->with('success', 'Sesión cerrada correctamente.');
    }
}