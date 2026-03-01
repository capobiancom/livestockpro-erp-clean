<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Permission;
use App\Models\User;

class BreedStepTest extends TestCase
{
    use RefreshDatabase;

    public function test_steps()
    {
        $this->seed();
        echo "AFTER SEED\n";

        $perm = Permission::firstOrCreate(['name' => 'breeds.create']);
        echo "AFTER PERM\n";

        $user = User::factory()->create();
        echo "AFTER USER\n";

        $user->givePermissionTo($perm);
        echo "AFTER GIVE\n";

        $this->actingAs($user);
        echo "AFTER ACT\n";

        // Now hit the route
        echo "BEFORE POST\n";
        $response = $this->post(route('breeds.store'), [
            'name' => 'Test Breed',
            'origin' => 'local',
            'animal_type' => 'cow',
        ]);
        echo "AFTER POST\n";

        $response->assertRedirect();
        $this->assertDatabaseHas('breeds', ['name' => 'Test Breed']);
    }
}
