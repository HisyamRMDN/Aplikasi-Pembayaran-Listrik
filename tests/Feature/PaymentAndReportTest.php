<?php

namespace Tests\Feature;

use App\Models\Level;
use App\Models\Pelanggan;
use App\Models\Pembayaran;
use App\Models\Penggunaan;
use App\Models\Tagihan;
use App\Models\Tarif;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PaymentAndReportTest extends TestCase
{
    use RefreshDatabase;

    private User $admin;
    private User $kasir;
    private Tagihan $tagihan;

    protected function setUp(): void
    {
        parent::setUp();

        $levelAdmin = Level::create(['nama_level' => 'Administrator']);
        $levelKasir = Level::create(['nama_level' => 'Petugas Loket']);

        $this->admin = User::create([
            'username' => 'admin_pay',
            'password' => bcrypt('password123'),
            'nama_admin' => 'Admin Pembayaran',
            'id_level' => $levelAdmin->id_level,
        ]);

        $this->kasir = User::create([
            'username' => 'kasir_pay',
            'password' => bcrypt('password123'),
            'nama_admin' => 'Kasir Loket',
            'id_level' => $levelKasir->id_level,
        ]);

        $tarif = Tarif::create([
            'daya' => 1300,
            'tarif_per_kwh' => 1500,
        ]);

        $pelanggan = Pelanggan::create([
            'username' => 'pelanggan_pay',
            'password' => bcrypt('password123'),
            'nomor_kwh' => '541122334455',
            'nama_pelanggan' => 'Ahmad Fauzi',
            'alamat' => 'Jl. Gatot Subroto No. 12',
            'id_tarif' => $tarif->id_tarif,
        ]);

        $penggunaan = Penggunaan::create([
            'id_pelanggan' => $pelanggan->id_pelanggan,
            'bulan' => 5,
            'tahun' => 2026,
            'meter_awal' => 500,
            'meter_akhir' => 600,
        ]);

        $this->tagihan = Tagihan::create([
            'id_penggunaan' => $penggunaan->id_penggunaan,
            'id_pelanggan' => $pelanggan->id_pelanggan,
            'bulan' => 5,
            'tahun' => 2026,
            'jumlah_meter' => 100,
            'tarif_per_kwh' => 1500,
            'total_tagihan' => 150000,
            'tanggal_jatuh_tempo' => Carbon::create(2026, 5, 20),
            'status' => 'belum_lunas',
        ]);
    }

    public function test_kasir_dapat_memproses_pembayaran_dan_status_tagihan_menjadi_lunas(): void
    {
        $response = $this->actingAs($this->kasir, 'web')
            ->post(route('pembayaran.store'), [
                'id_tagihan' => $this->tagihan->id_tagihan,
                'tanggal_pembayaran' => '2026-05-10',
                'biaya_admin' => 2500,
            ]);

        $response->assertRedirect(route('pembayaran.index'));

        $this->assertDatabaseHas('pembayarans', [
            'id_tagihan' => $this->tagihan->id_tagihan,
            'id_user' => $this->kasir->id_user,
            'biaya_admin' => 2500,
            'total_bayar' => 152500,
            'status' => 'berhasil',
        ]);

        $this->assertDatabaseHas('tagihans', [
            'id_tagihan' => $this->tagihan->id_tagihan,
            'status' => 'lunas',
        ]);
    }

    public function test_hanya_administrator_yang_dapat_membatalkan_pembayaran(): void
    {
        $pembayaran = Pembayaran::create([
            'id_tagihan' => $this->tagihan->id_tagihan,
            'id_pelanggan' => $this->tagihan->id_pelanggan,
            'tanggal_pembayaran' => '2026-05-10',
            'bulan_bayar' => 'Mei 2026',
            'biaya_admin' => 2500,
            'total_bayar' => 152500,
            'status' => 'berhasil',
            'id_user' => $this->kasir->id_user,
        ]);

        $this->tagihan->update(['status' => 'lunas']);

        // Kasir ditolak 403 saat mencoba membatalkan pembayaran
        $this->actingAs($this->kasir, 'web')
            ->delete(route('pembayaran.destroy', $pembayaran->id_pembayaran), [
                'alasan' => 'Salah input',
            ])
            ->assertForbidden();

        // Administrator berhasil membatalkan pembayaran (audit status -> batal)
        $this->actingAs($this->admin, 'web')
            ->delete(route('pembayaran.destroy', $pembayaran->id_pembayaran), [
                'alasan' => 'Koreksi transaksi loket',
            ])
            ->assertRedirect(route('pembayaran.index'));

        $this->assertDatabaseHas('pembayarans', [
            'id_pembayaran' => $pembayaran->id_pembayaran,
            'status' => 'batal',
            'alasan_batal' => 'Koreksi transaksi loket',
            'id_user_batal' => $this->admin->id_user,
        ]);

        $this->assertDatabaseHas('tagihans', [
            'id_tagihan' => $this->tagihan->id_tagihan,
            'status' => 'belum_lunas',
        ]);
    }

    public function test_laporan_rekapitulasi_dan_export_csv_dapat_diakses(): void
    {
        $this->actingAs($this->kasir, 'web')
            ->get(route('laporan.index', ['bulan' => 5, 'tahun' => 2026]))
            ->assertOk();

        $response = $this->actingAs($this->kasir, 'web')
            ->get(route('laporan.export-csv', ['bulan' => 5, 'tahun' => 2026]));

        $response->assertOk();
        $this->assertStringContainsString('text/csv', (string) $response->headers->get('Content-Type'));
    }
}
