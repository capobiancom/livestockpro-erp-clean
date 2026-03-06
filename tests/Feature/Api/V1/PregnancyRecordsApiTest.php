<?php

namespace Tests\Feature\Api\V1;

use App\Models\Animal;
use App\Models\Pregnancy;
use App\Models\PregnancyCheckup;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class PregnancyRecordsApiTest extends TestCase
{
    use RefreshDatabase;

    public function test_index_requires_authentication(): void
    {
        $this->getJson('/api/v1/pregnancy-records')
            ->assertStatus(401);
    }

    public function test_farm_owner_can_crud_pregnancy_record_scoped_to_their_farm(): void
    {
        $this->seed();

        /** @var User $user */
        $user = User::factory()->create(['farm_id' => 1]);
        $user->assignRole('farm owner');
        Sanctum::actingAs($user);

        $animal = Animal::create([
            'farm_id' => 1,
            'user_id' => $user->id,
            'tag' => 'A-200',
            'name' => 'Cow 2',
            'type' => 'cow',
            'status' => 'active',
        ]);

        $reproductionRecordId = \App\Models\ReproductionRecord::create([
            'animal_id' => $animal->id,
            'event' => 'pregnancy',
            'event_date' => '2026-01-01',
            'farm_id' => 1,
            'user_id' => $user->id,
        ])->id;

        $pregnancy = Pregnancy::create([
            'farm_id' => 1,
            'animal_id' => $animal->id,
            'reproduction_record_id' => $reproductionRecordId,
            'pregnancy_confirmed_date' => '2026-01-01',
            'expected_gestation_days' => 280,
            'expected_calving_date' => '2026-10-08',
            'pregnancy_status' => 'confirmed',
            'health_notes' => 'ok',
            'user_id' => $user->id,
        ]);

        // Create
        $create = $this->postJson('/api/v1/pregnancy-records', [
            'pregnancy_id' => $pregnancy->id,
            'checkup_date' => '2026-02-01',
            'checkup_result' => 'normal',
            'observations' => 'ok',
        ])->assertStatus(201);

        $recordId = $create->json('data.id');
        $this->assertNotNull($recordId);

        // Show
        $this->getJson("/api/v1/pregnancy-records/{$recordId}")
            ->assertOk()
            ->assertJsonPath('data.farm_id', 1)
            ->assertJsonPath('data.pregnancy_id', $pregnancy->id);

        // Update
        $this->patchJson("/api/v1/pregnancy-records/{$recordId}", [
            'observations' => 'updated',
        ])->assertOk()
            ->assertJsonPath('data.observations', 'updated')
            ->assertJsonPath('data.farm_id', 1)
            ->assertJsonPath('data.pregnancy_id', $pregnancy->id);

        // Update rejects tampering
        $this->patchJson("/api/v1/pregnancy-records/{$recordId}", [
            'farm_id' => 999,
            'user_id' => 999,
            'pregnancy_id' => 999,
        ])->assertStatus(422);

        // Index filters
        $this->getJson('/api/v1/pregnancy-records?pregnancy_id=' . $pregnancy->id)
            ->assertOk()
            ->assertJsonFragment(['id' => $recordId]);

        // Delete
        $this->deleteJson("/api/v1/pregnancy-records/{$recordId}")
            ->assertOk()
            ->assertJsonPath('message', 'Deleted.');

        $this->assertDatabaseMissing('pregnancy_checkups', ['id' => $recordId]);
    }

    public function test_farm_owner_cannot_access_other_farms_pregnancy_record(): void
    {
        $this->seed();

        /** @var User $user */
        $user = User::factory()->create(['farm_id' => 1]);
        $user->assignRole('farm owner');
        Sanctum::actingAs($user);

        $otherAnimalId = Animal::create([
            'farm_id' => 2,
            'user_id' => $user->id,
            'tag' => 'A-999',
            'name' => 'Cow X',
            'type' => 'cow',
            'status' => 'active',
        ])->id;

        $otherReproductionRecordId = \App\Models\ReproductionRecord::create([
            'animal_id' => $otherAnimalId,
            'event' => 'pregnancy',
            'event_date' => '2026-01-01',
            'farm_id' => 2,
            'user_id' => $user->id,
        ])->id;

        $other = PregnancyCheckup::create([
            'farm_id' => 2,
            'pregnancy_id' => Pregnancy::create([
                'farm_id' => 2,
                'animal_id' => $otherAnimalId,
                'user_id' => $user->id,
                'pregnancy_status' => 'confirmed',
                'reproduction_record_id' => $otherReproductionRecordId,
                'pregnancy_confirmed_date' => '2026-01-01',
                'expected_calving_date' => '2026-10-08',
            ])->id,
            'user_id' => $user->id,
            'checkup_date' => '2026-02-01',
            'checkup_result' => 'normal',
        ]);

        $this->getJson("/api/v1/pregnancy-records/{$other->id}")
            ->assertForbidden();

        $this->patchJson("/api/v1/pregnancy-records/{$other->id}", [
            'observations' => 'hack',
        ])->assertForbidden();

        $this->deleteJson("/api/v1/pregnancy-records/{$other->id}")
            ->assertForbidden();
    }
}
