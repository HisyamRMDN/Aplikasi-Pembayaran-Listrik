<?php

namespace App\Http\Controllers;

use App\Models\Pelanggan;
use App\Models\Pembayaran;
use App\Models\Penggunaan;
use App\Models\Tagihan;
use Illuminate\Database\UniqueConstraintViolationException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PenggunaanController extends Controller
{
    private const PESAN_DUPLIKAT = 'Data penggunaan untuk pelanggan ini pada bulan/tahun tersebut sudah ada';
    private const PESAN_TERKUNCI = 'Data penggunaan terkunci karena tagihannya sudah lunas. Batalkan pembayarannya lebih dulu bila ada kesalahan.';

    public function index(Request $request)
    {
        $query = Penggunaan::with('pelanggan', 'tagihans');

        if ($request->filled('search')) {
            $search = $request->search;
            $query->whereHas('pelanggan', function ($q) use ($search) {
                $q->where('nama_pelanggan', 'like', "%{$search}%")->orWhere('nomor_kwh', 'like', "%{$search}%");
            });
        }

        if ($request->filled('bulan')) {
            $query->where('bulan', $request->bulan);
        }

        if ($request->filled('tahun')) {
            $query->where('tahun', $request->tahun);
        }

        $penggunaans = $query
            ->orderBy('tahun', 'desc')
            ->orderBy('bulan', 'desc')
            ->orderBy('id_penggunaan', 'desc')
            ->paginate(10)
            ->appends($request->all());

        return view('admin.penggunaan.index', compact('penggunaans'));
    }

    /**
     * Endpoint JSON: Ambil meter akhir terakhir pelanggan sebelum periode target.
     */
    public function getMeterTerakhir(Request $request)
    {
        $request->validate([
            'id_pelanggan' => 'required|exists:pelanggans,id_pelanggan',
            'bulan' => 'nullable|integer|min:1|max:12',
            'tahun' => 'nullable|integer|min:2020',
        ]);

        $terakhir = $this->cariPenggunaanSebelumnya(
            (int) $request->id_pelanggan,
            $request->bulan ? (int) $request->bulan : null,
            $request->tahun ? (int) $request->tahun : null,
        );

        return response()->json([
            'ada_riwayat' => $terakhir !== null,
            'meter_awal' => $terakhir ? (int) $terakhir->meter_akhir : 0,
            'periode_sebelumnya' => $terakhir
                ? str_pad($terakhir->bulan, 2, '0', STR_PAD_LEFT) . '/' . $terakhir->tahun
                : null,
        ]);
    }

    public function create()
    {
        $pelanggans = Pelanggan::orderBy('nama_pelanggan')->get();
        return view('admin.penggunaan.create', compact('pelanggans'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'id_pelanggan' => 'required|exists:pelanggans,id_pelanggan',
            'bulan' => 'required|integer|min:1|max:12',
            'tahun' => 'required|integer|min:2020',
            'meter_awal' => 'required|integer|min:0',
            'meter_akhir' => 'required|integer|min:0|gt:meter_awal',
        ]);

        try {
            return DB::transaction(function () use ($request) {
                $sudahAda = Penggunaan::where('id_pelanggan', $request->id_pelanggan)
                    ->where('bulan', $request->bulan)
                    ->where('tahun', $request->tahun)
                    ->lockForUpdate()
                    ->exists();

                if ($sudahAda) {
                    return back()
                        ->withInput()
                        ->withErrors(['error' => self::PESAN_DUPLIKAT]);
                }

                // Validasi ulang meter_awal dari catatan terakhir (mencegah bypass readonly)
                $sebelumnya = $this->cariPenggunaanSebelumnya(
                    (int) $request->id_pelanggan,
                    (int) $request->bulan,
                    (int) $request->tahun,
                );

                $meterAwal = $sebelumnya ? (int) $sebelumnya->meter_akhir : (int) $request->meter_awal;

                if ((int) $request->meter_akhir <= $meterAwal) {
                    return back()
                        ->withInput()
                        ->withErrors([
                            'meter_akhir' => "Meter akhir harus lebih besar dari meter awal ({$meterAwal}).",
                        ]);
                }

                $penggunaan = Penggunaan::create([
                    'id_pelanggan' => $request->id_pelanggan,
                    'bulan' => $request->bulan,
                    'tahun' => $request->tahun,
                    'meter_awal' => $meterAwal,
                    'meter_akhir' => $request->meter_akhir,
                ]);

                $this->buatTagihan($penggunaan);

                return redirect()
                    ->route('penggunaan.index')
                    ->with('success', 'Data penggunaan berhasil ditambahkan dan tagihan otomatis dibuat');
            });
        } catch (UniqueConstraintViolationException $e) {
            return back()
                ->withInput()
                ->withErrors(['error' => self::PESAN_DUPLIKAT]);
        }
    }

    /**
     * Halaman Input Meter Massal untuk periode bulan & tahun tertentu.
     */
    public function massal(Request $request)
    {
        $bulan = (int) $request->input('bulan', now()->month);
        $tahun = (int) $request->input('tahun', now()->year);

        // Pelanggan yang sudah dicatat pada bulan/tahun ini
        $sudahDicatatIds = Penggunaan::where('bulan', $bulan)->where('tahun', $tahun)->pluck('id_pelanggan');

        // Pelanggan yang belum dicatat beserta riwayat penggunaan sebelumnya
        $daftarPelanggan = Pelanggan::with('tarif')
            ->whereNotIn('id_pelanggan', $sudahDicatatIds)
            ->orderBy('nama_pelanggan')
            ->get()
            ->map(function ($pelanggan) use ($bulan, $tahun) {
                $sebelumnya = $this->cariPenggunaanSebelumnya($pelanggan->id_pelanggan, $bulan, $tahun);
                $pelanggan->meter_awal_otomatis = $sebelumnya ? (int) $sebelumnya->meter_akhir : 0;
                $pelanggan->ada_riwayat_meter = $sebelumnya !== null;
                $pelanggan->periode_sebelumnya = $sebelumnya
                    ? str_pad($sebelumnya->bulan, 2, '0', STR_PAD_LEFT) . '/' . $sebelumnya->tahun
                    : null;

                return $pelanggan;
            });

        $jumlahSudahDicatat = $sudahDicatatIds->count();

        return view('admin.penggunaan.massal', compact('daftarPelanggan', 'bulan', 'tahun', 'jumlahSudahDicatat'));
    }

    /**
     * Simpan seluruh baris meter akhir yang diisi pada form massal.
     */
    public function storeMassal(Request $request)
    {
        $request->validate([
            'bulan' => 'required|integer|min:1|max:12',
            'tahun' => 'required|integer|min:2020',
            'rows' => 'required|array',
            'rows.*.meter_awal' => 'required|integer|min:0',
            'rows.*.meter_akhir' => 'nullable|integer|min:0',
        ]);

        $bulan = (int) $request->bulan;
        $tahun = (int) $request->tahun;

        // Filter hanya baris yang meter_akhir-nya diisi petugas
        $barisDiisi = collect($request->rows)->filter(
            fn($row) => isset($row['meter_akhir']) && $row['meter_akhir'] !== '' && $row['meter_akhir'] !== null,
        );

        if ($barisDiisi->isEmpty()) {
            return back()->withInput()->with('error', 'Tidak ada angka meter akhir yang diisi.');
        }

        // Validasi meter_akhir > meter_awal untuk setiap baris yang diisi
        $errors = [];
        foreach ($barisDiisi as $idPelanggan => $row) {
            $sebelumnya = $this->cariPenggunaanSebelumnya((int) $idPelanggan, $bulan, $tahun);
            $meterAwal = $sebelumnya ? (int) $sebelumnya->meter_akhir : (int) $row['meter_awal'];
            $meterAkhir = (int) $row['meter_akhir'];

            if ($meterAkhir <= $meterAwal) {
                $pelanggan = Pelanggan::find($idPelanggan);
                $nama = $pelanggan ? $pelanggan->nama_pelanggan : "ID #{$idPelanggan}";
                $errors[] = "{$nama}: Meter akhir ({$meterAkhir}) harus lebih besar dari meter awal ({$meterAwal}).";
            }
        }

        if (!empty($errors)) {
            return back()->withInput()->withErrors($errors);
        }

        try {
            $jumlahTersimpan = DB::transaction(function () use ($barisDiisi, $bulan, $tahun) {
                $count = 0;

                foreach ($barisDiisi as $idPelanggan => $row) {
                    $idPelanggan = (int) $idPelanggan;

                    $sudahAda = Penggunaan::where('id_pelanggan', $idPelanggan)
                        ->where('bulan', $bulan)
                        ->where('tahun', $tahun)
                        ->lockForUpdate()
                        ->exists();

                    if ($sudahAda) {
                        continue;
                    }

                    $sebelumnya = $this->cariPenggunaanSebelumnya($idPelanggan, $bulan, $tahun);
                    $meterAwal = $sebelumnya ? (int) $sebelumnya->meter_akhir : (int) $row['meter_awal'];

                    $penggunaan = Penggunaan::create([
                        'id_pelanggan' => $idPelanggan,
                        'bulan' => $bulan,
                        'tahun' => $tahun,
                        'meter_awal' => $meterAwal,
                        'meter_akhir' => (int) $row['meter_akhir'],
                    ]);

                    $this->buatTagihan($penggunaan);
                    $count++;
                }

                return $count;
            });

            return redirect()
                ->route('penggunaan.index', ['bulan' => $bulan, 'tahun' => $tahun])
                ->with(
                    'success',
                    "{$jumlahTersimpan} catatan penggunaan berhasil disimpan dan tagihannya otomatis diterbitkan.",
                );
        } catch (UniqueConstraintViolationException $e) {
            return back()
                ->withInput()
                ->with('error', 'Terjadi bentrok data ganda saat menyimpan massal. Silakan muat ulang halaman.');
        }
    }

    public function show(string $id)
    {
        $penggunaan = Penggunaan::with('pelanggan.tarif', 'tagihans')->findOrFail($id);
        return view('admin.penggunaan.show', compact('penggunaan'));
    }

    public function edit(string $id)
    {
        $penggunaan = Penggunaan::with('tagihans')->findOrFail($id);

        if ($penggunaan->terkunci()) {
            return redirect()
                ->route('penggunaan.show', $penggunaan->id_penggunaan)
                ->with('error', self::PESAN_TERKUNCI);
        }

        $pelanggans = Pelanggan::orderBy('nama_pelanggan')->get();
        return view('admin.penggunaan.edit', compact('penggunaan', 'pelanggans'));
    }

    public function update(Request $request, string $id)
    {
        $penggunaan = Penggunaan::with('tagihans')->findOrFail($id);

        if ($penggunaan->tagihan && $penggunaan->tagihan->isLunas()) {
            return redirect()
                ->route('penggunaan.index')
                ->with('error', 'Data penggunaan tidak dapat diubah karena tagihan periode ini sudah lunas.');
        }

        $validated = $request->validate(
            [
                'meter_awal' => 'required|integer|min:0',
                'meter_akhir' => 'required|integer|gt:meter_awal',
            ],
            [
                'meter_akhir.gt' => 'Meter akhir harus lebih besar dari meter awal.',
            ],
        );

        // Cari periode tepat sebelum bulan & tahun penggunaan yang sedang diedit
        $periodeSaatIni = $penggunaan->tahun * 100 + $penggunaan->bulan;

        $penggunaanSebelumnya = Penggunaan::where('id_pelanggan', $penggunaan->id_pelanggan)
            ->where('id_penggunaan', '!=', $penggunaan->id_penggunaan)
            ->whereRaw('(tahun * 100 + bulan) < ?', [$periodeSaatIni])
            ->orderByDesc('tahun')
            ->orderByDesc('bulan')
            ->first();

        if ($penggunaanSebelumnya && (int) $validated['meter_awal'] < (int) $penggunaanSebelumnya->meter_akhir) {
            return back()
                ->withInput()
                ->withErrors([
                    'meter_awal' => "Meter awal tidak boleh lebih kecil dari meter akhir periode sebelumnya ({$penggunaanSebelumnya->meter_akhir} kWh).",
                ]);
        }

        $penggunaan->update($validated);

        // Sinkronkan jumlah_meter & total_tagihan jika tagihan terkait belum lunas
        if ($penggunaan->tagihan && !$penggunaan->tagihan->isLunas()) {
            $jumlahMeter = $validated['meter_akhir'] - $validated['meter_awal'];
            $tarifPerKwh = (float) $penggunaan->tagihan->tarif_per_kwh;

            $penggunaan->tagihan->update([
                'jumlah_meter' => $jumlahMeter,
                'total_tagihan' => $jumlahMeter * $tarifPerKwh,
            ]);
        }

        return redirect()->route('penggunaan.index')->with('success', 'Data penggunaan listrik berhasil diperbarui.');
    }

    public function destroy(string $id)
    {
        return DB::transaction(function () use ($id) {
            $penggunaan = Penggunaan::lockForUpdate()->findOrFail($id);

            $tagihans = Tagihan::where('id_penggunaan', $penggunaan->id_penggunaan)->lockForUpdate()->get();

            if ($tagihans->contains(fn($t) => $t->isLunas())) {
                return back()->with('error', self::PESAN_TERKUNCI);
            }

            if (Pembayaran::whereIn('id_tagihan', $tagihans->pluck('id_tagihan'))->exists()) {
                return back()->with(
                    'error',
                    'Data ini punya riwayat pembayaran (termasuk yang dibatalkan), jadi tidak bisa dihapus.',
                );
            }

            $penggunaan->tagihans()->delete();
            $penggunaan->delete();

            return redirect()->route('penggunaan.index')->with('success', 'Data penggunaan berhasil dihapus');
        });
    }

    /**
     * Cari catatan penggunaan terakhir sebelum periode (tahun, bulan) target.
     */
    private function cariPenggunaanSebelumnya(int $idPelanggan, ?int $bulan = null, ?int $tahun = null): ?Penggunaan
    {
        $query = Penggunaan::where('id_pelanggan', $idPelanggan);

        if ($tahun && $bulan) {
            $query->where(function ($q) use ($tahun, $bulan) {
                $q->where('tahun', '<', $tahun)->orWhere(function ($q2) use ($tahun, $bulan) {
                    $q2->where('tahun', $tahun)->where('bulan', '<', $bulan);
                });
            });
        }

        return $query->orderBy('tahun', 'desc')->orderBy('bulan', 'desc')->first();
    }

    private function buatTagihan(Penggunaan $penggunaan): void
    {
        $pelanggan = Pelanggan::with('tarif')->findOrFail($penggunaan->id_pelanggan);

        $tagihan = new Tagihan([
            'id_penggunaan' => $penggunaan->id_penggunaan,
            'id_pelanggan' => $penggunaan->id_pelanggan,
            'bulan' => $penggunaan->bulan,
            'tahun' => $penggunaan->tahun,
            'jumlah_meter' => $penggunaan->jumlah_meter,
            'tarif_per_kwh' => $pelanggan->tarif->tarif_per_kwh,
            'status' => 'belum_lunas',
            'tanggal_jatuh_tempo' => Tagihan::hitungTanggalJatuhTempo(
                (int) $penggunaan->bulan,
                (int) $penggunaan->tahun,
            ),
            'denda' => 0,
        ]);
        $tagihan->hitungTotal();
        $tagihan->save();
    }

    private function sinkronkanTagihan(Tagihan $tagihan, Penggunaan $penggunaan): void
    {
        if ((int) $tagihan->id_pelanggan !== (int) $penggunaan->id_pelanggan) {
            $pelanggan = Pelanggan::with('tarif')->findOrFail($penggunaan->id_pelanggan);
            $tagihan->id_pelanggan = $pelanggan->id_pelanggan;
            $tagihan->tarif_per_kwh = $pelanggan->tarif->tarif_per_kwh;
        }

        $tagihan->bulan = $penggunaan->bulan;
        $tagihan->tahun = $penggunaan->tahun;
        $tagihan->jumlah_meter = $penggunaan->jumlah_meter;
        $tagihan->tanggal_jatuh_tempo = Tagihan::hitungTanggalJatuhTempo(
            (int) $penggunaan->bulan,
            (int) $penggunaan->tahun,
        );
        $tagihan->hitungTotal();
        $tagihan->save();
    }
}
