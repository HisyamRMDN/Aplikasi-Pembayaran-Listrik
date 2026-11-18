<?php

namespace Tests\Feature;

use App\Models\Level;
use App\Models\Pelanggan;
use App\Models\Pembayaran;
use App\Models\Penggunaan;
use App\Models\Tagihan;
use App\Models\Tarif;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class VoltpayBusinessFlowTest extends TestCase
{
    use RefreshDatabase;

    private User $admin;
    private Tarif $tarif900;
    private Pelanggan $pelanggan;

    protected function setUp(): void
    {
        parent::setUp();

        $level = Level::create(['nama_level' => 'Administrator']);
        $this->withoutExceptionHandling();

        $this->admin = User::create([
            'username' => 'admin_test',
            'password' => Hash::make('password'),
            'nama_admin' => 'Petugas Uji',
            'id_level' => $level->id_level,
        ]);

        $this->tarif900 = Tarif::create([
            'daya' => 900,
            'tarif_per_kwh' => 1352.00,
        ]);

        $this->pelanggan = Pelanggan::create([
            'username' => 'budi_test',
            'password' => Hash::make('password'),
            'nomor_kwh' => '12345678901',
            'nama_pelanggan' => 'Budi Santoso',
            'alamat' => 'Jl. Merdeka No. 10',
            'id_tarif' => $this->tarif900->id_tarif,
        ]);
    }

    public function test_snapshot_tarif_tidak_berubah_saat_master_tarif_diupdate(): void
    {
        $this->actingAs($this->admin);

        // 1. Catat penggunaan 100 kWh pada tarif awal Rp 1.352/kWh -> Total Rp 135.200
        $this->post(route('penggunaan.store'), [
            'id_pelanggan' => $this->pelanggan->id_pelanggan,
            'bulan' => 10,
            'tahun' => 2026,
            'meter_awal' => 1000,
            'meter_akhir' => 1100,
        ])->assertRedirect(route('penggunaan.index'));

        $tagihanLama = Tagihan::first();
        $this->assertEquals(1352.00, (float) $tagihanLama->tarif_per_kwh);
        $this->assertEquals(135200.00, (float) $tagihanLama->total_tagihan);

        // 2. Ubah master tarif menjadi Rp 1.500/kWh
        $this->put(route('tarif.update', $this->tarif900->id_tarif), [
            'daya' => 900,
            'tarif_per_kwh' => 1500.00,
        ])->assertRedirect(route('tarif.index'));

        // 3. Pastikan riwayat perubahan tarif tercatat & tagihan lama tetap Rp 135.200
        $this->assertDatabaseHas('tarif_histories', [
            'id_tarif' => $this->tarif900->id_tarif,
            'tarif_lama' => 1352.00,
            'tarif_baru' => 1500.00,
            'id_user' => $this->admin->id_user,
        ]);

        $tagihanLama->refresh();
        $this->assertEquals(1352.00, (float) $tagihanLama->tarif_per_kwh);
        $this->assertEquals(135200.00, (float) $tagihanLama->total_tagihan);
    }

    public function test_meter_awal_otomatis_terkunci_dari_periode_sebelumnya_dan_tolak_duplikat(): void
    {
        $this->actingAs($this->admin);

        // Bulan 10/2026: meter 1000 -> 1250
        $this->post(route('penggunaan.store'), [
            'id_pelanggan' => $this->pelanggan->id_pelanggan,
            'bulan' => 10,
            'tahun' => 2026,
            'meter_awal' => 1000,
            'meter_akhir' => 1250,
        ]);

        // Cek endpoint JSON meter-terakhir untuk bulan 11/2026
        $this->getJson(route('penggunaan.meter-terakhir', [
            'id_pelanggan' => $this->pelanggan->id_pelanggan,
            'bulan' => 11,
            'tahun' => 2026,
        ]))->assertOk()->assertJson([
            'ada_riwayat' => true,
            'meter_awal' => 1250,
            'periode_sebelumnya' => '10/2026',
        ]);

        // Simulasi bypass form: petugas mengirim meter_awal = 999 (seharusnya dipaksa 1250 oleh server)
        $this->post(route('penggunaan.store'), [
            'id_pelanggan' => $this->pelanggan->id_pelanggan,
            'bulan' => 11,
            'tahun' => 2026,
            'meter_awal' => 999,
            'meter_akhir' => 1400,
        ])->assertRedirect(route('penggunaan.index'));

        $this->assertDatabaseHas('penggunaans', [
            'id_pelanggan' => $this->pelanggan->id_pelanggan,
            'bulan' => 11,
            'tahun' => 2026,
            'meter_awal' => 1250,
            'meter_akhir' => 1400,
        ]);

        // Tolak input ganda pada periode yang sama (11/2026)
        $this->post(route('penggunaan.store'), [
            'id_pelanggan' => $this->pelanggan->id_pelanggan,
            'bulan' => 11,
            'tahun' => 2026,
            'meter_awal' => 1400,
            'meter_akhir' => 1500,
        ])->assertSessionHasErrors('error');
    }

    public function test_input_meter_massal_hanya_menyimpan_baris_yang_diisi(): void
    {
        $this->actingAs($this->admin);

        $pelanggan2 = Pelanggan::create([
            'username' => 'sari_test',
            'password' => Hash::make('password'),
            'nomor_kwh' => '98765432109',
            'nama_pelanggan' => 'Sari Dewi',
            'alamat' => 'Jl. Sudirman No. 5',
            'id_tarif' => $this->tarif900->id_tarif,
        ]);

        $this->post(route('penggunaan.massal.store'), [
            'bulan' => 12,
            'tahun' => 2026,
            'rows' => [
                $this->pelanggan->id_pelanggan => ['meter_awal' => 500, 'meter_akhir' => 650],
                $pelanggan2->id_pelanggan => ['meter_awal' => 0, 'meter_akhir' => null], // dilewati
            ],
        ])->assertRedirect(route('penggunaan.index', ['bulan' => 12, 'tahun' => 2026]));

        $this->assertDatabaseHas('penggunaans', [
            'id_pelanggan' => $this->pelanggan->id_pelanggan,
            'bulan' => 12,
            'tahun' => 2026,
            'meter_akhir' => 650,
        ]);

        // Jatuh tempo Desember 2026 otomatis lintas tahun ke 20 Januari 2027
        $this->assertDatabaseHas('tagihans', [
            'id_pelanggan' => $this->pelanggan->id_pelanggan,
            'bulan' => 12,
            'tahun' => 2026,
            'tanggal_jatuh_tempo' => '2027-01-20',
        ]);

        $this->assertDatabaseMissing('penggunaans', [
            'id_pelanggan' => $pelanggan2->id_pelanggan,
            'bulan' => 12,
            'tahun' => 2026,
        ]);
    }

    public function test_jatuh_tempo_denda_keterlambatan_dan_pembatalan_pembayaran(): void
    {
        $this->actingAs($this->admin);
        config(['voltpay.denda_keterlambatan' => 10000]);

        // Buat tagihan periode Oktober 2026 (jatuh tempo: 2026-11-20), 100 kWh = Rp 135.200
        $this->post(route('penggunaan.store'), [
            'id_pelanggan' => $this->pelanggan->id_pelanggan,
            'bulan' => 10,
            'tahun' => 2026,
            'meter_awal' => 100,
            'meter_akhir' => 200,
        ]);

        $tagihan = Tagihan::first();
        $this->assertEquals('2026-11-20', $tagihan->tanggal_jatuh_tempo->toDateString());

        // Proses pembayaran melewati jatuh tempo (25 November 2026) + biaya admin Rp 2.500
        $this->post(route('pembayaran.store'), [
            'id_tagihan' => $tagihan->id_tagihan,
            'tanggal_pembayaran' => '2026-11-25',
            'biaya_admin' => 2500,
        ])->assertRedirect(route('pembayaran.index'));

        $tagihan->refresh();
        $pembayaran = Pembayaran::first();

        // Total bayar = 135.200 (pokok) + 10.000 (denda) + 2.500 (admin) = 147.700
        $this->assertEquals('lunas', $tagihan->status);
        $this->assertEquals(10000.00, (float) $tagihan->denda);
        $this->assertEquals(147700.00, (float) $pembayaran->total_bayar);

        // Pastikan penggunaan yang sudah lunas terkunci dari edit/hapus
        $this->delete(route('penggunaan.destroy', $tagihan->id_penggunaan))
            ->assertSessionHas('error');
        $this->assertDatabaseHas('penggunaans', ['id_penggunaan' => $tagihan->id_penggunaan]);

        // Batalkan pembayaran -> jejak tetap ada dengan status 'batal' & tagihan kembali 'belum_lunas'
        $this->delete(route('pembayaran.destroy', $pembayaran->id_pembayaran), [
            'alasan' => 'Salah input tanggal transaksi',
        ])->assertRedirect(route('pembayaran.index'));

        $pembayaran->refresh();
        $tagihan->refresh();

        $this->assertEquals('batal', $pembayaran->status);
        $this->assertEquals('Salah input tanggal transaksi', $pembayaran->alasan_batal);
        $this->assertEquals($this->admin->id_user, $pembayaran->id_user_batal);
        $this->assertEquals('belum_lunas', $tagihan->status);
        $this->assertEquals(0.00, (float) $tagihan->denda);
    }
}
