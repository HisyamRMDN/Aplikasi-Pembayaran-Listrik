<?php

namespace App\Http\Controllers;

use App\Models\Level;
use App\Models\Pembayaran;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class PetugasController extends Controller
{
    public function index(Request $request)
    {
        $query = User::with('level')->orderBy('nama_admin');

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('nama_admin', 'like', "%{$search}%")
                  ->orWhere('username', 'like', "%{$search}%");
            });
        }

        if ($request->filled('id_level')) {
            $query->where('id_level', $request->id_level);
        }

        $petugas = $query->paginate(10)->withQueryString();
        $levels = Level::orderBy('id_level')->get();

        return view('admin.petugas.index', compact('petugas', 'levels'));
    }

    public function create()
    {
        $levels = Level::orderBy('id_level')->get();
        return view('admin.petugas.create', compact('levels'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'nama_admin' => 'required|string|max:100',
            'username' => 'required|string|max:50|alpha_dash|unique:users,username',
            'id_level' => 'required|exists:levels,id_level',
            'password' => 'required|string|min:6|confirmed',
        ], [
            'username.unique' => 'Username sudah digunakan oleh petugas lain.',
            'username.alpha_dash' => 'Username hanya boleh berisi huruf, angka, tanda hubung (-), atau garis bawah (_).',
            'password.confirmed' => 'Konfirmasi kata sandi tidak cocok.',
        ]);

        User::create([
            'nama_admin' => $validated['nama_admin'],
            'username' => $validated['username'],
            'id_level' => $validated['id_level'],
            'password' => Hash::make($validated['password']),
        ]);

        return redirect()->route('petugas.index')
            ->with('success', 'Akun petugas baru berhasil ditambahkan.');
    }

    public function edit(string $id)
    {
        $petugas = User::findOrFail($id);
        $levels = Level::orderBy('id_level')->get();

        return view('admin.petugas.edit', compact('petugas', 'levels'));
    }

    public function update(Request $request, string $id)
    {
        $petugas = User::findOrFail($id);

        $validated = $request->validate([
            'nama_admin' => 'required|string|max:100',
            'username' => [
                'required',
                'string',
                'max:50',
                'alpha_dash',
                Rule::unique('users', 'username')->ignore($petugas->id_user, 'id_user'),
            ],
            'id_level' => 'required|exists:levels,id_level',
            'password' => 'nullable|string|min:6|confirmed',
        ]);

        // Cegah Administrator menurunkan level dirinya sendiri agar tidak terkunci dari sistem
        if ((int) $petugas->id_user === (int) Auth::id() && (int) $validated['id_level'] !== (int) $petugas->id_level) {
            return back()
                ->withInput()
                ->withErrors(['id_level' => 'Anda tidak dapat mengubah level akses akun Anda sendiri.']);
        }

        $dataUpdate = [
            'nama_admin' => $validated['nama_admin'],
            'username' => $validated['username'],
            'id_level' => $validated['id_level'],
        ];

        // Jika password diisi, lakukan reset password petugas
        if (!empty($validated['password'])) {
            $dataUpdate['password'] = Hash::make($validated['password']);
        }

        $petugas->update($dataUpdate);

        return redirect()->route('petugas.index')
            ->with('success', 'Data akun petugas berhasil diperbarui.');
    }

    public function destroy(string $id)
    {
        $petugas = User::findOrFail($id);

        // 1. Cegah menghapus akun sendiri yang sedang login
        if ((int) $petugas->id_user === (int) Auth::id()) {
            return redirect()->route('petugas.index')
                ->with('error', 'Anda tidak dapat menghapus akun Anda sendiri yang sedang aktif.');
        }

        // 2. Cegah menghapus petugas yang sudah memiliki riwayat transaksi (menjaga integritas audit)
        $memilikiTransaksi = Pembayaran::where('id_user', $petugas->id_user)
            ->orWhere('id_user_batal', $petugas->id_user)
            ->exists();

        if ($memilikiTransaksi) {
            return redirect()->route('petugas.index')
                ->with('error', 'Akun petugas tidak dapat dihapus karena memiliki riwayat pemrosesan atau pembatalan transaksi.');
        }

        $petugas->delete();

        return redirect()->route('petugas.index')
            ->with('success', 'Akun petugas berhasil dihapus.');
    }
}
