<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Tagihan;
use App\Models\Pelanggan;
use App\Models\Penggunaan;

class TagihanController extends Controller
{
    public function index(Request $request)
    {
        $query = Tagihan::with('pelanggan', 'penggunaan');

        // Filter: search (nama pelanggan atau nomor KWH)
        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->whereHas('pelanggan', function ($q) use ($search) {
                $q->where('nama_pelanggan', 'like', '%' . $search . '%')
                    ->orWhere('nomor_kwh', 'like', '%' . $search . '%');
            });
        }

        // Filter: status
        if ($request->filled('status')) {
            $query->where('status', $request->input('status'));
        }

        // Filter: bulan
        if ($request->filled('bulan')) {
            $query->where('bulan', $request->input('bulan'));
        }

        // Pagination + sorting (optional: by tahun desc & bulan desc)
        $tagihans = $query->orderBy('tahun', 'desc')
            ->orderBy('bulan', 'desc')
            ->paginate(10)
            ->appends($request->query()); // penting untuk pagination

        return view('admin.tagihan.index', compact('tagihans'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $pelanggans = Pelanggan::all();
        $penggunaans = Penggunaan::with('pelanggan')->get();
        return view('admin.tagihan.create', compact('pelanggans', 'penggunaans'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'id_pelanggan' => 'required|exists:pelanggans,id_pelanggan',
            'id_penggunaan' => 'nullable|exists:penggunaans,id_penggunaan',
            'bulan' => 'required|integer|min:1|max:12',
            'tahun' => 'required|integer|min:2020',
            'jumlah_meter' => 'required|integer|min:0',
            'status' => 'required|in:belum_lunas,lunas'
        ]);

        // Check if tagihan already exists for this customer in this month/year
        $existingTagihan = Tagihan::where('id_pelanggan', $request->id_pelanggan)
            ->where('bulan', $request->bulan)
            ->where('tahun', $request->tahun)
            ->first();

        if ($existingTagihan) {
            return back()->withErrors(['error' => 'Tagihan untuk pelanggan ini pada bulan/tahun tersebut sudah ada']);
        }

        Tagihan::create([
            'id_penggunaan' => $request->id_penggunaan,
            'id_pelanggan' => $request->id_pelanggan,
            'bulan' => $request->bulan,
            'tahun' => $request->tahun,
            'jumlah_meter' => $request->jumlah_meter,
            'status' => $request->status
        ]);

        return redirect()->route('tagihan.index')
            ->with('success', 'Tagihan berhasil ditambahkan');
    }

    public function show(string $id)
    {
        $tagihan = Tagihan::with('pelanggan', 'penggunaan')->findOrFail($id);
        return view('admin.tagihan.show', compact('tagihan'));
    }

    public function edit(string $id)
    {
        $tagihan = Tagihan::findOrFail($id);
        return view('admin.tagihan.edit', compact('tagihan'));
    }

    public function update(Request $request, string $id)
    {
        $tagihan = Tagihan::findOrFail($id);

        $request->validate([
            'status' => 'required|in:belum_lunas,lunas',
            'jumlah_meter' => 'required|integer|min:0'
        ]);

        $tagihan->update([
            'status' => $request->status,
            'jumlah_meter' => $request->jumlah_meter
        ]);

        return redirect()->route('tagihan.index')
            ->with('success', 'Tagihan berhasil diperbarui');
    }

    public function destroy(string $id)
    {
        $tagihan = Tagihan::findOrFail($id);
        $tagihan->delete();

        return redirect()->route('tagihan.index')
            ->with('success', 'Tagihan berhasil dihapus');
    }
}
