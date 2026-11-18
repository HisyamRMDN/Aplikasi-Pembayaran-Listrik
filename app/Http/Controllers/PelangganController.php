<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Pelanggan;
use App\Models\Tarif;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use App\Models\Tagihan;

class PelangganController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {

        $query = Pelanggan::with('tarif')->withCount(['penggunaans', 'tagihans']);

        if ($request->has('search') && $request->search != '') {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('nama_pelanggan', 'like', '%' . $search . '%')
                    ->orWhere('nomor_kwh', 'like', '%' . $search . '%');
            });
        }

        $pelanggans = $query->paginate(10)->withQueryString(); // penting agar parameter search tetap ada di pagination

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
            'nomor_kwh' => 'required|numeric|digits_between:11,12|unique:pelanggans,nomor_kwh',
            'nama_pelanggan' => 'required|string|max:100',
            'alamat' => 'required|string',
            'id_tarif' => 'required|exists:tarifs,id_tarif'
        ]);

        Pelanggan::create([
            'nomor_kwh' => $request->nomor_kwh,
            'nama_pelanggan' => $request->nama_pelanggan,
            'alamat' => $request->alamat,
            'id_tarif' => $request->id_tarif
        ]);

        return redirect()->route('pelanggan.index')
            ->with('success', 'Data meteran pelanggan berhasil ditambahkan');
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
            'nomor_kwh' => 'required|numeric|digits_between:11,12|unique:pelanggans,nomor_kwh,' . $id . ',id_pelanggan',
            'nama_pelanggan' => 'required|string|max:100',
            'alamat' => 'required|string',
            'id_tarif' => 'required|exists:tarifs,id_tarif'
        ]);

        $pelanggan->update([
            'nomor_kwh' => $request->nomor_kwh,
            'nama_pelanggan' => $request->nama_pelanggan,
            'alamat' => $request->alamat,
            'id_tarif' => $request->id_tarif
        ]);

        return redirect()->route('pelanggan.index')
            ->with('success', 'Data pelanggan berhasil diperbarui');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $pelanggan = Pelanggan::findOrFail($id);

        if ($pelanggan->penggunaans()->exists() || $pelanggan->tagihans()->exists()) {
            return redirect()->route('pelanggan.index')
                ->with('error', 'Pelanggan tidak dapat dihapus karena masih memiliki riwayat penggunaan atau tagihan.');
        }

        $pelanggan->delete();

        return redirect()->route('pelanggan.index')
            ->with('success', 'Pelanggan berhasil dihapus');
    }

    public function riwayatPenggunaan()
    {
        $pelanggan = Auth::guard('pelanggan')->user();
        $penggunaan = $pelanggan->penggunaans()->orderByDesc('tahun')->orderByDesc('bulan')->get();

        return view('pelanggan.penggunaan', compact('penggunaan', 'pelanggan'));
    }

    public function struk(string $id)
    {
        /** @var Pelanggan $pelanggan */
        $pelanggan = Auth::guard('pelanggan')->user();

        $tagihan = Tagihan::with([
            'pelanggan.tarif',
            'penggunaan',
            'pembayarans' => fn ($q) => $q->where('status', 'berhasil')->with('user'),
        ])
            ->where('id_pelanggan', $pelanggan->id_pelanggan)
            ->findOrFail($id);

        $pembayaran = $tagihan->pembayarans->first();

        if (! $tagihan->isLunas() || ! $pembayaran) {
            return redirect()->route('pelanggan.tagihan')
                ->with('error', 'Struk pembayaran hanya tersedia untuk tagihan yang sudah lunas.');
        }

        return view('pelanggan.struk', compact('tagihan', 'pembayaran', 'pelanggan'));
    }

    public function editProfil()
    {
        /** @var Pelanggan $pelanggan */
        $pelanggan = Auth::guard('pelanggan')->user();
        $pelanggan->load('tarif');

        return view('pelanggan.profil', compact('pelanggan'));
    }

    public function updateProfil(Request $request)
    {
        /** @var Pelanggan $pelanggan */
        $pelanggan = Auth::guard('pelanggan')->user();

        $validated = $request->validate([
            'nama_pelanggan' => 'required|string|max:100',
            'alamat' => 'required|string|max:255',
        ]);

        $pelanggan->update($validated);

        return redirect()->route('pelanggan.profil.edit')
            ->with('success', 'Data profil berhasil diperbarui.');
    }

    public function updatePassword(Request $request)
    {
        /** @var Pelanggan $pelanggan */
        $pelanggan = Auth::guard('pelanggan')->user();

        $request->validate([
            'current_password' => 'required|string',
            'password' => 'required|string|min:6|confirmed',
        ]);

        if (! Hash::check($request->current_password, $pelanggan->password)) {
            return back()->withErrors([
                'current_password' => 'Kata sandi saat ini tidak sesuai.',
            ]);
        }

        $pelanggan->update([
            'password' => Hash::make($request->password),
        ]);

        return redirect()->route('pelanggan.profil.edit')
            ->with('success', 'Kata sandi berhasil diperbarui.');
    }
}
