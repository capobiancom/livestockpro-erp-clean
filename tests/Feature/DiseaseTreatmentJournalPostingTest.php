<?php

namespace Tests\Feature;

use App\Models\ChartOfAccount;
use App\Models\DiseaseTreatment;
use App\Models\DiseaseTreatmentMedication;
use App\Models\Animal;
use App\Models\Farm;
use App\Models\HealthEvent;
use App\Models\HealthIssue;
use App\Models\EventType;
use App\Models\Medicine;
use App\Models\StockMovement;
use App\Models\Treatment;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class DiseaseTreatmentJournalPostingTest extends TestCase
{
    use RefreshDatabase;

    public function test_creating_disease_treatment_with_medicine_posts_journal_entry(): void
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

        $medicine = Medicine::query()->create([
            'farm_id' => $farm->id,
            'name' => 'Test Med',
            'unit' => 'pcs',
        ]);

        // Seed stock (IN) so treatment consumption can succeed
        StockMovement::query()->create([
            'farm_id' => $farm->id,
            'item_type' => Medicine::class,
            'item_id' => $medicine->id,
            'movement_type' => 'in',
            'source_type' => 'test',
            'source_event_type' => 'purchase',
            'source_id' => 1,
            'quantity' => 10,
            'unit_cost' => 10,
            'batch_no' => 'B-001',
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

        $payload = [
            'health_issue_id' => $healthIssue->id,
            'health_event_id' => $healthEvent->id,
            'treatment_id' => $treatment->id,
            'description' => null,
            'resolved_at' => null,
            'status' => 'planned',
            'notes' => null,
            'disease_treatment_medications' => [
                [
                    'medicine_id' => $medicine->id,
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
            ->post(route('disease-treatments.store'), $payload)
            ->assertRedirect(route('disease-treatments.index'));

        $dt = DiseaseTreatment::query()->where('farm_id', $farm->id)->latest('id')->firstOrFail();

        $this->assertDatabaseHas('journal_entries', [
            'farm_id' => $farm->id,
            'reference_type' => 'disease_treatment',
            'reference_id' => $dt->id,
            'status' => 'posted',
        ]);

        $entryId = DB::table('journal_entries')
            ->where('farm_id', $farm->id)
            ->where('reference_type', 'disease_treatment')
            ->where('reference_id', $dt->id)
            ->value('id');

        $expenseAccountId = ChartOfAccount::query()->where('farm_id', $farm->id)->where('code', '5002')->value('id');
        $inventoryAccountId = ChartOfAccount::query()->where('farm_id', $farm->id)->where('code', '1008')->value('id');

        $this->assertDatabaseHas('journal_entry_lines', [
            'journal_entry_id' => $entryId,
            'account_id' => $expenseAccountId,
            'debit_amount' => 20,
            'credit_amount' => 0,
        ]);

        $this->assertDatabaseHas('journal_entry_lines', [
            'journal_entry_id' => $entryId,
            'account_id' => $inventoryAccountId,
            'debit_amount' => 0,
            'credit_amount' => 20,
        ]);
    }
}
