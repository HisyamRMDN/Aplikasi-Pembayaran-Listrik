<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use App\Models\Pelanggan;

class AuthController extends Controller
{

    public function showActivateForm()
    {
        return view('auth.activate');
    }

    public function activateAccount(Request $request)
    {
        $request->validate([
            'nomor_kwh' => 'required|numeric|digits_between:11,12',
            'username'  => 'required|string|max:50|unique:pelanggans,username|unique:users,username',
            'password'  => 'required|string|min:6|confirmed',
        ], [
            'username.unique'    => 'Username sudah digunakan, silakan pilih username lain.',
            'password.confirmed' => 'Konfirmasi password tidak cocok.',
        ]);

        $pelanggan = Pelanggan::where('nomor_kwh', $request->nomor_kwh)->first();

        if (!$pelanggan) {
            return back()->withErrors([
                'nomor_kwh' => 'Nomor KWH tidak ditemukan. Pastikan meteran Anda sudah didaftarkan petugas.',
            ])->withInput();
        }

        if (!is_null($pelanggan->username)) {
            return back()->withErrors([
                'nomor_kwh' => 'Nomor KWH ini sudah memiliki akun aktif. Silakan langsung masuk.',
            ])->withInput();
        }

        $pelanggan->update([
            'username' => $request->username,
            'password' => Hash::make($request->password),
        ]);

        Auth::guard('pelanggan')->login($pelanggan);
        $request->session()->regenerate();

        return redirect('/customer/dashboard');
    }
    public function showLoginForm()
    {
        return view('auth.login');
    }

    public function login(Request $request)
    {
        $request->validate([
            'username' => 'required',
            'password' => 'required',
        ]);

        $credentials = $request->only('username', 'password');

        // Coba login sebagai admin (default guard "web")
        if (Auth::attempt($credentials)) {
            $request->session()->regenerate();
            return redirect()->intended('/dashboard');
        }

        // Jika gagal, coba login sebagai pelanggan
        if (Auth::guard('pelanggan')->attempt($credentials)) {
            $request->session()->regenerate();
            return redirect()->intended('/customer/dashboard');
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
        return view('auth.login');
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
            return redirect()->intended('/customer/dashboard');
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
