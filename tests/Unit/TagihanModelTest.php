<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Tagihan extends Model
{
    use HasFactory;
    protected $primaryKey = 'id_tagihan';

    protected $fillable = [
        'id_penggunaan',
        'id_pelanggan',
        'bulan',
        'tahun',
        'jumlah_meter',
        'tarif_per_kwh',
        'total_tagihan',
        'status', // <-- tambahkan koma di sini
        'tanggal_jatuh_tempo',
        'denda',
    ];

    protected $casts = [
        'bulan' => 'integer',
        'tahun' => 'integer',
        'jumlah_meter' => 'integer',
        'tarif_per_kwh' => 'decimal:2',
        'total_tagihan' => 'decimal:2',
        'tanggal_jatuh_tempo' => 'date',
        'denda' => 'decimal:2',
    ];

    public function penggunaan()
    {
        return $this->belongsTo(Penggunaan::class, 'id_penggunaan', 'id_penggunaan');
    }

    public function pelanggan()
    {
        return $this->belongsTo(Pelanggan::class, 'id_pelanggan', 'id_pelanggan');
    }

    public function pembayarans()
    {
        return $this->hasMany(Pembayaran::class, 'id_tagihan', 'id_tagihan');
    }

    /**
     * Hitung ulang total dari kWh x tarif yang tersimpan di tagihan ini.
     * Tarif TIDAK diambil dari pelanggan, supaya perubahan tarif
     * tidak mengubah tagihan yang sudah terbit.
     */
    public function hitungTotal(): void
    {
        $this->total_tagihan = round($this->jumlah_meter * (float) $this->tarif_per_kwh, 2);
    }

    public function isLunas(): bool
    {
        return $this->status === 'lunas';
    }

    /** Tagihan manual = tidak berasal dari data penggunaan. */
    public function isManual(): bool
    {
        return $this->id_penggunaan === null;
    }

    /**
     * Hanya tagihan manual yang belum lunas yang boleh diubah/dihapus dari menu Tagihan.
     * Tagihan dari penggunaan dikelola lewat data penggunaan, tagihan lunas dikunci.
     */
    public function bisaDiubah(): bool
    {
        return ! $this->isLunas() && $this->isManual();
    }


    // Tambahkan helper method di dalam class Tagihan:
    public static function hitungTanggalJatuhTempo(int $bulan, int $tahun): string
    {
        $hari = (int) config('voltpay.hari_jatuh_tempo', 20);

        return \Carbon\Carbon::create($tahun, $bulan, 1)
            ->addMonthNoOverflow()
            ->day($hari)
            ->toDateString();
    }

    public function isTerlambat(?\Carbon\Carbon $padaTanggal = null): bool
    {
        if ($this->isLunas() || ! $this->tanggal_jatuh_tempo) {
            return false;
        }

        $referensi = ($padaTanggal ?? now())->copy()->startOfDay();
        return $referensi->gt($this->tanggal_jatuh_tempo->copy()->startOfDay());
    }

    public function estimasiDenda(?\Carbon\Carbon $padaTanggal = null): float
    {
        if ($this->isLunas()) {
            return (float) $this->denda;
        }

        return $this->isTerlambat($padaTanggal)
            ? (float) config('voltpay.denda_keterlambatan', 10000)
            : 0.0;
    }

    public function getStatusTampilAttribute(): string
    {
        if ($this->isLunas()) {
            return 'lunas';
        }

        return $this->isTerlambat() ? 'terlambat' : 'belum_lunas';
    }
}

