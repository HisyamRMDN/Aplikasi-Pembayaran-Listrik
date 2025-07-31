<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
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

        return view('dashboard.admin', compact(
            'totalPelanggan',
            'totalPenggunaan',
            'totalTagihan',
            'totalPembayaran',
            'tagihanBelumLunas'
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

        return view('dashboard.pelanggan', compact(
            'pelanggan',
            'penggunaan',
            'tagihan',
            'tagihanBelumLunas'
        ));
    }
}
