<?php

namespace Tests\Feature;

use App\Models\Level;
use App\Models\Pelanggan;
use App\Models\Penggunaan;
use App\Models\Tagihan;
use App\Models\Tarif;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class MeterAndBillingIntegrityTest extends TestCase
{
    use RefreshDatabase;

    private User $admin;
    private Pelanggan $pelanggan;
    private Tarif $tarif;

    protected function setUp(): void
    {
        parent::setUp();

        $levelAdmin = Level::create(['nama_level' => 'Administrator']);
        $this->admin = User::create([
            'username' => 'admin_billing',
            'password' => bcrypt('password123'),
            'nama_admin' => 'Admin Billing',
            'id_level' => $levelAdmin->id_level,
        ]);

        $this->tarif = Tarif::create([
            'daya' => 1300,
            'tarif_per_kwh' => 1500,
        ]);

        $this->pelanggan = Pelanggan::create([
            'username' => 'pelanggan_uji',
            'password' => bcrypt('password123'),
            'nomor_kwh' => '549876543210',
            'nama_pelanggan' => 'Siti Aminah',
            'alamat' => 'Jl. Sudirman No. 5',
            'id_tarif' => $this->tarif->id_tarif,
        ]);
    }

    public function test_input_penggunaan_otomatis_membuat_tagihan_dengan_perhitungan_benar(): void
    {
        $response = $this->actingAs($this->admin, 'web')
            ->post(route('penggunaan.store'), [
                'id_pelanggan' => $this->pelanggan->id_pelanggan,
                'bulan' => 4,
                'tahun' => 2026,
                'meter_awal' => 1000,
                'meter_akhir' => 1150,
            ]);

        $response->assertRedirect(route('penggunaan.index'));

        $this->assertDatabaseHas('penggunaans', [
            'id_pelanggan' => $this->pelanggan->id_pelanggan,
            'bulan' => 4,
            'tahun' => 2026,
            'meter_awal' => 1000,
            'meter_akhir' => 1150,
        ]);

        $this->assertDatabaseHas('tagihans', [
            'id_pelanggan' => $this->pelanggan->id_pelanggan,
            'bulan' => 4,
            'tahun' => 2026,
            'jumlah_meter' => 150,
            'total_tagihan' => 225000,
            'status' => 'belum_lunas',
        ]);
    }

    public function test_update_penggunaan_menolak_meter_awal_lebih_kecil_dari_meter_akhir_bulan_sebelumnya(): void
    {
        // Periode April 2026: meter akhir 1150
        Penggunaan::create([
            'id_pelanggan' => $this->pelanggan->id_pelanggan,
            'bulan' => 4,
            'tahun' => 2026,
            'meter_awal' => 1000,
            'meter_akhir' => 1150,
        ]);

        // Periode Mei 2026: meter awal 1150 -> 1300
        $penggunaanMei = Penggunaan::create([
            'id_pelanggan' => $this->pelanggan->id_pelanggan,
            'bulan' => 5,
            'tahun' => 2026,
            'meter_awal' => 1150,
            'meter_akhir' => 1300,
        ]);

        // Coba ubah meter_awal Mei menjadi 1100 (< 1150)
        $response = $this->actingAs($this->admin, 'web')
            ->from(route('penggunaan.edit', $penggunaanMei->id_penggunaan))
            ->put(route('penggunaan.update', $penggunaanMei->id_penggunaan), [
                'meter_awal' => 1100,
                'meter_akhir' => 1300,
            ]);

        $response->assertRedirect(route('penggunaan.edit', $penggunaanMei->id_penggunaan));
        $response->assertSessionHasErrors('meter_awal');
    }

    public function test_pelanggan_dengan_riwayat_penggunaan_atau_tagihan_tidak_dapat_dihapus(): void
    {
        Penggunaan::create([
            'id_pelanggan' => $this->pelanggan->id_pelanggan,
            'bulan' => 4,
            'tahun' => 2026,
            'meter_awal' => 1000,
            'meter_akhir' => 1150,
        ]);

        $response = $this->actingAs($this->admin, 'web')
            ->delete(route('pelanggan.destroy', $this->pelanggan->id_pelanggan));

        $response->assertRedirect(route('pelanggan.index'));
        $response->assertSessionHas('error');

        $this->assertDatabaseHas('pelanggans', [
            'id_pelanggan' => $this->pelanggan->id_pelanggan,
        ]);
    }
}
