<?php

namespace Database\Factories;

use App\Models\Penggunaan;
use App\Models\Pelanggan;
use Illuminate\Database\Eloquent\Factories\Factory;

class PenggunaanFactory extends Factory
{
    protected $model = Penggunaan::class;

    public function definition(): array
    {
        return [
            'id_pelanggan' => Pelanggan::factory(),
            'bulan' => $this->faker->numberBetween(1, 12),
            'tahun' => $this->faker->year(),
            'meter_awal' => 1000,
            'meter_akhir' => 1500,
        ];
    }
}
