<?php

namespace Tests\Feature;

use App\Models\Level;
use App\Models\Pelanggan;
use App\Models\Tarif;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AuthAndRoleAccessTest extends TestCase
{
    use RefreshDatabase;

    public function test_administrator_dapat_mengakses_manajemen_pelanggan_dan_tarif(): void
    {
        $levelAdmin = Level::create(['nama_level' => 'Administrator']);
        $admin = User::create([
            'username' => 'admin_voltpay',
            'password' => bcrypt('password123'),
            'nama_admin' => 'Super Admin',
            'id_level' => $levelAdmin->id_level,
        ]);

        $this->actingAs($admin, 'web')
            ->get(route('pelanggan.index'))
            ->assertOk();

        $this->actingAs($admin, 'web')
            ->get(route('tarif.index'))
            ->assertOk();
    }

    public function test_petugas_loket_ditolak_403_saat_mengakses_halaman_khusus_administrator(): void
    {
        $levelKasir = Level::create(['nama_level' => 'Petugas Loket']);
        $kasir = User::create([
            'username' => 'kasir_voltpay',
            'password' => bcrypt('password123'),
            'nama_admin' => 'Petugas Kasir',
            'id_level' => $levelKasir->id_level,
        ]);

        // Boleh mengakses operasional harian (tagihan & laporan)
        $this->actingAs($kasir, 'web')
            ->get(route('tagihan.index'))
            ->assertOk();

        $this->actingAs($kasir, 'web')
            ->get(route('laporan.index'))
            ->assertOk();

        // Ditolak 403 pada menu khusus Administrator
        $this->actingAs($kasir, 'web')
            ->get(route('pelanggan.index'))
            ->assertForbidden();

        $this->actingAs($kasir, 'web')
            ->get(route('tarif.index'))
            ->assertForbidden();
    }

    public function test_pelanggan_tidak_bisa_mengakses_dashboard_admin_dan_sebaliknya(): void
    {
        $tarif = Tarif::create([
            'daya' => 1300,
            'tarif_per_kwh' => 1444.70,
        ]);

        $pelanggan = Pelanggan::create([
            'username' => 'budi_pelanggan',
            'password' => bcrypt('rahasia123'),
            'nomor_kwh' => '541234567890',
            'nama_pelanggan' => 'Budi Santoso',
            'alamat' => 'Jl. Merdeka No. 10',
            'id_tarif' => $tarif->id_tarif,
        ]);

        // Pelanggan dialihkan ke login admin jika coba buka /dashboard admin
        $this->actingAs($pelanggan, 'pelanggan')
            ->get('/dashboard')
            ->assertRedirect(route('login'));

        // Pelanggan bisa mengakses portal mandiri /customer/dashboard
        $this->actingAs($pelanggan, 'pelanggan')
            ->get(route('pelanggan.dashboard'))
            ->assertOk();
    }

    public function test_login_dibatasi_rate_limiter_setelah_lima_percobaan(): void
    {
        for ($i = 0; $i < 5; $i++) {
            $this->post(route('user.login'), [
                'username' => 'user_tidak_ada',
                'password' => 'salah_password',
            ])->assertStatus(302);
        }

        $this->post(route('user.login'), [
            'username' => 'user_tidak_ada',
            'password' => 'salah_password',
        ])->assertStatus(429);
    }

    public function test_aktivasi_akun_dibatasi_rate_limiter_setelah_lima_percobaan(): void
    {
        for ($i = 0; $i < 5; $i++) {
            $this->post(route('pelanggan.activate.submit'), [
                'nomor_kwh' => '000000000000',
                'username' => 'coba_aktivasi',
                'password' => 'password123',
                'password_confirmation' => 'password123',
            ])->assertStatus(302);
        }

        $this->post(route('pelanggan.activate.submit'), [
            'nomor_kwh' => '000000000000',
            'username' => 'coba_aktivasi',
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ])->assertStatus(429);
    }
}
