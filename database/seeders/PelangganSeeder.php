<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Pelanggan;

class PelangganSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Pelanggan::create([
            'username' => 'budi',
            'password' => bcrypt('budi123'),
            'nomor_kwh' => '12345678901',
            'nama_pelanggan' => 'Budi Santoso',
            'alamat' => 'Jl. Merdeka No. 123, Jakarta',
            'id_tarif' => 1
        ]);

        Pelanggan::create([
            'username' => 'sari',
            'password' => bcrypt('sari123'),
            'nomor_kwh' => '23456789012',
            'nama_pelanggan' => 'Sari Dewi',
            'alamat' => 'Jl. Pahlawan No. 456, Bandung',
            'id_tarif' => 2
        ]);

        Pelanggan::create([
            'username' => 'andi',
            'password' => bcrypt('andi123'),
            'nomor_kwh' => '34567890123',
            'nama_pelanggan' => 'Andi Wijaya',
            'alamat' => 'Jl. Sudirman No. 789, Surabaya',
            'id_tarif' => 3
        ]);
    }
}
