<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Tarif;

class TarifSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Tarif::create([
            'daya' => 450,
            'tarif_per_kwh' => 415.00
        ]);

        Tarif::create([
            'daya' => 900,
            'tarif_per_kwh' => 605.00
        ]);

        Tarif::create([
            'daya' => 1300,
            'tarif_per_kwh' => 1394.50
        ]);

        Tarif::create([
            'daya' => 2200,
            'tarif_per_kwh' => 1444.70
        ]);

        Tarif::create([
            'daya' => 3500,
            'tarif_per_kwh' => 1699.53
        ]);
    }
}
