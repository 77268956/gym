<?php

namespace App\Http\Controllers;

use App\Models\Empleado;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    public function showLoginForm()
    {
        if (Auth::check()) {
            return redirect()->route('dashboard');
        }

        return view('auth.login');
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => ['required', 'string', 'max:255'],
            'password' => ['required'],
        ]);
        $loginIdentifier = mb_strtolower(trim($credentials['email']));

        $empleado = Empleado::where('estado', 'activo')
            ->where(function ($query) use ($loginIdentifier): void {
                $query->whereRaw('LOWER(email) = ?', [$loginIdentifier])
                    ->orWhereRaw('LOWER(usuario) = ?', [$loginIdentifier]);
            })
            ->first();

        if ($empleado && Hash::check($credentials['password'], $empleado->password_hash)) {
            Auth::login($empleado);
            $request->session()->regenerate();

            return redirect()->intended('dashboard');
        }

        return back()->withErrors([
            'email' => 'Las credenciales proporcionadas no coinciden con nuestros registros.',
        ])->onlyInput('email');
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/');
    }
}
