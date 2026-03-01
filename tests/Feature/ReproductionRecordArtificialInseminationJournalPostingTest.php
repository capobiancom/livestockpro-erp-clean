<?php

namespace Tests\Feature;

use App\Models\Animal;
use App\Models\ArtificialInsemination;
use App\Models\ChartOfAccount;
use App\Models\Farm;
use App\Models\JournalEntry;
use App\Models\ReproductionRecord;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Gate;
use Tests\TestCase;

class ReproductionRecordArtificialInseminationJournalPostingTest extends TestCase
{
    use RefreshDatabase;

    private function seedAccounts(Farm $farm, User $user): void
    {
        foreach (
            [
                ['code' => '5012', 'name' => 'Artificial Insemination Expense'],
                ['code' => '1008', 'name' => 'Medicine Inventory'],
            ] as $acc
        ) {
            ChartOfAccount::create([
                'farm_id' => $farm->id,
                'user_id' => $user->id,
                'code' => $acc['code'],
                'name' => $acc['name'],
                'type' => 'asset',
                'parent_id' => null,
                'is_system' => true,
                'is_active' => true,
            ]);
        }
    }

    public function test_store_ai_reproduction_record_posts_journal_entry(): void
    {
        $user = User::factory()->create();
        $farm = Farm::factory()->create();
        $user->farm_id = $farm->id;
        $user->save();

        $this->seedAccounts($farm, $user);

        $animal = Animal::factory()->create(['farm_id' => $farm->id]);

        Gate::before(fn() => true);

        $payload = [
            'animal_id' => $animal->id,
            'event' => 'artificial_insemination',
            'partner_id' => null,
            'event_date' => now()->toDateString(),
            'outcome' => 'pending',
            'notes' => null,
            'heat_stage' => 'optimal',
            'performed_by' => $user->id,
            'semen_batch_no' => 'B-1',
            'breed_id' => 1,
            'insemination_date' => now()->toDateString(),
            'vet_id' => $user->id,
            'cost' => 250,
            'remarks' => 'test',
        ];

        /** @var \App\Models\User $user */
        $this->actingAs($user)
            ->post(route('reproduction-records.store'), $payload)
            ->assertRedirect(route('reproduction-records.index'));

        $record = ReproductionRecord::firstOrFail();
        $this->assertSame('artificial_insemination', $record->event);

        $ai = ArtificialInsemination::firstOrFail();
        $this->assertSame((float) 250, (float) $ai->cost);

        $je = JournalEntry::where('reference_type', 'reproduction_record')
            ->where('reference_id', $record->id)
            ->firstOrFail();

        $this->assertCount(2, $je->lines);

        $expense = ChartOfAccount::where('code', '5012')->firstOrFail();
        $inv = ChartOfAccount::where('code', '1008')->firstOrFail();

        $this->assertTrue($je->lines->contains(fn($l) => (int) $l->account_id === $expense->id && (float) $l->debit_amount === 250.0 && (float) $l->credit_amount === 0.0));
        $this->assertTrue($je->lines->contains(fn($l) => (int) $l->account_id === $inv->id && (float) $l->credit_amount === 250.0 && (float) $l->debit_amount === 0.0));
    }

    public function test_update_ai_cost_posts_delta_journal_entry(): void
    {
        $user = User::factory()->create();
        $farm = Farm::factory()->create();
        $user->farm_id = $farm->id;
        $user->save();

        $this->seedAccounts($farm, $user);

        $animal = Animal::factory()->create(['farm_id' => $farm->id]);

        $record = ReproductionRecord::create([
            'farm_id' => $farm->id,
            'user_id' => $user->id,
            'animal_id' => $animal->id,
            'event' => 'artificial_insemination',
            'event_date' => now()->toDateString(),
            'performed_by' => $user->id,
        ]);

        $ai = ArtificialInsemination::create([
            'farm_id' => $farm->id,
            'user_id' => $user->id,
            'reproduction_record_id' => $record->id,
            'semen_batch_no' => 'B-1',
            'breed_id' => 1,
            'insemination_date' => now()->toDateString(),
            'vet_id' => $user->id,
            'cost' => 100,
        ]);

        $record->artificial_insemination_id = $ai->id;
        $record->save();

        // Seed initial journal entry to simulate store behavior
        $expense = ChartOfAccount::where('code', '5012')->firstOrFail();
        $inv = ChartOfAccount::where('code', '1008')->firstOrFail();

        $je0 = JournalEntry::create([
            'farm_id' => $farm->id,
            'user_id' => $user->id,
            'entry_date' => $record->event_date,
            'reference_type' => 'reproduction_record',
            'reference_id' => $record->id,
            'description' => 'seed',
            'status' => 'posted',
            'created_by' => $user->id,
        ]);

        $je0->lines()->createMany([
            ['account_id' => $expense->id, 'debit_amount' => 100, 'credit_amount' => 0, 'narration' => 'seed'],
            ['account_id' => $inv->id, 'debit_amount' => 0, 'credit_amount' => 100, 'narration' => 'seed'],
        ]);

        Gate::before(fn() => true);

        $payload = [
            'animal_id' => $animal->id,
            'event' => 'artificial_insemination',
            'partner_id' => null,
            'event_date' => now()->toDateString(),
            'outcome' => 'pending',
            'notes' => null,
            'heat_stage' => 'optimal',
            'performed_by' => $user->id,
            'semen_batch_no' => 'B-1',
            'breed_id' => 1,
            'insemination_date' => now()->toDateString(),
            'vet_id' => $user->id,
            'cost' => 160,
            'remarks' => 'test',
        ];

        /** @var \App\Models\User $user */
        $this->actingAs($user)
            ->put(route('reproduction-records.update', $record), $payload)
            ->assertRedirect(route('reproduction-records.index'));

        $jes = JournalEntry::where('reference_type', 'reproduction_record')
            ->where('reference_id', $record->id)
            ->orderBy('id')
            ->get();

        // 1 seeded + 1 adjustment
        $this->assertCount(2, $jes);

        $adj = $jes->last();

        $this->assertTrue($adj->lines->contains(fn($l) => (int) $l->account_id === $expense->id && (float) $l->debit_amount === 60.0 && (float) $l->credit_amount === 0.0));
        $this->assertTrue($adj->lines->contains(fn($l) => (int) $l->account_id === $inv->id && (float) $l->credit_amount === 60.0 && (float) $l->debit_amount === 0.0));
    }
}
