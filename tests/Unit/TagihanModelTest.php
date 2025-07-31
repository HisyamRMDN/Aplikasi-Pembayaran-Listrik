<?php

namespace Tests\Unit;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Models\Tagihan;
use App\Models\Penggunaan;
use App\Models\Pelanggan;
use App\Models\Tarif;

class TagihanModelTest extends TestCase
{
    use RefreshDatabase;

    public function test_tagihan_can_be_created()
    {
        $tagihan = Tagihan::factory()->create([
            'jumlah_meter' => 200,
            'status' => 'belum_lunas',
        ]);

        $this->assertDatabaseHas('tagihans', [
            'id_tagihan' => $tagihan->id_tagihan,
            'jumlah_meter' => 200,
            'status' => 'belum_lunas',
        ]);
    }

    public function test_tagihan_belongs_to_penggunaan_and_pelanggan()
    {
        $tagihan = Tagihan::factory()->create();

        $this->assertNotNull($tagihan->penggunaan);
        $this->assertNotNull($tagihan->pelanggan);

        $this->assertInstanceOf(Penggunaan::class, $tagihan->penggunaan);
        $this->assertInstanceOf(Pelanggan::class, $tagihan->pelanggan);
    }

    public function test_tagihan_total_tagihan_attribute()
    {
        $tarif = Tarif::factory()->create(['tarif_per_kwh' => 1400]);

        $pelanggan = Pelanggan::factory()->create([
            'id_tarif' => $tarif->id_tarif,
        ]);

        $penggunaan = Penggunaan::factory()->create([
            'id_pelanggan' => $pelanggan->id_pelanggan,
        ]);

        $tagihan = Tagihan::factory()->create([
            'id_penggunaan' => $penggunaan->id_penggunaan,
            'id_pelanggan' => $pelanggan->id_pelanggan,
            'jumlah_meter' => 100,
        ]);

        $this->assertEquals(100 * 1400, $tagihan->total_tagihan);
    }
}
