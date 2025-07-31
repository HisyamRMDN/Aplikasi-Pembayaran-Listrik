<?php

namespace Tests\Unit;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Models\Penggunaan;
use App\Models\Pelanggan;

class PenggunaanModelTest extends TestCase
{
    use RefreshDatabase;

    public function test_penggunaan_can_be_created()
    {
        $penggunaan = Penggunaan::factory()->create([
            'meter_awal' => 1200,
            'meter_akhir' => 1600,
        ]);

        $this->assertDatabaseHas('penggunaans', [
            'id_penggunaan' => $penggunaan->id_penggunaan,
            'meter_awal' => 1200,
            'meter_akhir' => 1600,
        ]);
    }

    public function test_penggunaan_belongs_to_pelanggan()
    {
        $penggunaan = Penggunaan::factory()->create();

        $this->assertNotNull($penggunaan->pelanggan);
        $this->assertInstanceOf(Pelanggan::class, $penggunaan->pelanggan);
    }
}
