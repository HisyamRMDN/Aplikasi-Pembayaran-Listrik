<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Pelanggan;
use App\Models\Penggunaan;
use App\Models\Tagihan;

class PenggunaanSeeder extends Seeder
{
    public function run(): void
    {
        $dataDemo = [
            ['id_pelanggan' => 1, 'bulan' => 11, 'tahun' => 2024, 'meter_awal' => 1000, 'meter_akhir' => 1150, 'status' => 'belum_lunas'],
            ['id_pelanggan' => 1, 'bulan' => 12, 'tahun' => 2024, 'meter_awal' => 1150, 'meter_akhir' => 1280, 'status' => 'belum_lunas'],
            ['id_pelanggan' => 2, 'bulan' => 11, 'tahun' => 2024, 'meter_awal' => 2000, 'meter_akhir' => 2200, 'status' => 'lunas'],
            ['id_pelanggan' => 3, 'bulan' => 12, 'tahun' => 2024, 'meter_awal' => 3000, 'meter_akhir' => 3180, 'status' => 'belum_lunas'],
        ];

        foreach ($dataDemo as $item) {
            $pelanggan = Pelanggan::with('tarif')->find($item['id_pelanggan']);
            if (! $pelanggan) continue;

            $penggunaan = Penggunaan::create([
                'id_pelanggan' => $item['id_pelanggan'],
                'bulan' => $item['bulan'],
                'tahun' => $item['tahun'],
                'meter_awal' => $item['meter_awal'],
                'meter_akhir' => $item['meter_akhir'],
            ]);

            $jumlahMeter = $penggunaan->meter_akhir - $penggunaan->meter_awal;
            $tarifPerKwh = $pelanggan->tarif->tarif_per_kwh;

            Tagihan::create([
                'id_penggunaan' => $penggunaan->id_penggunaan,
                'id_pelanggan' => $penggunaan->id_pelanggan,
                'bulan' => $penggunaan->bulan,
                'tahun' => $penggunaan->tahun,
                'jumlah_meter' => $jumlahMeter,
                'tarif_per_kwh' => $tarifPerKwh,
                'total_tagihan' => $jumlahMeter * $tarifPerKwh,
                'tanggal_jatuh_tempo' => Tagihan::hitungTanggalJatuhTempo($penggunaan->bulan, $penggunaan->tahun),
                'denda' => 0,
                'status' => $item['status'],
            ]);
        }
    }
}
