<?php

namespace Tests\Feature;

use App\Models\InventoryItem;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

class FeedingCostAnalysisReportTest extends TestCase
{
    use RefreshDatabase;

    public function test_index_works_even_if_inventory_item_name_column_missing(): void
    {
        $this->seed();

        $user = User::factory()->create();
        $user->givePermissionTo('feedings.feedingCostAnalysis');

        // create a dummy item so the report has something to return
        $item = InventoryItem::factory()->create([
            'name' => 'Barley Mash',
            'sku' => 'BAR123',
        ]);

        // simulate an older database by dropping schema columns that might
        // not exist.  We remove names from inventory_items and animals, and
        // also strip the old name/unit/unit_cost fields from feeding_items.
        // this ensures both fallback branches in the controller are visited.
        if (Schema::hasColumn('inventory_items', 'name')) {
            Schema::table('inventory_items', function ($table) {
                $table->dropColumn('name');
            });
        }
        if (Schema::hasColumn('animals', 'name')) {
            Schema::table('animals', function ($table) {
                $table->dropColumn('name');
            });
        }
        if (Schema::hasColumn('feeding_items', 'name')) {
            Schema::table('feeding_items', function ($table) {
                $table->dropColumn('name');
            });
        }
        if (Schema::hasColumn('feeding_items', 'unit')) {
            Schema::table('feeding_items', function ($table) {
                $table->dropColumn('unit');
            });
        }
        if (Schema::hasColumn('feeding_items', 'unit_cost')) {
            Schema::table('feeding_items', function ($table) {
                $table->dropColumn('unit_cost');
            });
        }

        $this->actingAs($user);

        $response = $this->get(route('reports.feeding-cost-analysis.index'));
        $response->assertStatus(200);

        // should render via Inertia and the items array should contain our SKU
        $response->assertInertia(
            fn($page) =>
            $page->where('items.0.sku', $item->sku)
                ->where('items.0.id', $item->id)
        );
    }
}
