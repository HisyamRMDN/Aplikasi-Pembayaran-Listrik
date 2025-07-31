<?php

namespace Database\Factories;

use App\Models\Pelanggan;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;
use App\Models\Tarif;

class PelangganFactory extends Factory
{
    protected $model = Pelanggan::class;

    public function definition(): array
    {
        return [
            'username' => $this->faker->unique()->userName,
            'password' => Hash::make('password'),
            'nomor_kwh' => $this->faker->unique()->numerify('12345###'),
            'nama_pelanggan' => $this->faker->name,
            'alamat' => $this->faker->address,
            'id_tarif' => Tarif::factory() // Pastikan tarif dengan ID 1 sudah dibuat saat testing
        ];
    }
}
