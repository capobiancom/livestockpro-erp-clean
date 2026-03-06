<?php

namespace Tests\Feature\Api\V1;

use App\Models\Breed;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class BreedsApiTest extends TestCase
{
    use RefreshDatabase;

    public function test_index_requires_authentication(): void
    {
        $this->getJson('/api/v1/breeds')
            ->assertStatus(401);
    }

    public function test_farm_owner_can_crud_breed_scoped_to_their_farm(): void
    {
        $this->seed();

        /** @var User $user */
        $user = User::factory()->create([
            // Ensure farm scoping in controller applies
            'farm_id' => 1,
        ]);

        // Ensure policy passes: farm owner role
        $user->assignRole('farm owner');

        Sanctum::actingAs($user);

        // Create
        $create = $this->postJson('/api/v1/breeds', [
            'name' => 'Test Breed',
            'code' => 'TB',
            'origin' => 'local',
            'animal_type' => 'cow',
            'characteristics' => ['color' => 'brown'],
        ])->assertStatus(201);

        $breedId = $create->json('data.id');
        $this->assertNotNull($breedId);

        // Show
        $this->getJson("/api/v1/breeds/{$breedId}")
            ->assertOk()
            ->assertJsonPath('data.name', 'Test Breed')
            ->assertJsonPath('data.farm_id', 1);

        // Update (cannot overwrite farm_id/user_id)
        $this->patchJson("/api/v1/breeds/{$breedId}", [
            'name' => 'Updated Breed',
            'farm_id' => 999,
            'user_id' => 999,
        ])->assertOk()
            ->assertJsonPath('data.name', 'Updated Breed')
            ->assertJsonPath('data.farm_id', 1);

        // Index shows only farm breeds
        Breed::create([
            'name' => 'Other Farm Breed',
            'farm_id' => 2,
            'user_id' => $user->id,
        ]);

        $this->getJson('/api/v1/breeds')
            ->assertOk()
            ->assertJsonMissing(['name' => 'Other Farm Breed'])
            ->assertJsonFragment(['name' => 'Updated Breed']);

        // Delete
        $this->deleteJson("/api/v1/breeds/{$breedId}")
            ->assertOk()
            ->assertJsonPath('message', 'Deleted.');

        $this->assertDatabaseMissing('breeds', ['id' => $breedId]);
    }

    public function test_farm_owner_cannot_access_other_farms_breed(): void
    {
        $this->seed();

        /** @var User $user */
        $user = User::factory()->create(['farm_id' => 1]);
        $user->assignRole('farm owner');
        Sanctum::actingAs($user);

        $otherBreed = Breed::create([
            'name' => 'Other Farm Breed',
            'farm_id' => 2,
            'user_id' => $user->id,
        ]);

        $this->getJson("/api/v1/breeds/{$otherBreed->id}")
            ->assertForbidden();

        $this->patchJson("/api/v1/breeds/{$otherBreed->id}", [
            'name' => 'Hack',
        ])->assertForbidden();

        $this->deleteJson("/api/v1/breeds/{$otherBreed->id}")
            ->assertForbidden();
    }
}
