<?php

namespace Database\Factories;

use App\Models\Tagihan;
use App\Models\Pelanggan;
use App\Models\Penggunaan;
use Illuminate\Database\Eloquent\Factories\Factory;

class TagihanFactory extends Factory
{
    protected $model = Tagihan::class;

    public function definition(): array
    {
        $pelanggan = Pelanggan::factory()->create();

        return [
            'id_pelanggan' => $pelanggan->id_pelanggan,
            'id_penggunaan' => Penggunaan::factory()->create([
                'id_pelanggan' => $pelanggan->id_pelanggan,
            ])->id_penggunaan,
            'bulan' => $this->faker->numberBetween(1, 12),
            'tahun' => $this->faker->year(),
            'jumlah_meter' => $this->faker->numberBetween(100, 500),
            'status' => $this->faker->randomElement(['lunas', 'belum_lunas']),
        ];
    }
}
