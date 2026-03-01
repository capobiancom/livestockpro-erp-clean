<?php

namespace Tests\Feature;

use App\Models\Animal;
use App\Models\ChartOfAccount;
use App\Models\DiseaseTreatment;
use App\Models\EventType;
use App\Models\Farm;
use App\Models\HealthEvent;
use App\Models\HealthIssue;
use App\Models\Medicine;
use App\Models\StockMovement;
use App\Models\Treatment;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class DiseaseTreatmentJournalPostingUpdateTest extends TestCase
{
    use RefreshDatabase;

    public function test_updating_disease_treatment_add_remove_medicine_adjusts_journal_posting(): void
    {
        $this->seed();

        $farm = Farm::query()->firstOrFail();

        $user = User::factory()->create([
            'farm_id' => $farm->id,
        ]);
        $user->assignRole('farm owner');

        // Ensure required accounts exist for this farm
        ChartOfAccount::query()->firstOrCreate(
            ['farm_id' => $farm->id, 'code' => '5002'],
            ['name' => 'Medicine Expense', 'type' => 'expense', 'user_id' => $user->id]
        );
        ChartOfAccount::query()->firstOrCreate(
            ['farm_id' => $farm->id, 'code' => '1008'],
            ['name' => 'Medicine Inventory', 'type' => 'asset', 'user_id' => $user->id]
        );

        $animal = Animal::query()->create([
            'farm_id' => $farm->id,
            'user_id' => $user->id,
            'tag' => 'T-0001',
            'animal_type' => 'cow',
            'sex' => 'female',
            'status' => 'active',
        ]);

        $healthIssue = HealthIssue::query()->create([
            'farm_id' => $farm->id,
            'animal_id' => $animal->id,
            'name' => 'Test Health Issue',
            'status' => 'open',
            'user_id' => $user->id,
        ]);

        $treatment = Treatment::query()->create([
            'farm_id' => $farm->id,
            'name' => 'Test Treatment',
        ]);

        $med1 = Medicine::query()->create([
            'farm_id' => $farm->id,
            'name' => 'Test Med 1',
            'unit' => 'pcs',
        ]);

        $med2 = Medicine::query()->create([
            'farm_id' => $farm->id,
            'name' => 'Test Med 2',
            'unit' => 'pcs',
        ]);

        // Seed stock (IN) so treatment consumption can succeed
        StockMovement::query()->create([
            'farm_id' => $farm->id,
            'item_type' => Medicine::class,
            'item_id' => $med1->id,
            'movement_type' => 'in',
            'source_type' => 'test',
            'source_event_type' => 'purchase',
            'source_id' => 1,
            'quantity' => 100,
            'unit_cost' => 10,
            'batch_no' => 'B-001',
            'expiry_date' => now()->addYear(),
            'movement_date' => now(),
            'user_id' => $user->id,
        ]);

        StockMovement::query()->create([
            'farm_id' => $farm->id,
            'item_type' => Medicine::class,
            'item_id' => $med2->id,
            'movement_type' => 'in',
            'source_type' => 'test',
            'source_event_type' => 'purchase',
            'source_id' => 2,
            'quantity' => 100,
            'unit_cost' => 20,
            'batch_no' => 'B-002',
            'expiry_date' => now()->addYear(),
            'movement_date' => now(),
            'user_id' => $user->id,
        ]);

        $eventType = EventType::query()->create([
            'farm_id' => $farm->id,
            'name' => 'Test Event Type',
        ]);

        $healthEvent = HealthEvent::query()->create([
            'farm_id' => $farm->id,
            'health_issue_id' => $healthIssue->id,
            'animal_id' => $animal->id,
            'event_type_id' => $eventType->id,
            'title' => 'Test Event',
            'occurred_at' => now(),
            'user_id' => $user->id,
        ]);

        // Create treatment with med1 only: total 2 * 10 = 20
        $createPayload = [
            'health_issue_id' => $healthIssue->id,
            'health_event_id' => $healthEvent->id,
            'treatment_id' => $treatment->id,
            'description' => null,
            'resolved_at' => null,
            'status' => 'planned',
            'notes' => null,
            'disease_treatment_medications' => [
                [
                    'medicine_id' => $med1->id,
                    'dose' => null,
                    'frequency' => null,
                    'duration_days' => 0,
                    'status' => 'planned',
                    'started_at' => now()->toDateString(),
                    'ended_at' => null,
                    'qty' => 2,
                    'unit_cost' => 10,
                    'total_cost' => 20,
                    'notes' => null,
                ],
            ],
        ];

        $this->actingAs($user)
            ->post(route('disease-treatments.store'), $createPayload)
            ->assertRedirect(route('disease-treatments.index'));

        $dt = DiseaseTreatment::query()->where('farm_id', $farm->id)->latest('id')->firstOrFail();
        $dt->load('diseaseTreatmentMedications');

        $med1LineId = $dt->diseaseTreatmentMedications->first()->id;

        // Update: remove med1, add med2 (qty 1 * 20 = 20) => delta should be 0, so no adjustment entry
        $updatePayload = [
            'health_issue_id' => $healthIssue->id,
            'health_event_id' => $healthEvent->id,
            'treatment_id' => $treatment->id,
            'description' => null,
            'resolved_at' => null,
            'status' => 'planned',
            'notes' => null,
            'disease_treatment_medications' => [
                [
                    'medicine_id' => $med2->id,
                    'dose' => null,
                    'frequency' => null,
                    'duration_days' => 0,
                    'status' => 'planned',
                    'started_at' => now()->toDateString(),
                    'ended_at' => null,
                    'qty' => 1,
                    'unit_cost' => 20,
                    'total_cost' => 20,
                    'notes' => null,
                ],
            ],
        ];

        $this->actingAs($user)
            ->put(route('disease-treatments.update', $dt), $updatePayload)
            ->assertRedirect(route('disease-treatments.index'));

        // Ensure med1 medication row is deleted
        $this->assertDatabaseMissing('disease_treatment_medications', [
            'id' => $med1LineId,
        ]);

        // Ensure there is still only 1 journal entry for this treatment (the original posting)
        $entriesCount = DB::table('journal_entries')
            ->where('farm_id', $farm->id)
            ->where('reference_type', 'disease_treatment')
            ->where('reference_id', $dt->id)
            ->count();

        $this->assertSame(1, $entriesCount);
    }
}
