<?php

namespace Tests\Feature;

use App\Models\Pembayaran;
use App\Models\Pelanggan;
use App\Models\Penggunaan;
use App\Models\Tagihan;
use App\Models\User;
use App\Models\Level;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class DashboardControllerTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function admin_can_access_dashboard_and_see_summary_data()
    {
        $level = Level::factory()->create(['nama_level' => 'admin']);
        $admin = User::factory()->create([
            'id_level' => $level->id_level,
            'password' => Hash::make('password')
        ]);

        // Data dummy
        Pelanggan::factory()->count(2)->create();
        Penggunaan::factory()->count(3)->create();
        Tagihan::factory()->count(4)->create();
        Tagihan::factory()->create(['status' => 'belum_lunas']);
        Pembayaran::factory()->count(5)->create();

        $this->actingAs($admin);

        $response = $this->get('/dashboard');

        $response->assertStatus(200);
        $response->assertViewIs('dashboard.admin');
        $response->assertViewHasAll([
            'totalPelanggan',
            'totalPenggunaan',
            'totalTagihan',
            'totalPembayaran',
            'tagihanBelumLunas'
        ]);
    }

    /** @test */
    public function pelanggan_can_access_dashboard_and_see_data()
    {
        $pelanggan = Pelanggan::factory()->create([
            'password' => Hash::make('password')
        ]);

        Penggunaan::factory()->count(3)->create([
            'id_pelanggan' => $pelanggan->id_pelanggan
        ]);

        Tagihan::factory()->count(4)->create([
            'id_pelanggan' => $pelanggan->id_pelanggan
        ]);

        Tagihan::factory()->create([
            'id_pelanggan' => $pelanggan->id_pelanggan,
            'status' => 'belum_lunas'
        ]);

        $this->actingAs($pelanggan, 'pelanggan');

        $response = $this->get('/customer/dashboard');

        $response->assertStatus(200);
        $response->assertViewIs('dashboard.pelanggan');
        $response->assertViewHasAll([
            'pelanggan',
            'penggunaan',
            'tagihan',
            'tagihanBelumLunas'
        ]);
    }
}
