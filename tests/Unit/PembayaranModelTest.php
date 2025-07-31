<?php

namespace Tests\Unit;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Models\Pembayaran;
use App\Models\Tagihan;
use App\Models\Pelanggan;
use App\Models\User;

class PembayaranModelTest extends TestCase
{
    use RefreshDatabase;

    public function test_pembayaran_can_be_created()
    {
        $pembayaran = Pembayaran::factory()->create([
            'biaya_admin' => 3000
        ]);

        $this->assertDatabaseHas('pembayarans', [
            'id_pembayaran' => $pembayaran->id_pembayaran,
            'biaya_admin' => 3000,
        ]);
    }

    public function test_pembayaran_belongs_to_tagihan()
    {
        $pembayaran = Pembayaran::factory()->create();

        $this->assertNotNull($pembayaran->tagihan);
        $this->assertInstanceOf(Tagihan::class, $pembayaran->tagihan);
    }

    public function test_pembayaran_belongs_to_pelanggan()
    {
        $pembayaran = Pembayaran::factory()->create();

        $this->assertNotNull($pembayaran->pelanggan);
        $this->assertInstanceOf(Pelanggan::class, $pembayaran->pelanggan);
    }

    public function test_pembayaran_belongs_to_user()
    {
        $pembayaran = Pembayaran::factory()->create();

        $this->assertNotNull($pembayaran->user);
        $this->assertInstanceOf(User::class, $pembayaran->user);
    }
}
