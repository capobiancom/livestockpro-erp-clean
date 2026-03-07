<?php

namespace Tests\Feature;

use App\Models\Animal;
use App\Models\Pregnancy;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

class PregnancyLossAnalysisReportTest extends TestCase
{
    use RefreshDatabase;

    public function test_index_survives_missing_loss_columns(): void
    {
        $this->seed();

        $user = User::factory()->create();
        $user->givePermissionTo('pregnancies.pregnancyLossAnalysis');

        $farm = $user->farm_id ? null : \App\Models\Farm::factory()->create();
        $animal = Animal::factory()->create([
            'farm_id' => $user->farm_id ?? $farm->id,
        ]);

        Pregnancy::query()->create([
            'animal_id' => $animal->id,
            'farm_id' => $animal->farm_id,
            'reproduction_record_id' => \App\Models\ReproductionRecord::factory()->create([
                'farm_id' => $animal->farm_id,
                'animal_id' => $animal->id,
            ])->id,
            'pregnancy_confirmed_date' => now()->subDays(10)->toDateString(),
            'pregnancy_status' => 'lost',
            'expected_gestation_days' => 283,
            'expected_calving_date' => now()->addDays(273)->toDateString(),
        ]);

        // drop loss-related columns if present to simulate old schema
        foreach (['abortion_date', 'embryonic_death_date', 'miscarriage_date'] as $col) {
            if (Schema::hasColumn('pregnancies', $col)) {
                Schema::table('pregnancies', function ($table) use ($col) {
                    $table->dropColumn($col);
                });
            }
        }

        $this->actingAs($user);

        // Ensure the request is scoped to this user's farm (FarmScope uses session farm_id first).
        session(['farm_id' => $animal->farm_id]);

        $response = $this->get(route('reports.pregnancy-loss-analysis.index'));
        $response->assertStatus(200);

        $response->assertInertia(
            fn($page) =>
            $page->where('summary.total_confirmed_pregnancies', 1)
        );
    }
}
