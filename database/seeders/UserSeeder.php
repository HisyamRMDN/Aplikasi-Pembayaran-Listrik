<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        User::create([
            'username' => 'admin',
            'password' => bcrypt('admin123'),
            'nama_admin' => 'Administrator',
            'id_level' => 1
        ]);

        User::create([
            'username' => 'petugas',
            'password' => bcrypt('petugas123'),
            'nama_admin' => 'Petugas Pembayaran',
            'id_level' => 2
        ]);
    }
}
