<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // 1. Cek dulu sebelum mengubah apa pun: data ganda akan membuat unique index gagal.
        $this->pastikanTidakAdaDuplikat('tagihans');
        $this->pastikanTidakAdaDuplikat('penggunaans');

        // 2. Kolom snapshot tarif (sementara nullable agar data lama bisa diisi).
        Schema::table('tagihans', function (Blueprint $table) {
            $table->decimal('tarif_per_kwh', 10, 2)->nullable()->after('jumlah_meter');
            $table->decimal('total_tagihan', 12, 2)->nullable()->after('tarif_per_kwh');
        });

        // 3. Isi tagihan lama memakai tarif pelanggan saat ini (satu kali saja).
        DB::statement('
            UPDATE tagihans t
            JOIN pelanggans p ON p.id_pelanggan = t.id_pelanggan
            JOIN tarifs r ON r.id_tarif = p.id_tarif
            SET t.tarif_per_kwh = r.tarif_per_kwh,
                t.total_tagihan = ROUND(t.jumlah_meter * r.tarif_per_kwh, 2)
        ');

        // 4. Kunci kolom, izinkan tagihan manual (tanpa penggunaan), dan pasang unique index.
        Schema::table('tagihans', function (Blueprint $table) {
            $table->decimal('tarif_per_kwh', 10, 2)->nullable(false)->change();
            $table->decimal('total_tagihan', 12, 2)->nullable(false)->change();
            $table->unsignedBigInteger('id_penggunaan')->nullable()->change();
            $table->unique(['id_pelanggan', 'bulan', 'tahun'], 'tagihans_pelanggan_periode_unique');
        });

        Schema::table('penggunaans', function (Blueprint $table) {
            $table->unique(['id_pelanggan', 'bulan', 'tahun'], 'penggunaans_pelanggan_periode_unique');
        });
    }

    public function down(): void
    {
        // MySQL bisa menghapus index biasa milik foreign key saat unique dibuat,
        // jadi pasang lagi index biasanya sebelum unique dilepas.
        Schema::table('penggunaans', function (Blueprint $table) {
            $table->index('id_pelanggan');
            $table->dropUnique('penggunaans_pelanggan_periode_unique');
        });

        Schema::table('tagihans', function (Blueprint $table) {
            $table->index('id_pelanggan');
            $table->dropUnique('tagihans_pelanggan_periode_unique');
            $table->dropColumn(['tarif_per_kwh', 'total_tagihan']);
            // id_penggunaan sengaja dibiarkan nullable agar rollback tidak gagal
            // bila sudah ada tagihan manual.
        });
    }

    private function pastikanTidakAdaDuplikat(string $tabel): void
    {
        $duplikat = DB::table($tabel)
            ->select('id_pelanggan', 'bulan', 'tahun', DB::raw('COUNT(*) as jumlah'))
            ->groupBy('id_pelanggan', 'bulan', 'tahun')
            ->havingRaw('COUNT(*) > 1')
            ->get();

        if ($duplikat->isNotEmpty()) {
            $daftar = $duplikat
                ->map(fn ($d) => "pelanggan {$d->id_pelanggan} periode {$d->bulan}/{$d->tahun} ({$d->jumlah}x)")
                ->implode('; ');

            throw new RuntimeException("Ada data ganda di tabel {$tabel}: {$daftar}. Bersihkan dulu, lalu jalankan migrate lagi.");
        }
    }
};
