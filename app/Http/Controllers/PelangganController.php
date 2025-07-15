<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Pelanggan;
use App\Models\Tarif;
use Illuminate\Support\Facades\Hash;

class PelangganController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $pelanggans = Pelanggan::with('tarif')->paginate(10);
        return view('admin.pelanggan.index', compact('pelanggans'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $tarifs = Tarif::all();
        return view('admin.pelanggan.create', compact('tarifs'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'username' => 'required|string|max:50|unique:pelanggans',
            'password' => 'required|string|min:6',
            'nomor_kwh' => 'required|string|max:20|unique:pelanggans',
            'nama_pelanggan' => 'required|string|max:100',
            'alamat' => 'required|string',
            'id_tarif' => 'required|exists:tarifs,id_tarif'
        ]);

        Pelanggan::create([
            'username' => $request->username,
            'password' => Hash::make($request->password),
            'nomor_kwh' => $request->nomor_kwh,
            'nama_pelanggan' => $request->nama_pelanggan,
            'alamat' => $request->alamat,
            'id_tarif' => $request->id_tarif
        ]);

        return redirect()->route('pelanggan.index')
            ->with('success', 'Pelanggan berhasil ditambahkan');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $pelanggan = Pelanggan::with('tarif', 'penggunaans', 'tagihans')->findOrFail($id);
        return view('admin.pelanggan.show', compact('pelanggan'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $pelanggan = Pelanggan::findOrFail($id);
        $tarifs = Tarif::all();
        return view('admin.pelanggan.edit', compact('pelanggan', 'tarifs'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $pelanggan = Pelanggan::findOrFail($id);

        $request->validate([
            'username' => 'required|string|max:50|unique:pelanggans,username,' . $id . ',id_pelanggan',
            'nomor_kwh' => 'required|string|max:20|unique:pelanggans,nomor_kwh,' . $id . ',id_pelanggan',
            'nama_pelanggan' => 'required|string|max:100',
            'alamat' => 'required|string',
            'id_tarif' => 'required|exists:tarifs,id_tarif'
        ]);

        $updateData = [
            'username' => $request->username,
            'nomor_kwh' => $request->nomor_kwh,
            'nama_pelanggan' => $request->nama_pelanggan,
            'alamat' => $request->alamat,
            'id_tarif' => $request->id_tarif
        ];

        if ($request->password) {
            $updateData['password'] = Hash::make($request->password);
        }

        $pelanggan->update($updateData);

        return redirect()->route('pelanggan.index')
            ->with('success', 'Pelanggan berhasil diperbarui');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $pelanggan = Pelanggan::findOrFail($id);
        $pelanggan->delete();

        return redirect()->route('pelanggan.index')
            ->with('success', 'Pelanggan berhasil dihapus');
    }
}
