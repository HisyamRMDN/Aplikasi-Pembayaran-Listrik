<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Pelanggan;
use App\Models\Penggunaan;
use App\Models\Tagihan;
use Illuminate\Foundation\Testing\RefreshDatabase;

class PenggunaanControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_index_returns_success()
    {
        $admin = User::factory()->create();
        $this->actingAs($admin);

        $response = $this->get(route('penggunaan.index'));

        $response->assertStatus(200);
        $response->assertViewIs('admin.penggunaan.index');
    }

    public function test_create_form_loads_successfully()
    {
        $admin = User::factory()->create();
        $this->actingAs($admin);

        $response = $this->get(route('penggunaan.create'));

        $response->assertStatus(200);
        $response->assertViewIs('admin.penggunaan.create');
    }

    public function test_store_penggunaan_and_generate_tagihan()
    {
        $admin = User::factory()->create();
        $this->actingAs($admin);

        $pelanggan = Pelanggan::factory()->create();

        $data = [
            'id_pelanggan' => $pelanggan->id_pelanggan,
            'bulan' => 7,
            'tahun' => 2025,
            'meter_awal' => 100,
            'meter_akhir' => 200
        ];

        $response = $this->post(route('penggunaan.store'), $data);

        $response->assertRedirect(route('penggunaan.index'));

        $this->assertDatabaseHas('penggunaans', [
            'id_pelanggan' => $pelanggan->id_pelanggan,
            'bulan' => 7,
            'tahun' => 2025
        ]);

        $this->assertDatabaseHas('tagihans', [
            'id_pelanggan' => $pelanggan->id_pelanggan,
            'bulan' => 7,
            'tahun' => 2025,
            'jumlah_meter' => 100,
            'status' => 'belum_lunas'
        ]);
    }

    public function test_show_penggunaan_detail()
    {
        $admin = User::factory()->create();
        $this->actingAs($admin);

        $penggunaan = Penggunaan::factory()->create();

        $response = $this->get(route('penggunaan.show', $penggunaan->id_penggunaan));
        $response->assertStatus(200);
        $response->assertViewIs('admin.penggunaan.show');
    }

    public function test_edit_form_loads_correctly()
    {
        $admin = User::factory()->create();
        $this->actingAs($admin);

        $penggunaan = Penggunaan::factory()->create();

        $response = $this->get(route('penggunaan.edit', $penggunaan->id_penggunaan));
        $response->assertStatus(200);
        $response->assertViewIs('admin.penggunaan.edit');
    }

    public function test_update_penggunaan_and_tagihan()
    {
        $admin = User::factory()->create(); // ← tambahkan ini
        $this->actingAs($admin);           // ← dan ini

        $penggunaan = Penggunaan::factory()->create([
            'meter_awal' => 100,
            'meter_akhir' => 200,
            'tahun' => 2025
        ]);

        // Buat tagihan awal agar bisa diupdate
        Tagihan::create([
            'id_penggunaan' => $penggunaan->id_penggunaan,
            'id_pelanggan' => $penggunaan->id_pelanggan,
            'bulan' => $penggunaan->bulan,
            'tahun' => $penggunaan->tahun,
            'jumlah_meter' => 100,
            'status' => 'belum_lunas'
        ]);

        $data = [
            'id_pelanggan' => $penggunaan->id_pelanggan,
            'bulan' => $penggunaan->bulan,
            'tahun' => 2025,
            'meter_awal' => 200,
            'meter_akhir' => 300
        ];

        $response = $this->put(route('penggunaan.update', $penggunaan->id_penggunaan), $data);

        $response->assertRedirect(route('penggunaan.index'));

        $this->assertDatabaseHas('penggunaans', [
            'id_penggunaan' => $penggunaan->id_penggunaan,
            'meter_awal' => 200,
            'meter_akhir' => 300
        ]);

        $this->assertDatabaseHas('tagihans', [
            'id_penggunaan' => $penggunaan->id_penggunaan,
            'jumlah_meter' => 100
        ]);
    }


    public function test_destroy_penggunaan_and_tagihan()
    {
        $admin = User::factory()->create();
        $this->actingAs($admin);

        $penggunaan = Penggunaan::factory()->create();

        // Auto generate tagihan saat create penggunaan
        $this->post(route('penggunaan.store'), [
            'id_pelanggan' => $penggunaan->id_pelanggan,
            'bulan' => $penggunaan->bulan,
            'tahun' => $penggunaan->tahun + 1,
            'meter_awal' => 100,
            'meter_akhir' => 200
        ]);

        $response = $this->delete(route('penggunaan.destroy', $penggunaan->id_penggunaan));

        $response->assertRedirect(route('penggunaan.index'));

        $this->assertDatabaseMissing('penggunaans', [
            'id_penggunaan' => $penggunaan->id_penggunaan
        ]);

        $this->assertDatabaseMissing('tagihans', [
            'id_penggunaan' => $penggunaan->id_penggunaan
        ]);
    }
}
