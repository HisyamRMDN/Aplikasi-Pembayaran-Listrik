<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Pembayaran;
use App\Models\Tagihan;
use App\Models\Pelanggan;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class PembayaranController extends Controller
{
    public function index(Request $request)
    {
        $query = Pembayaran::with('tagihan', 'pelanggan', 'user');

        // Fitur pencarian berdasarkan nama pelanggan
        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->whereHas('pelanggan', function ($q) use ($search) {
                $q->where('nama_pelanggan', 'like', '%' . $search . '%');
            });
        }

        // Fitur filter berdasarkan tanggal pembayaran
        if ($request->filled('tanggal')) {
            $tanggal = Carbon::parse($request->input('tanggal'))->format('Y-m-d');
            $query->whereDate('tanggal_pembayaran', $tanggal);
        }

        $pembayarans = $query->latest()->paginate(10)->withQueryString(); // withQueryString agar pagination bawa query

        $today = Carbon::today();

        $jumlahPembayaranHariIni = Pembayaran::whereDate('tanggal_pembayaran', $today)->count();
        $totalNominalHariIni = Pembayaran::whereDate('tanggal_pembayaran', $today)->sum('total_bayar');

        return view('admin.pembayaran.index', compact('pembayarans', 'jumlahPembayaranHariIni', 'totalNominalHariIni'));
    }


    public function create()
    {
        $tagihans = Tagihan::with('pelanggan')
            ->where('status', 'belum_lunas')
            ->get();
        return view('admin.pembayaran.create', compact('tagihans'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'id_tagihan' => 'required|exists:tagihans,id_tagihan',
            'tanggal_pembayaran' => 'required|date',
            'biaya_admin' => 'required|numeric|min:0',
        ]);

        $tagihan = Tagihan::with('pelanggan')->findOrFail($request->id_tagihan);

        // Check if tagihan is already paid
        if ($tagihan->status === 'lunas') {
            return back()->withErrors(['error' => 'Tagihan sudah lunas']);
        }

        // Calculate total payment
        $totalTagihan = $tagihan->total_tagihan;
        $totalBayar = $totalTagihan + $request->biaya_admin;

        // Create payment record
        $pembayaran = Pembayaran::create([
            'id_tagihan' => $request->id_tagihan,
            'id_pelanggan' => $tagihan->id_pelanggan,
            'tanggal_pembayaran' => $request->tanggal_pembayaran,
            'bulan_bayar' => $this->getNamaBulan($tagihan->bulan) . ' ' . $tagihan->tahun,
            'biaya_admin' => $request->biaya_admin,
            'total_bayar' => $totalBayar,
            'id_user' => Auth::id()
        ]);

        // Update tagihan status
        $tagihan->update(['status' => 'lunas']);

        return redirect()->route('pembayaran.index')
            ->with('success', 'Pembayaran berhasil diproses');
    }

    public function show(string $id)
    {
        $pembayaran = Pembayaran::with('tagihan', 'pelanggan', 'user')->findOrFail($id);
        return view('admin.pembayaran.show', compact('pembayaran'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $pembayaran = Pembayaran::with('tagihan', 'pelanggan')->findOrFail($id);
        return view('admin.pembayaran.edit', compact('pembayaran'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $pembayaran = Pembayaran::findOrFail($id);

        $request->validate([
            'tanggal_pembayaran' => 'required|date',
            'biaya_admin' => 'required|numeric|min:0',
        ]);

        // Recalculate total payment
        $tagihan = $pembayaran->tagihan;
        $totalTagihan = $tagihan->total_tagihan;
        $totalBayar = $totalTagihan + $request->biaya_admin;

        $pembayaran->update([
            'tanggal_pembayaran' => $request->tanggal_pembayaran,
            'biaya_admin' => $request->biaya_admin,
            'total_bayar' => $totalBayar
        ]);

        return redirect()->route('pembayaran.index')
            ->with('success', 'Data pembayaran berhasil diperbarui');
    }

    public function destroy(string $id)
    {
        $pembayaran = Pembayaran::findOrFail($id);

        // Update tagihan status back to belum_lunas
        $tagihan = $pembayaran->tagihan;
        $tagihan->update(['status' => 'belum_lunas']);

        $pembayaran->delete();

        return redirect()->route('pembayaran.index')
            ->with('success', 'Pembayaran berhasil dibatalkan');
    }

    // For customer view
    public function pelangganTagihan()
    {
        $pelanggan = Auth::guard('pelanggan')->user();
        $tagihans = Tagihan::with(['penggunaan', 'pelanggan.tarif'])
            ->where('id_pelanggan', $pelanggan->id_pelanggan)
            ->orderBy('tahun', 'desc')
            ->orderBy('bulan', 'desc')
            ->get();

        return view('pelanggan.tagihan', compact('tagihans'));
    }

    private function getNamaBulan($bulan)
    {
        $namaBulan = [
            1 => 'Januari',
            2 => 'Februari',
            3 => 'Maret',
            4 => 'April',
            5 => 'Mei',
            6 => 'Juni',
            7 => 'Juli',
            8 => 'Agustus',
            9 => 'September',
            10 => 'Oktober',
            11 => 'November',
            12 => 'Desember'
        ];

        return $namaBulan[$bulan] ?? 'Tidak Diketahui';
    }
}
