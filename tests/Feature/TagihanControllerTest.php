<?php

namespace App\Http\Controllers;

use App\Models\Pelanggan;
use App\Models\Tagihan;
use Illuminate\Database\UniqueConstraintViolationException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

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

        if ($request->filled('status')) {
            $query->where('status', $request->input('status'));
        }

        if ($request->filled('bulan')) {
            $query->where('bulan', $request->input('bulan'));
        }

        $tagihans = $query->orderBy('tahun', 'desc')
            ->orderBy('bulan', 'desc')
            ->paginate(10)
            ->appends($request->query());

        return view('admin.tagihan.index', compact('tagihans'));
    }

    /**
     * Form tagihan manual (tanpa data penggunaan).
     * Tagihan yang berasal dari meter dibuat otomatis oleh PenggunaanController.
     */
    public function create()
    {
        $pelanggans = Pelanggan::orderBy('nama_pelanggan')->get();
        return view('admin.tagihan.create', compact('pelanggans'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'id_pelanggan' => 'required|exists:pelanggans,id_pelanggan',
            'bulan' => 'required|integer|min:1|max:12',
            'tahun' => 'required|integer|min:2020',
            'jumlah_meter' => 'required|integer|min:0',
        ]); // <-- Pastikan kurung tutup validate() berhenti di sini, jangan ada 'denda' => 0 di dalamnya

        $pesanDuplikat = 'Tagihan untuk pelanggan ini pada bulan/tahun tersebut sudah ada';

        try {
            return DB::transaction(function () use ($request, $pesanDuplikat) {
                $pelanggan = Pelanggan::with('tarif')->findOrFail($request->id_pelanggan);

                $sudahAda = Tagihan::where('id_pelanggan', $pelanggan->id_pelanggan)
                    ->where('bulan', $request->bulan)
                    ->where('tahun', $request->tahun)
                    ->exists();

                if ($sudahAda) {
                    return back()->withInput()->withErrors(['error' => $pesanDuplikat]);
                }

                $tagihan = new Tagihan([
                    'id_penggunaan' => null,
                    'id_pelanggan' => $pelanggan->id_pelanggan,
                    'bulan' => $request->bulan,
                    'tahun' => $request->tahun,
                    'jumlah_meter' => $request->jumlah_meter,
                    'tarif_per_kwh' => $pelanggan->tarif->tarif_per_kwh,
                    'tanggal_jatuh_tempo' => Tagihan::hitungTanggalJatuhTempo((int) $request->bulan, (int) $request->tahun),
                    'denda' => 0,
                    'status' => 'belum_lunas',
                ]);
                $tagihan->hitungTotal();
                $tagihan->save();

                return redirect()->route('tagihan.index')
                    ->with('success', 'Tagihan berhasil ditambahkan');
            });
        } catch (UniqueConstraintViolationException $e) {
            return back()->withInput()->withErrors(['error' => $pesanDuplikat]);
        }
    }

    public function show(string $id)
    {
        $tagihan = Tagihan::with('pelanggan.tarif', 'penggunaan', 'pembayarans.user')->findOrFail($id);
        return view('admin.tagihan.show', compact('tagihan'));
    }

    public function edit(string $id)
    {
        $tagihan = Tagihan::with('pelanggan.tarif')->findOrFail($id);

        if ($alasan = $this->alasanTerkunci($tagihan)) {
            return redirect()->route('tagihan.show', $tagihan->id_tagihan)->with('error', $alasan);
        }

        return view('admin.tagihan.edit', compact('tagihan'));
    }

    public function update(Request $request, string $id)
    {
        $request->validate([
            'jumlah_meter' => 'required|integer|min:0',
        ]);

        return DB::transaction(function () use ($request, $id) {
            $tagihan = Tagihan::lockForUpdate()->findOrFail($id);

            if ($alasan = $this->alasanTerkunci($tagihan)) {
                return redirect()->route('tagihan.show', $tagihan->id_tagihan)->with('error', $alasan);
            }

            $tagihan->jumlah_meter = $request->jumlah_meter;
            $tagihan->hitungTotal(); // tarif tetap memakai snapshot tagihan
            $tagihan->save();

            return redirect()->route('tagihan.index')
                ->with('success', 'Tagihan berhasil diperbarui');
        });
    }

    public function destroy(string $id)
    {
        return DB::transaction(function () use ($id) {
            $tagihan = Tagihan::lockForUpdate()->findOrFail($id);

            if ($alasan = $this->alasanTerkunci($tagihan)) {
                return back()->with('error', $alasan);
            }

            if ($tagihan->pembayarans()->exists()) {
                return back()->with('error', 'Tagihan ini punya riwayat pembayaran (termasuk yang dibatalkan), jadi tidak bisa dihapus.');
            }

            $tagihan->delete();

            return redirect()->route('tagihan.index')
                ->with('success', 'Tagihan berhasil dihapus');
        });
    }

    /**
     * Mengembalikan alasan bila tagihan tidak boleh diubah/dihapus dari menu Tagihan.
     */
    private function alasanTerkunci(Tagihan $tagihan): ?string
    {
        if ($tagihan->isLunas()) {
            return 'Tagihan yang sudah lunas dikunci. Batalkan pembayarannya lebih dulu bila ada kesalahan.';
        }

        if (! $tagihan->isManual()) {
            return 'Tagihan ini dibuat dari data penggunaan. Ubah atau hapus lewat menu Penggunaan.';
        }

        return null;
    }
}
