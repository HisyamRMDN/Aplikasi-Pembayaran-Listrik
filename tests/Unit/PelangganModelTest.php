<?php

namespace Tests\Unit;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Models\Pelanggan;
use App\Models\Tarif;
use App\Models\Penggunaan;

class PelangganModelTest extends TestCase
{
    use RefreshDatabase;

    public function test_pelanggan_can_be_created()
    {
        $pelanggan = Pelanggan::factory()->create([
            'nama_pelanggan' => 'John Doe',
        ]);

        $this->assertDatabaseHas('pelanggans', [
            'id_pelanggan' => $pelanggan->id_pelanggan,
            'nama_pelanggan' => 'John Doe',
        ]);
    }

    public function test_pelanggan_belongs_to_tarif()
    {
        $pelanggan = Pelanggan::factory()->create();

        $this->assertNotNull($pelanggan->tarif);
        $this->assertInstanceOf(Tarif::class, $pelanggan->tarif);
    }

    public function test_pelanggan_has_many_penggunaan()
    {
        $pelanggan = Pelanggan::factory()->create();

        Penggunaan::factory()->count(3)->create([
            'id_pelanggan' => $pelanggan->id_pelanggan,
        ]);

        $pelanggan->load('penggunaans'); // load relasi yang benar

        $this->assertCount(3, $pelanggan->penggunaans); // akses relasi yang benar
    }
}
