<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Pelanggan;
use App\Models\Penggunaan;
use App\Models\Tagihan;
use App\Models\Pembayaran;

class DashboardController extends Controller
{
    public function index()
    {
        $totalPelanggan = Pelanggan::count();
        $totalPenggunaan = Penggunaan::count();
        $totalTagihan = Tagihan::count();
        $totalPembayaran = Pembayaran::count();
        $tagihanBelumLunas = Tagihan::where('status', 'belum_lunas')->count();

        $tagihanTerbaru = Tagihan::with(['pelanggan.tarif'])
            ->orderByDesc('tahun')
            ->orderByDesc('bulan')
            ->orderByDesc('id_tagihan')
            ->limit(6)
            ->get();

        return view('dashboard.admin', compact(
            'totalPelanggan',
            'totalPenggunaan',
            'totalTagihan',
            'totalPembayaran',
            'tagihanBelumLunas',
            'tagihanTerbaru'
        ));
    }

    public function pelangganDashboard()
    {
        $pelanggan = Auth::guard('pelanggan')->user();

        $penggunaan = Penggunaan::where('id_pelanggan', $pelanggan->id_pelanggan)
            ->orderBy('tahun', 'desc')
            ->orderBy('bulan', 'desc')
            ->limit(5)
            ->get();

        $tagihan = Tagihan::where('id_pelanggan', $pelanggan->id_pelanggan)
            ->orderBy('tahun', 'desc')
            ->orderBy('bulan', 'desc')
            ->limit(5)
            ->get();

        $tagihanBelumLunas = Tagihan::where('id_pelanggan', $pelanggan->id_pelanggan)
            ->where('status', 'belum_lunas')
            ->count();

        $pembayaranDibatalkan = Pembayaran::with('tagihan')
            ->where('id_pelanggan', $pelanggan->id_pelanggan)
            ->where('status', 'batal')
            ->whereHas('tagihan', function ($q) {
                $q->where('status', 'belum_lunas');
            })
            ->latest('dibatalkan_pada')
            ->get();

        return view('dashboard.pelanggan', compact(
            'pelanggan',
            'penggunaan',
            'tagihan',
            'tagihanBelumLunas',
            'pembayaranDibatalkan'
        ));
    }
}
