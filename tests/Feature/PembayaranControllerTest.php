<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Tarif;
use App\Models\Tagihan;
use App\Models\Pelanggan;
use App\Models\Pembayaran;
use App\Models\Penggunaan;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Carbon\Carbon;

class PembayaranControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_index_returns_successful_response()
    {
        $admin = User::factory()->create();
        $this->actingAs($admin);

        $response = $this->get(route('pembayaran.index'));
        $response->assertStatus(200);
        $response->assertViewIs('admin.pembayaran.index');
    }

    public function test_create_view_contains_belum_lunas_tagihans()
    {
        $admin = User::factory()->create();
        $this->actingAs($admin);

        Tagihan::factory()->create(['status' => 'belum_lunas']);

        $response = $this->get(route('pembayaran.create'));
        $response->assertStatus(200);
        $response->assertViewIs('admin.pembayaran.create');
    }

    public function test_store_successful_pembayaran()
    {
        $admin = User::factory()->create();
        $this->actingAs($admin);

        // Buat tarif dulu
        $tarif = Tarif::factory()->create(['tarif_per_kwh' => 1000]);

        // Buat pelanggan dengan tarif tersebut
        $pelanggan = Pelanggan::factory()->create([
            'id_tarif' => $tarif->id_tarif,
        ]);

        // Buat penggunaan
        $penggunaan = Penggunaan::factory()->create([
            'id_pelanggan' => $pelanggan->id_pelanggan,
            'meter_awal' => 100,
            'meter_akhir' => 200,
        ]);

        $jumlah_meter = $penggunaan->meter_akhir - $penggunaan->meter_awal;

        $tagihan = Tagihan::factory()->create([
            'id_pelanggan' => $pelanggan->id_pelanggan,
            'id_penggunaan' => $penggunaan->id_penggunaan,
            'status' => 'belum_lunas',
            'jumlah_meter' => $jumlah_meter,
        ]);

        $biaya_admin = 2500;
        $total_tagihan = $jumlah_meter * $tarif->tarif_per_kwh;
        $total_bayar = $total_tagihan + $biaya_admin;

        $response = $this->post(route('pembayaran.store'), [
            'id_tagihan' => $tagihan->id_tagihan,
            'tanggal_pembayaran' => now()->format('Y-m-d'),
            'biaya_admin' => $biaya_admin,
        ]);

        $response->assertRedirect(route('pembayaran.index'));

        $this->assertDatabaseHas('pembayarans', [
            'id_tagihan' => $tagihan->id_tagihan,
            'total_bayar' => $total_bayar,
        ]);

        $this->assertDatabaseHas('tagihans', [
            'id_tagihan' => $tagihan->id_tagihan,
            'status' => 'lunas',
        ]);
    }


    public function test_store_fails_when_tagihan_already_lunas()
    {
        $admin = User::factory()->create();
        $this->actingAs($admin);

        $tagihan = Tagihan::factory()->create(['status' => 'lunas']);

        $response = $this->post(route('pembayaran.store'), [
            'id_tagihan' => $tagihan->id_tagihan,
            'tanggal_pembayaran' => now()->format('Y-m-d'),
            'biaya_admin' => 2500,
        ]);

        $response->assertSessionHasErrors('error');

        $this->assertDatabaseMissing('pembayarans', [
            'id_tagihan' => $tagihan->id_tagihan,
        ]);
    }

    public function test_show_returns_pembayaran_detail()
    {
        $admin = User::factory()->create();
        $this->actingAs($admin);

        $pembayaran = Pembayaran::factory()->create();

        $response = $this->get(route('pembayaran.show', $pembayaran->id_pembayaran));
        $response->assertStatus(200);
        $response->assertViewIs('admin.pembayaran.show');
        $response->assertViewHas('pembayaran');
    }

    public function test_update_pembayaran_data()
    {
        $admin = User::factory()->create();
        $this->actingAs($admin);

        $pembayaran = Pembayaran::factory()->create(['biaya_admin' => 1000]);

        $response = $this->put(route('pembayaran.update', $pembayaran->id_pembayaran), [
            'tanggal_pembayaran' => now()->format('Y-m-d'),
            'biaya_admin' => 2000,
        ]);

        $response->assertRedirect(route('pembayaran.index'));

        $this->assertDatabaseHas('pembayarans', [
            'id_pembayaran' => $pembayaran->id_pembayaran,
            'biaya_admin' => 2000,
        ]);
    }

    public function test_destroy_deletes_pembayaran_and_updates_tagihan()
    {
        $admin = User::factory()->create();
        $this->actingAs($admin);

        $tagihan = Tagihan::factory()->create(['status' => 'lunas']);
        $pembayaran = Pembayaran::factory()->create(['id_tagihan' => $tagihan->id_tagihan]);

        $response = $this->delete(route('pembayaran.destroy', $pembayaran->id_pembayaran));

        $response->assertRedirect(route('pembayaran.index'));

        $this->assertDatabaseMissing('pembayarans', [
            'id_pembayaran' => $pembayaran->id_pembayaran
        ]);

        $this->assertDatabaseHas('tagihans', [
            'id_tagihan' => $tagihan->id_tagihan,
            'status' => 'belum_lunas'
        ]);
    }

    public function test_pelanggan_tagihan_view()
    {
        $pelanggan = Pelanggan::factory()->create();
        $this->actingAs($pelanggan, 'pelanggan');

        $response = $this->get(route('pelanggan.tagihan'));

        $response->assertStatus(200);
        $response->assertViewIs('pelanggan.tagihan');
    }
}
