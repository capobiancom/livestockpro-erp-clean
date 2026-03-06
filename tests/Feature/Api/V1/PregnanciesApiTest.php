<?php

namespace Tests\Feature\Api\V1;

use App\Models\Animal;
use App\Models\Pregnancy;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class PregnanciesApiTest extends TestCase
{
    use RefreshDatabase;

    public function test_index_requires_authentication(): void
    {
        $this->getJson('/api/v1/pregnancies')
            ->assertStatus(401);
    }

    public function test_farm_owner_can_crud_pregnancy_scoped_to_their_farm(): void
    {
        $this->seed();

        /** @var User $user */
        $user = User::factory()->create(['farm_id' => 1]);
        $user->assignRole('farm owner');
        Sanctum::actingAs($user);

        // Create an animal in the user's farm to reference
        $animal = Animal::create([
            'farm_id' => 1,
            'user_id' => $user->id,
            'tag' => 'A-100',
            'name' => 'Cow 1',
            'type' => 'cow',
            'status' => 'active',
        ]);

        // Create
        $create = $this->postJson('/api/v1/pregnancies', [
            'animal_id' => $animal->id,
            'pregnancy_confirmed_date' => '2026-01-01',
            'expected_gestation_days' => 280,
            'expected_calving_date' => '2026-10-08',
            'pregnancy_status' => 'confirmed',
            'health_notes' => 'ok',
        ])->assertStatus(201);

        $pregnancyId = $create->json('data.id');
        $this->assertNotNull($pregnancyId);

        // Show
        $this->getJson("/api/v1/pregnancies/{$pregnancyId}")
            ->assertOk()
            ->assertJsonPath('data.farm_id', 1)
            ->assertJsonPath('data.animal_id', $animal->id);

        // Update
        $this->patchJson("/api/v1/pregnancies/{$pregnancyId}", [
            'health_notes' => 'updated',
            'farm_id' => 999,
            'user_id' => 999,
        ])->assertOk()
            ->assertJsonPath('data.health_notes', 'updated')
            ->assertJsonPath('data.farm_id', 1);

        // Index filters
        $this->getJson('/api/v1/pregnancies?animal_id=' . $animal->id)
            ->assertOk()
            ->assertJsonFragment(['id' => $pregnancyId]);

        // Delete
        $this->deleteJson("/api/v1/pregnancies/{$pregnancyId}")
            ->assertOk()
            ->assertJsonPath('message', 'Deleted.');

        $this->assertSoftDeleted('pregnancies', ['id' => $pregnancyId]);
    }

    public function test_farm_owner_cannot_access_other_farms_pregnancy(): void
    {
        $this->seed();

        /** @var User $user */
        $user = User::factory()->create(['farm_id' => 1]);
        $user->assignRole('farm owner');
        Sanctum::actingAs($user);

        $otherPregnancy = Pregnancy::create([
            'farm_id' => 2,
            'animal_id' => 1,
            'user_id' => $user->id,
            'pregnancy_status' => 'confirmed',
        ]);

        $this->getJson("/api/v1/pregnancies/{$otherPregnancy->id}")
            ->assertForbidden();

        $this->patchJson("/api/v1/pregnancies/{$otherPregnancy->id}", [
            'health_notes' => 'hack',
        ])->assertForbidden();

        $this->deleteJson("/api/v1/pregnancies/{$otherPregnancy->id}")
            ->assertForbidden();
    }
}
