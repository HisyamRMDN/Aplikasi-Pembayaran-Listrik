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

class CustomerPortalTest extends TestCase
{
    use RefreshDatabase;

    private Pelanggan $pelanggan;
    private Pelanggan $pelangganLain;
    private Tagihan $tagihanLunas;
    private Tagihan $tagihanBelumLunas;

    protected function setUp(): void
    {
        parent::setUp();

        $levelAdmin = Level::create(['nama_level' => 'Administrator']);
        $admin = User::create([
            'username' => 'admin_portal',
            'password' => bcrypt('password123'),
            'nama_admin' => 'Admin Portal',
            'id_level' => $levelAdmin->id_level,
        ]);

        $tarif = Tarif::create([
            'daya' => 1300,
            'tarif_per_kwh' => 1500,
        ]);

        $this->pelanggan = Pelanggan::create([
            'username' => 'hisyam_cust',
            'password' => bcrypt('password123'),
            'nomor_kwh' => '540001112223',
            'nama_pelanggan' => 'Hisyam Arasy',
            'alamat' => 'Jl. Margonda Raya No. 1',
            'id_tarif' => $tarif->id_tarif,
        ]);

        $this->pelangganLain = Pelanggan::create([
            'username' => 'orang_lain',
            'password' => bcrypt('password123'),
            'nomor_kwh' => '549998887776',
            'nama_pelanggan' => 'Pelanggan Lain',
            'alamat' => 'Jl. Raya Bogor No. 2',
            'id_tarif' => $tarif->id_tarif,
        ]);

        // Periode 1: Sudah Lunas
        $penggunaan1 = Penggunaan::create([
            'id_pelanggan' => $this->pelanggan->id_pelanggan,
            'bulan' => 4,
            'tahun' => 2026,
            'meter_awal' => 100,
            'meter_akhir' => 200,
        ]);

        $this->tagihanLunas = Tagihan::create([
            'id_penggunaan' => $penggunaan1->id_penggunaan,
            'id_pelanggan' => $this->pelanggan->id_pelanggan,
            'bulan' => 4,
            'tahun' => 2026,
            'jumlah_meter' => 100,
            'tarif_per_kwh' => 1500,
            'total_tagihan' => 150000,
            'tanggal_jatuh_tempo' => Carbon::create(2026, 4, 20),
            'status' => 'lunas',
        ]);

        Pembayaran::create([
            'id_tagihan' => $this->tagihanLunas->id_tagihan,
            'id_pelanggan' => $this->pelanggan->id_pelanggan,
            'tanggal_pembayaran' => '2026-04-15',
            'bulan_bayar' => 'April 2026',
            'biaya_admin' => 2500,
            'total_bayar' => 152500,
            'status' => 'berhasil',
            'id_user' => $admin->id_user,
        ]);

        // Periode 2: Belum Lunas
        $penggunaan2 = Penggunaan::create([
            'id_pelanggan' => $this->pelanggan->id_pelanggan,
            'bulan' => 5,
            'tahun' => 2026,
            'meter_awal' => 200,
            'meter_akhir' => 320,
        ]);

        $this->tagihanBelumLunas = Tagihan::create([
            'id_penggunaan' => $penggunaan2->id_penggunaan,
            'id_pelanggan' => $this->pelanggan->id_pelanggan,
            'bulan' => 5,
            'tahun' => 2026,
            'jumlah_meter' => 120,
            'tarif_per_kwh' => 1500,
            'total_tagihan' => 180000,
            'tanggal_jatuh_tempo' => Carbon::create(2026, 5, 20),
            'status' => 'belum_lunas',
        ]);
    }

    public function test_pelanggan_dapat_mengakses_halaman_tagihan_penggunaan_dan_tarif(): void
    {
        $this->actingAs($this->pelanggan, 'pelanggan')
            ->get(route('pelanggan.tagihan'))
            ->assertOk();

        $this->actingAs($this->pelanggan, 'pelanggan')
            ->get(route('pelanggan.penggunaan'))
            ->assertOk();

        $this->actingAs($this->pelanggan, 'pelanggan')
            ->get(route('pelanggan.tarif'))
            ->assertOk();
    }

    public function test_pelanggan_dapat_mencetak_struk_tagihan_lunas_miliknya_sendiri(): void
    {
        $this->actingAs($this->pelanggan, 'pelanggan')
            ->get(route('pelanggan.tagihan.struk', $this->tagihanLunas->id_tagihan))
            ->assertOk();
    }

    public function test_pelanggan_tidak_dapat_mencetak_struk_tagihan_belum_lunas_atau_milik_orang_lain(): void
    {
        // Tagihan milik sendiri tetapi belum lunas -> ditolak
        $responseBelumLunas = $this->actingAs($this->pelanggan, 'pelanggan')
            ->get(route('pelanggan.tagihan.struk', $this->tagihanBelumLunas->id_tagihan));

        $this->assertTrue(in_array($responseBelumLunas->getStatusCode(), [302, 403, 404]));

        // Pelanggan lain mencoba mengakses struk tagihan lunas milik Hisyam -> ditolak (404/403)
        $responseOrangLain = $this->actingAs($this->pelangganLain, 'pelanggan')
            ->get(route('pelanggan.tagihan.struk', $this->tagihanLunas->id_tagihan));

        $this->assertTrue(in_array($responseOrangLain->getStatusCode(), [302, 403, 404]));
    }
}
