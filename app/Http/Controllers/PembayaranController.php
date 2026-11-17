<?php

namespace App\Http\Controllers;

use App\Models\Pembayaran;
use App\Models\Tagihan;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class PembayaranController extends Controller
{
    public function index(Request $request)
    {
        // Hanya pembayaran yang masih berlaku. Yang dibatalkan terlihat di detail tagihan.
        $query = Pembayaran::aktif()->with('tagihan', 'pelanggan', 'user');

        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->whereHas('pelanggan', function ($q) use ($search) {
                $q->where('nama_pelanggan', 'like', '%' . $search . '%');
            });
        }

        if ($request->filled('tanggal')) {
            $tanggal = Carbon::parse($request->input('tanggal'))->format('Y-m-d');
            $query->whereDate('tanggal_pembayaran', $tanggal);
        }

        $pembayarans = $query->latest()->paginate(10)->withQueryString();

        $today = Carbon::today();

        $jumlahPembayaranHariIni = Pembayaran::aktif()->whereDate('tanggal_pembayaran', $today)->count();
        $totalNominalHariIni = Pembayaran::aktif()->whereDate('tanggal_pembayaran', $today)->sum('total_bayar');

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

        return DB::transaction(function () use ($request) {
            // Kunci baris tagihan agar dua petugas tidak melunasi tagihan yang sama bersamaan.
            $tagihan = Tagihan::where('id_tagihan', $request->id_tagihan)
                ->lockForUpdate()
                ->firstOrFail();

            if ($tagihan->isLunas()) {
                return back()->withInput()->withErrors(['error' => 'Tagihan ini sudah lunas.']);
            }

            // total_tagihan adalah snapshot, tidak terpengaruh perubahan tarif.
            $totalBayar = $tagihan->total_tagihan + $request->biaya_admin;

            Pembayaran::create([
                'id_tagihan' => $tagihan->id_tagihan,
                'id_pelanggan' => $tagihan->id_pelanggan,
                'tanggal_pembayaran' => $request->tanggal_pembayaran,
                'bulan_bayar' => $this->getNamaBulan($tagihan->bulan) . ' ' . $tagihan->tahun,
                'biaya_admin' => $request->biaya_admin,
                'total_bayar' => $totalBayar,
                'status' => 'berhasil',
                'id_user' => Auth::id()
            ]);

            $tanggalBayar = Carbon::parse($request->tanggal_pembayaran);
            $denda = $tagihan->estimasiDenda($tanggalBayar);
            $totalBayar = $tagihan->total_tagihan + $denda + $request->biaya_admin;

            $tagihan->update([
            'status' => 'lunas',
            'denda' => $denda,
        ]);

            return redirect()->route('pembayaran.index')
                ->with('success', 'Pembayaran berhasil diproses');
        });
    }

    public function show(string $id)
    {
        $pembayaran = Pembayaran::with('tagihan', 'pelanggan', 'user', 'userPembatal')->findOrFail($id);
        return view('admin.pembayaran.show', compact('pembayaran'));
    }

    public function edit(string $id)
    {
        $pembayaran = Pembayaran::with('tagihan', 'pelanggan')->findOrFail($id);

        if ($pembayaran->isBatal()) {
            return redirect()->route('pembayaran.show', $pembayaran->id_pembayaran)
                ->with('error', 'Pembayaran yang sudah dibatalkan tidak bisa diubah.');
        }

        return view('admin.pembayaran.edit', compact('pembayaran'));
    }

    public function update(Request $request, string $id)
    {
        $request->validate([
            'tanggal_pembayaran' => 'required|date',
            'biaya_admin' => 'required|numeric|min:0',
        ]);

        return DB::transaction(function () use ($request, $id) {
            $pembayaran = Pembayaran::lockForUpdate()->findOrFail($id);

            if ($pembayaran->isBatal()) {
                return redirect()->route('pembayaran.show', $pembayaran->id_pembayaran)
                    ->with('error', 'Pembayaran yang sudah dibatalkan tidak bisa diubah.');
            }

            $tanggalBayar = Carbon::parse($request->tanggal_pembayaran);
            $denda = $pembayaran->tagihan->estimasiDenda($tanggalBayar);
            $totalBayar = $pembayaran->tagihan->total_tagihan + $denda + $request->biaya_admin;

            $pembayaran->update([
                'tanggal_pembayaran' => $request->tanggal_pembayaran,
                'biaya_admin' => $request->biaya_admin,
                'total_bayar' => $totalBayar
            ]);

            $pembayaran->tagihan->update(['denda' => $denda]);

            return redirect()->route('pembayaran.index')
                ->with('success', 'Data pembayaran berhasil diperbarui');
        });
    }

    /**
     * Membatalkan pembayaran (bukan menghapus): barisnya tetap ada sebagai jejak audit.
     * Route DELETE yang sama dipakai; kirim field "alasan" dari form bila ada.
     */
    public function destroy(Request $request, string $id)
    {
        $request->validate([
            'alasan' => 'nullable|string|max:255',
        ]);

        return DB::transaction(function () use ($request, $id) {
            $pembayaran = Pembayaran::lockForUpdate()->findOrFail($id);

            if ($pembayaran->isBatal()) {
                return back()->with('error', 'Pembayaran ini sudah dibatalkan sebelumnya.');
            }

            $tagihan = Tagihan::lockForUpdate()->findOrFail($pembayaran->id_tagihan);

            $pembayaran->update([
                'status' => 'batal',
                'dibatalkan_pada' => now(),
                'alasan_batal' => $request->input('alasan') ?: 'Dibatalkan oleh petugas',
                'id_user_batal' => Auth::id(),
            ]);

            $tagihan->update([
                'status' => 'belum_lunas',
                'denda' => 0,
            ]);

            return redirect()->route('pembayaran.index')
                ->with('success', 'Pembayaran dibatalkan. Tagihan kembali berstatus belum lunas.');
        });
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
