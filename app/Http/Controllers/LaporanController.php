<?php

namespace App\Http\Controllers;

use App\Models\Pembayaran;
use App\Models\Tagihan;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\StreamedResponse;

class LaporanController extends Controller
{
    public function index(Request $request)
    {
        $tanggalMulai = $request->input('tanggal_mulai', now()->startOfMonth()->toDateString());
        $tanggalSelesai = $request->input('tanggal_selesai', now()->toDateString());

        $pembayarans = Pembayaran::with(['tagihan.pelanggan.tarif', 'user'])
            ->where('status', 'berhasil')
            ->whereBetween('tanggal_pembayaran', [$tanggalMulai, $tanggalSelesai])
            ->orderByDesc('tanggal_pembayaran')
            ->orderByDesc('id_pembayaran')
            ->get();

        $ringkasan = [
            'jumlah_transaksi' => $pembayarans->count(),
            'total_pokok' => $pembayarans->sum(fn($p) => (float) ($p->tagihan?->total_tagihan ?? 0)),
            'total_denda' => $pembayarans->sum(fn($p) => (float) ($p->tagihan?->denda ?? 0)),
            'total_admin' => $pembayarans->sum(fn($p) => (float) $p->biaya_admin),
            'grand_total' => $pembayarans->sum(fn($p) => (float) $p->total_bayar),
        ];

        $tunggakans = Tagihan::with('pelanggan.tarif')
            ->where('status', 'belum_lunas')
            ->orderBy('tahun')
            ->orderBy('bulan')
            ->get();

        $totalTunggakanPokok = $tunggakans->sum(fn($t) => (float) $t->total_tagihan);
        $totalEstimasiDenda = $tunggakans->sum(fn($t) => $t->estimasiDenda());

        return view(
            'admin.laporan.index',
            compact(
                'tanggalMulai',
                'tanggalSelesai',
                'pembayarans',
                'ringkasan',
                'tunggakans',
                'totalTunggakanPokok',
                'totalEstimasiDenda',
            ),
        );
    }

    public function exportCsv(Request $request): StreamedResponse
    {
        $tanggalMulai = $request->input('tanggal_mulai', now()->startOfMonth()->toDateString());
        $tanggalSelesai = $request->input('tanggal_selesai', now()->toDateString());

        $pembayarans = Pembayaran::with(['tagihan.pelanggan', 'user'])
            ->where('status', 'berhasil')
            ->whereBetween('tanggal_pembayaran', [$tanggalMulai, $tanggalSelesai])
            ->orderBy('tanggal_pembayaran')
            ->get();

        $filename = "laporan-voltpay-{$tanggalMulai}-sd-{$tanggalSelesai}.csv";

        return response()->streamDownload(
            function () use ($pembayarans) {
                $handle = fopen('php://output', 'w');
                // BOM UTF-8 agar karakter dan pemisah kolom rapi di Excel
                fwrite($handle, "\xEF\xBB\xBF");

                fputcsv(
                    $handle,
                    [
                        'No Referensi',
                        'Tanggal Bayar',
                        'Nomor KWH',
                        'Nama Pelanggan',
                        'Periode Tagihan',
                        'Tagihan Pokok (Rp)',
                        'Denda (Rp)',
                        'Biaya Admin (Rp)',
                        'Total Bayar (Rp)',
                        'Petugas Loket',
                    ],
                    ';',
                );

                foreach ($pembayarans as $p) {
                    fputcsv(
                        $handle,
                        [
                            'VP-' . str_pad($p->id_pembayaran, 6, '0', STR_PAD_LEFT),
                            Carbon::parse($p->tanggal_pembayaran)->format('Y-m-d'),
                            $p->tagihan?->pelanggan?->nomor_kwh ?? '-',
                            $p->tagihan?->pelanggan?->nama_pelanggan ?? '-',
                            $p->bulan_bayar,
                            (float) ($p->tagihan?->total_tagihan ?? 0),
                            (float) ($p->tagihan?->denda ?? 0),
                            (float) $p->biaya_admin,
                            (float) $p->total_bayar,
                            $p->user?->nama_admin ?? '-',
                        ],
                        ';',
                    );
                }

                fclose($handle);
            },
            $filename,
            [
                'Content-Type' => 'text/csv; charset=UTF-8',
            ],
        );
    }
}
