<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use App\Models\Pelanggan;

class AuthController extends Controller
{
    public function showLoginForm()
    {
        return view('auth.login');
    }

    public function login(Request $request)
    {
        // dd($request->login_type);
        $request->validate([
            'username' => 'required',
            'password' => 'required',
            'login_type' => 'required|in:admin,pelanggan'
        ]);

        $credentials = $request->only('username', 'password');

        if ($request->login_type === 'admin') {
            // Admin login
            if (Auth::attempt($credentials)) {
                $request->session()->regenerate();
                return redirect()->intended('/dashboard');
            }
        } else {
            // Pelanggan login
            if (Auth::guard('pelanggan')->attempt($credentials)) {
                $request->session()->regenerate();
                return redirect()->intended('/pelanggan/dashboard');
            }
        }

        return back()->withErrors([
            'username' => 'Username atau password salah.',
        ])->onlyInput('username');
    }

    public function logout(Request $request)
    {
        $guard = $request->route()->getPrefix() === 'pelanggan' ? 'pelanggan' : 'web';

        Auth::guard($guard)->logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/');
    }

    // Pelanggan specific methods
    public function showPelangganLoginForm()
    {
        return view('auth.pelanggan-login');
    }

    public function pelangganLogin(Request $request)
    {
        $request->validate([
            'username' => 'required',
            'password' => 'required'
        ]);

        $credentials = $request->only('username', 'password');

        if (Auth::guard('pelanggan')->attempt($credentials)) {
            $request->session()->regenerate();
            return redirect()->intended('/pelanggan/dashboard');
        }

        return back()->withErrors([
            'username' => 'Username atau password salah.',
        ])->onlyInput('username');
    }

    public function pelangganLogout(Request $request)
    {
        Auth::guard('pelanggan')->logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/');
    }
}
