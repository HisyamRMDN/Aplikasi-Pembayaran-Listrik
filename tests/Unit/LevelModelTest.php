<?php

namespace Tests\Unit;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Models\Level;
use App\Models\User;

class LevelModelTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function test_level_can_be_created()
    {
        $level = Level::factory()->create([
            'nama_level' => 'admin'
        ]);

        $this->assertDatabaseHas('levels', [
            'id_level' => $level->id_level,
            'nama_level' => 'admin'
        ]);
    }

    public function test_level_has_many_users()
    {
        $level = Level::factory()->create();

        User::factory()->count(3)->create([
            'id_level' => $level->id_level
        ]);

        $this->assertCount(3, $level->users);
    }
}
