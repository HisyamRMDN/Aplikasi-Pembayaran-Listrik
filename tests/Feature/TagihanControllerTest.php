<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Tagihan;
use App\Models\Pelanggan;
use App\Models\Penggunaan;
use Illuminate\Foundation\Testing\RefreshDatabase;

class TagihanControllerTest extends TestCase
{
    use RefreshDatabase;

    private function actingAsAdmin()
    {
        $admin = User::factory()->create();
        $this->actingAs($admin);
    }

    public function test_index_shows_tagihan_list()
    {
        $this->actingAsAdmin();

        Tagihan::factory()->count(3)->create();

        $response = $this->get(route('tagihan.index'));

        $response->assertStatus(200);
        $response->assertViewIs('admin.tagihan.index');
    }

    public function test_create_form_loads_successfully()
    {
        $this->actingAsAdmin();

        $response = $this->get(route('tagihan.create'));

        $response->assertStatus(200);
        $response->assertViewIs('admin.tagihan.create');
    }

    public function test_store_new_tagihan()
    {
        $this->actingAsAdmin();

        $pelanggan = Pelanggan::factory()->create();
        $penggunaan = Penggunaan::factory()->create([
            'id_pelanggan' => $pelanggan->id_pelanggan
        ]);

        $data = [
            'id_pelanggan' => $pelanggan->id_pelanggan,
            'id_penggunaan' => $penggunaan->id_penggunaan,
            'bulan' => 7,
            'tahun' => 2025,
            'jumlah_meter' => 100,
            'status' => 'belum_lunas'
        ];

        $response = $this->post(route('tagihan.store'), $data);

        $response->assertRedirect(route('tagihan.index'));
        $this->assertDatabaseHas('tagihans', $data);
    }

    public function test_store_duplicate_tagihan_fails()
    {
        $this->actingAsAdmin();

        $pelanggan = Pelanggan::factory()->create();

        Tagihan::factory()->create([
            'id_pelanggan' => $pelanggan->id_pelanggan,
            'bulan' => 7,
            'tahun' => 2025
        ]);

        $data = [
            'id_pelanggan' => $pelanggan->id_pelanggan,
            'bulan' => 7,
            'tahun' => 2025,
            'jumlah_meter' => 150,
            'status' => 'belum_lunas'
        ];

        $response = $this->post(route('tagihan.store'), $data);

        $response->assertSessionHasErrors(['error']);
    }

    public function test_show_tagihan_detail()
    {
        $this->actingAsAdmin();

        $tagihan = Tagihan::factory()->create();

        $response = $this->get(route('tagihan.show', $tagihan->id_tagihan));

        $response->assertStatus(200);
        $response->assertViewIs('admin.tagihan.show');
    }

    public function test_edit_form_loads_correctly()
    {
        $this->actingAsAdmin();

        $tagihan = Tagihan::factory()->create();

        $response = $this->get(route('tagihan.edit', $tagihan->id_tagihan));

        $response->assertStatus(200);
        $response->assertViewIs('admin.tagihan.edit');
    }

    public function test_update_tagihan()
    {
        $this->actingAsAdmin();

        $tagihan = Tagihan::factory()->create([
            'status' => 'belum_lunas',
            'jumlah_meter' => 100
        ]);

        $response = $this->put(route('tagihan.update', $tagihan->id_tagihan), [
            'status' => 'lunas',
            'jumlah_meter' => 150
        ]);

        $response->assertRedirect(route('tagihan.index'));

        $this->assertDatabaseHas('tagihans', [
            'id_tagihan' => $tagihan->id_tagihan,
            'status' => 'lunas',
            'jumlah_meter' => 150
        ]);
    }

    public function test_destroy_tagihan()
    {
        $this->actingAsAdmin();

        $tagihan = Tagihan::factory()->create();

        $response = $this->delete(route('tagihan.destroy', $tagihan->id_tagihan));

        $response->assertRedirect(route('tagihan.index'));

        $this->assertDatabaseMissing('tagihans', [
            'id_tagihan' => $tagihan->id_tagihan
        ]);
    }
}
