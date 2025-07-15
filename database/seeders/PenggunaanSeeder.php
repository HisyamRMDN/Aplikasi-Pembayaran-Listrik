<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Penggunaan;
use App\Models\Tagihan;

class PenggunaanSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Data penggunaan untuk pelanggan 1 (Budi)
        $penggunaan1 = Penggunaan::create([
            'id_pelanggan' => 1,
            'bulan' => 11,
            'tahun' => 2024,
            'meter_awal' => 1000,
            'meter_akhir' => 1150
        ]);

        // Generate tagihan otomatis
        Tagihan::create([
            'id_penggunaan' => $penggunaan1->id_penggunaan,
            'id_pelanggan' => $penggunaan1->id_pelanggan,
            'bulan' => $penggunaan1->bulan,
            'tahun' => $penggunaan1->tahun,
            'jumlah_meter' => $penggunaan1->meter_akhir - $penggunaan1->meter_awal,
            'status' => 'belum_lunas'
        ]);

        $penggunaan2 = Penggunaan::create([
            'id_pelanggan' => 1,
            'bulan' => 12,
            'tahun' => 2024,
            'meter_awal' => 1150,
            'meter_akhir' => 1280
        ]);

        Tagihan::create([
            'id_penggunaan' => $penggunaan2->id_penggunaan,
            'id_pelanggan' => $penggunaan2->id_pelanggan,
            'bulan' => $penggunaan2->bulan,
            'tahun' => $penggunaan2->tahun,
            'jumlah_meter' => $penggunaan2->meter_akhir - $penggunaan2->meter_awal,
            'status' => 'belum_lunas'
        ]);

        // Data penggunaan untuk pelanggan 2 (Sari)
        $penggunaan3 = Penggunaan::create([
            'id_pelanggan' => 2,
            'bulan' => 11,
            'tahun' => 2024,
            'meter_awal' => 2000,
            'meter_akhir' => 2200
        ]);

        Tagihan::create([
            'id_penggunaan' => $penggunaan3->id_penggunaan,
            'id_pelanggan' => $penggunaan3->id_pelanggan,
            'bulan' => $penggunaan3->bulan,
            'tahun' => $penggunaan3->tahun,
            'jumlah_meter' => $penggunaan3->meter_akhir - $penggunaan3->meter_awal,
            'status' => 'lunas'
        ]);

        // Data penggunaan untuk pelanggan 3 (Andi)
        $penggunaan4 = Penggunaan::create([
            'id_pelanggan' => 3,
            'bulan' => 12,
            'tahun' => 2024,
            'meter_awal' => 3000,
            'meter_akhir' => 3180
        ]);

        Tagihan::create([
            'id_penggunaan' => $penggunaan4->id_penggunaan,
            'id_pelanggan' => $penggunaan4->id_pelanggan,
            'bulan' => $penggunaan4->bulan,
            'tahun' => $penggunaan4->tahun,
            'jumlah_meter' => $penggunaan4->meter_akhir - $penggunaan4->meter_awal,
            'status' => 'belum_lunas'
        ]);
    }
}
