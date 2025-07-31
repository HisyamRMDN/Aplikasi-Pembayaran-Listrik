<?php

namespace Tests\Unit;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Models\Tarif;
use App\Models\Pelanggan;

class TarifModelTest extends TestCase
{
    use RefreshDatabase;

    public function test_tarif_can_be_created()
    {
        $tarif = Tarif::factory()->create([
            'daya' => 1300,
            'tarif_per_kwh' => 1500.75,
        ]);

        $this->assertDatabaseHas('tarifs', [
            'id_tarif' => $tarif->id_tarif,
            'daya' => 1300,
            'tarif_per_kwh' => 1500.75,
        ]);
    }

    public function test_tarif_has_many_pelanggans()
    {
        $tarif = Tarif::factory()->create();

        $pelanggan1 = Pelanggan::factory()->create(['id_tarif' => $tarif->id_tarif]);
        $pelanggan2 = Pelanggan::factory()->create(['id_tarif' => $tarif->id_tarif]);

        $this->assertTrue($tarif->pelanggans->contains($pelanggan1));
        $this->assertTrue($tarif->pelanggans->contains($pelanggan2));
        $this->assertCount(2, $tarif->pelanggans);
    }

    public function test_tarif_per_kwh_casting_to_decimal()
    {
        $tarif = Tarif::factory()->create([
            'tarif_per_kwh' => 1234.567,
        ]);

        $this->assertEquals('1234.57', $tarif->tarif_per_kwh); // dibulatkan ke 2 angka desimal
    }
}
