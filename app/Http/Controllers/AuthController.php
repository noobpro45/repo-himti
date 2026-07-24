<?php

namespace App\Http\Controllers;

use App\Models\LogAktivitas;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    public function showLogin()
    {
        if (Auth::check()) {
            return redirect()->route('dashboard');
        }

        return view('auth.login');
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'username' => 'required|string',
            'password' => 'required|string',
        ]);

        if (Auth::attempt([
            'username'  => $credentials['username'],
            'password'  => $credentials['password'],
            'is_aktif'  => true,
        ], $request->boolean('remember'))) {
            $request->session()->regenerate();

            LogAktivitas::catat(
                Auth::id(),
                'Login ke sistem',
                $request->ip()
            );

            return redirect()->intended(route('dashboard'));
        }

        return back()->withErrors([
            'username' => 'NIM atau Password salah. Periksa kembali kredensial Anda dan coba lagi.',
        ])->onlyInput('username');
    }

    public function logout(Request $request)
    {
        if (Auth::check()) {
            LogAktivitas::catat(
                Auth::id(),
                'Logout dari sistem',
                $request->ip()
            );
        }

        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login');
    }
}
