<?php

namespace Tests\Feature;

use App\Models\Pelanggan;
use App\Models\Tarif;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class PelangganControllerTest extends TestCase
{
    use RefreshDatabase;

    private function actingAsAdmin()
    {
        $admin = User::factory()->create();
        $this->actingAs($admin); // Sesuaikan jika kamu pakai guard lain
    }

    public function test_index_displays_pelanggans()
    {
        $this->actingAsAdmin();

        $tarif = Tarif::factory()->create();
        Pelanggan::factory()->count(2)->create(['id_tarif' => $tarif->id_tarif]);

        $response = $this->get(route('pelanggan.index'));

        $response->assertStatus(200);
        $response->assertViewIs('admin.pelanggan.index');
        $response->assertViewHas('pelanggans');
    }

    public function test_create_displays_form()
    {
        $this->actingAsAdmin();

        Tarif::factory()->count(2)->create();

        $response = $this->get(route('pelanggan.create'));

        $response->assertStatus(200);
        $response->assertViewIs('admin.pelanggan.create');
        $response->assertViewHas('tarifs');
    }

    public function test_store_creates_pelanggan()
    {
        $this->actingAsAdmin();

        $tarif = Tarif::factory()->create();

        $data = [
            'username' => 'pelanggan1',
            'password' => 'secret123',
            'nomor_kwh' => '1234567890',
            'nama_pelanggan' => 'Nama Pelanggan',
            'alamat' => 'Jl. Testing',
            'id_tarif' => $tarif->id_tarif,
        ];

        $response = $this->post(route('pelanggan.store'), $data);

        $response->assertRedirect(route('pelanggan.index'));
        $this->assertDatabaseHas('pelanggans', [
            'username' => 'pelanggan1',
            'nomor_kwh' => '1234567890',
        ]);
    }

    public function test_show_displays_detail()
    {
        $this->actingAsAdmin();

        $tarif = Tarif::factory()->create();
        $pelanggan = Pelanggan::factory()->create(['id_tarif' => $tarif->id_tarif]);

        $response = $this->get(route('pelanggan.show', $pelanggan->id_pelanggan));

        $response->assertStatus(200);
        $response->assertViewIs('admin.pelanggan.show');
        $response->assertViewHas('pelanggan');
    }

    public function test_edit_displays_form()
    {
        $this->actingAsAdmin();

        $tarif = Tarif::factory()->create();
        $pelanggan = Pelanggan::factory()->create(['id_tarif' => $tarif->id_tarif]);

        $response = $this->get(route('pelanggan.edit', $pelanggan->id_pelanggan));

        $response->assertStatus(200);
        $response->assertViewIs('admin.pelanggan.edit');
        $response->assertViewHas(['pelanggan', 'tarifs']);
    }

    public function test_update_modifies_data()
    {
        $this->actingAsAdmin();

        $tarif1 = Tarif::factory()->create();
        $tarif2 = Tarif::factory()->create();
        $pelanggan = Pelanggan::factory()->create([
            'username' => 'userlama',
            'nomor_kwh' => '111111',
            'id_tarif' => $tarif1->id_tarif
        ]);

        $data = [
            'username' => 'userbaru',
            'nomor_kwh' => '222222',
            'nama_pelanggan' => 'Update Nama',
            'alamat' => 'Alamat Baru',
            'id_tarif' => $tarif2->id_tarif,
        ];

        $response = $this->put(route('pelanggan.update', $pelanggan->id_pelanggan), $data);

        $response->assertRedirect(route('pelanggan.index'));
        $this->assertDatabaseHas('pelanggans', [
            'id_pelanggan' => $pelanggan->id_pelanggan,
            'username' => 'userbaru',
            'nomor_kwh' => '222222',
        ]);
    }

    public function test_destroy_deletes_pelanggan()
    {
        $this->actingAsAdmin();

        $pelanggan = Pelanggan::factory()->create();

        $response = $this->delete(route('pelanggan.destroy', $pelanggan->id_pelanggan));

        $response->assertRedirect(route('pelanggan.index'));
        $this->assertModelMissing($pelanggan);
    }
}
