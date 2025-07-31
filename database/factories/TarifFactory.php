<?php

namespace Database\Factories;

use App\Models\Tarif;
use Illuminate\Database\Eloquent\Factories\Factory;

class TarifFactory extends Factory
{
    protected $model = Tarif::class;

    public function definition(): array
    {
        return [
            'daya' => $this->faker->randomElement([900, 1300, 2200]),
            'tarif_per_kwh' => $this->faker->randomFloat(2, 1000, 1500),
        ];
    }
}
