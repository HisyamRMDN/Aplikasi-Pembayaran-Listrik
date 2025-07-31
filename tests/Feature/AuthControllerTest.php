<?php

namespace Tests\Feature;

use App\Models\Pelanggan;
use App\Models\User;
use App\Models\Level;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class AuthControllerTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function admin_can_login_with_correct_credentials()
    {
        $level = Level::factory()->create(['nama_level' => 'admin']);

        $user = User::factory()->create([
            'username' => 'adminuser',
            'password' => Hash::make('password'),
            'id_level' => $level->id_level,
        ]);

        $response = $this->post('/login', [
            'username' => 'adminuser',
            'password' => 'password',
            'login_type' => 'admin'
        ]);

        $response->assertRedirect('/dashboard');
        $this->assertAuthenticatedAs($user);
    }

    /** @test */
    public function pelanggan_can_login_with_correct_credentials()
    {
        $pelanggan = Pelanggan::factory()->create([
            'username' => 'pelangganuser',
            'password' => Hash::make('password'),
        ]);

        $response = $this->post('/login', [
            'username' => 'pelangganuser',
            'password' => 'password',
            'login_type' => 'pelanggan'
        ]);

        $response->assertRedirect('/customer/dashboard');
        $this->assertAuthenticated('pelanggan');
    }

    /** @test */
    public function login_fails_with_wrong_credentials()
    {
        $response = $this->post('/login', [
            'username' => 'wronguser',
            'password' => 'wrongpass',
            'login_type' => 'admin'
        ]);

        $response->assertSessionHasErrors('username');
        $this->assertGuest();
    }

    /** @test */
    public function admin_can_logout_successfully()
    {
        $user = User::factory()->create([
            'password' => Hash::make('password')
        ]);

        $this->actingAs($user);

        $response = $this->post('/logout');

        $response->assertRedirect('/');
        $this->assertGuest();
    }

    /** @test */
    public function pelanggan_can_logout_successfully()
    {
        $pelanggan = Pelanggan::factory()->create([
            'password' => Hash::make('password')
        ]);

        $this->actingAs($pelanggan, 'pelanggan');

        $response = $this->post('/pelanggan/logout');

        $response->assertRedirect('/');
        $this->assertGuest('pelanggan');
    }
}
