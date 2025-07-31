<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Penggunaan;
use App\Models\Pelanggan;
use App\Models\Tagihan;

class PenggunaanController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = Penggunaan::with('pelanggan');

        // Filter berdasarkan search query (nama pelanggan atau nomor KWH)
        if ($request->filled('search')) {
            $search = $request->search;
            $query->whereHas('pelanggan', function ($q) use ($search) {
                $q->where('nama_pelanggan', 'like', "%{$search}%")
                    ->orWhere('nomor_kwh', 'like', "%{$search}%");
            });
        }

        // Filter berdasarkan bulan
        if ($request->filled('bulan')) {
            $query->where('bulan', $request->bulan);
        }

        // Filter berdasarkan tahun
        if ($request->filled('tahun')) {
            $query->where('tahun', $request->tahun);
        }

        // Ambil data dengan pagination
        $penggunaans = $query->orderBy('id_penggunaan', 'desc')->paginate(10)->appends($request->all());

        return view('admin.penggunaan.index', compact('penggunaans'));
    }


    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $pelanggans = Pelanggan::all();
        return view('admin.penggunaan.create', compact('pelanggans'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'id_pelanggan' => 'required|exists:pelanggans,id_pelanggan',
            'bulan' => 'required|integer|min:1|max:12',
            'tahun' => 'required|integer|min:2020',
            'meter_awal' => 'required|integer|min:0',
            'meter_akhir' => 'required|integer|min:0|gt:meter_awal'
        ]);

        // Check if usage already exists for this customer in this month/year
        $existingUsage = Penggunaan::where('id_pelanggan', $request->id_pelanggan)
            ->where('bulan', $request->bulan)
            ->where('tahun', $request->tahun)
            ->first();

        if ($existingUsage) {
            return back()->withErrors(['error' => 'Data penggunaan untuk pelanggan ini pada bulan/tahun tersebut sudah ada']);
        }

        $penggunaan = Penggunaan::create([
            'id_pelanggan' => $request->id_pelanggan,
            'bulan' => $request->bulan,
            'tahun' => $request->tahun,
            'meter_awal' => $request->meter_awal,
            'meter_akhir' => $request->meter_akhir
        ]);

        // Auto-generate tagihan
        $this->generateTagihan($penggunaan);

        return redirect()->route('penggunaan.index')
            ->with('success', 'Data penggunaan berhasil ditambahkan dan tagihan otomatis dibuat');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $penggunaan = Penggunaan::with('pelanggan', 'tagihans')->findOrFail($id);
        return view('admin.penggunaan.show', compact('penggunaan'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $penggunaan = Penggunaan::findOrFail($id);
        $pelanggans = Pelanggan::all();
        return view('admin.penggunaan.edit', compact('penggunaan', 'pelanggans'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        
        $penggunaan = Penggunaan::findOrFail($id);

        $request->validate([
            'id_pelanggan' => 'required|exists:pelanggans,id_pelanggan',
            'bulan' => 'required|integer|min:1|max:12',
            'tahun' => 'required|integer|min:2020',
            'meter_awal' => 'required|integer|min:0',
            'meter_akhir' => 'required|integer|min:0|gt:meter_awal'
        ]);

        // Check if usage already exists for this customer in this month/year (except current record)
        $existingUsage = Penggunaan::where('id_pelanggan', $request->id_pelanggan)
            ->where('bulan', $request->bulan)
            ->where('tahun', $request->tahun)
            ->where('id_penggunaan', '!=', $id)
            ->first();

        if ($existingUsage) {
            return back()->withErrors(['error' => 'Data penggunaan untuk pelanggan ini pada bulan/tahun tersebut sudah ada']);
        }

        $penggunaan->update([
            'id_pelanggan' => $request->id_pelanggan,
            'bulan' => $request->bulan,
            'tahun' => $request->tahun,
            'meter_awal' => $request->meter_awal,
            'meter_akhir' => $request->meter_akhir
        ]);

        // Update related tagihan
        $this->updateTagihan($penggunaan);

        return redirect()->route('penggunaan.index')
            ->with('success', 'Data penggunaan berhasil diperbarui');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $penggunaan = Penggunaan::findOrFail($id);

        // Delete related tagihan first
        $penggunaan->tagihans()->delete();

        $penggunaan->delete();

        return redirect()->route('penggunaan.index')
            ->with('success', 'Data penggunaan berhasil dihapus');
    }

    private function generateTagihan($penggunaan)
    {
        $jumlahMeter = $penggunaan->meter_akhir - $penggunaan->meter_awal;

        Tagihan::create([
            'id_penggunaan' => $penggunaan->id_penggunaan,
            'id_pelanggan' => $penggunaan->id_pelanggan,
            'bulan' => $penggunaan->bulan,
            'tahun' => $penggunaan->tahun,
            'jumlah_meter' => $jumlahMeter,
            'status' => 'belum_lunas'
        ]);
    }

    private function updateTagihan($penggunaan)
    {
        $tagihan = Tagihan::where('id_penggunaan', $penggunaan->id_penggunaan)->first();

        if ($tagihan) {
            $jumlahMeter = $penggunaan->meter_akhir - $penggunaan->meter_awal;

            $tagihan->update([
                'bulan' => $penggunaan->bulan,
                'tahun' => $penggunaan->tahun,
                'jumlah_meter' => $jumlahMeter
            ]);
        }
    }
}
