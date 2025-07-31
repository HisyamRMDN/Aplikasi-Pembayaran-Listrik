<?php

namespace Database\Factories;

use App\Models\Pembayaran;
use App\Models\Tagihan;
use App\Models\Pelanggan;
use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\User;

class PembayaranFactory extends Factory
{
    protected $model = Pembayaran::class;

    public function definition(): array
    {
        $tagihan = Tagihan::factory()->create();
        $pelanggan = $tagihan->pelanggan; // diasumsikan relasi sudah ada

        return [
            'id_tagihan' => $tagihan->id_tagihan,
            'id_pelanggan' => $pelanggan->id_pelanggan,
            'tanggal_pembayaran' => $this->faker->dateTimeThisYear(),
            'biaya_admin' => 2500,
            'total_bayar' => $this->faker->numberBetween(10000, 200000),
            'bulan_bayar' => $this->faker->numberBetween(1, 12),
            'id_user' => User::factory(),

        ];
    }
}
