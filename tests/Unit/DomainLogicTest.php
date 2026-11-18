<?php

namespace Tests\Unit;

use App\Models\Level;
use App\Models\Tagihan;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DomainLogicTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_is_administrator_hanya_true_untuk_level_administrator(): void
    {
        $levelAdmin = Level::create(['nama_level' => 'Administrator']);
        $levelKasir = Level::create(['nama_level' => 'Petugas Loket']);

        $admin = User::create([
            'username' => 'admin_test',
            'password' => bcrypt('password'),
            'nama_admin' => 'Admin Test',
            'id_level' => $levelAdmin->id_level,
        ]);

        $kasir = User::create([
            'username' => 'kasir_test',
            'password' => bcrypt('password'),
            'nama_admin' => 'Kasir Test',
            'id_level' => $levelKasir->id_level,
        ]);

        $this->assertTrue($admin->isAdministrator());
        $this->assertFalse($kasir->isAdministrator());
    }

    public function test_tagihan_menghitung_denda_dan_status_terlambat_setelah_jatuh_tempo(): void
    {
        Carbon::setTestNow(Carbon::create(2026, 5, 25));

        $tagihanTelat = new Tagihan([
            'bulan' => 5,
            'tahun' => 2026,
            'jumlah_meter' => 100,
            'tarif_per_kwh' => 1500,
            'total_tagihan' => 150000,
            'tanggal_jatuh_tempo' => Carbon::create(2026, 5, 20),
            'status' => 'belum_lunas',
        ]);

        $this->assertTrue($tagihanTelat->isTerlambat());
        $this->assertGreaterThan(0, $tagihanTelat->estimasiDenda());
        $this->assertEquals('terlambat', $tagihanTelat->status_tampil);

        Carbon::setTestNow();
    }
}
