<?php

namespace Tests\Feature;

use App\Models\Animal;
use App\Models\Pregnancy;
use App\Models\ReproductionRecord;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

class ConceptionSuccessRateReportTest extends TestCase
{
    use RefreshDatabase;

    public function test_report_shows_data_after_seeding(): void
    {
        $this->seed();

        // log in as the farm owner that DatabaseSeeder creates (if any)
        $user = User::where('email', 'farmowner@livestockproerp.com')->first();
        if (!$user) {
            $user = User::first();
        }
        // ensure the policy check passes even if permission is missing
        $user->assignRole('farm owner');
        $this->actingAs($user);

        // make sure at least one reproduction attempt exists for the user's farm
        $farmId = $user->farm_id;
        $attempt = ReproductionRecord::query()
            ->when($farmId, fn($q) => $q->where('farm_id', $farmId))
            ->first();

        $this->assertNotNull($attempt, 'No reproduction attempts were seeded');

        $response = $this->get(route('reports.conception-success-rate.index'));
        $response->assertStatus(200);

        $response->assertInertia(
            fn($page) =>
            // summary values should exist and we should have at least one row
            $page->has('summary.total_attempts')
                ->has('summary.conception_success_rate')
                // at least one row in the result set
                ->has('rows.0')
        );
    }
}
