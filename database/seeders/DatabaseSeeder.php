<?php

namespace Database\Seeders;

use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
{
    $this->call([
        LevelSeeder::class,
        UserSeeder::class, // sesuaikan dengan nama seeder admin di proyekmu
        TarifSeeder::class, // sesuaikan jika ada seeder master tarif
    ]);
}
}
