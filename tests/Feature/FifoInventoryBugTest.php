<?php

namespace Tests\Feature;

use App\Models\Farm;
use App\Models\InventoryItem;
use App\Models\Sale;
use App\Models\StockMovement;
use App\Models\User;
use App\Models\ChartOfAccount;
use App\Models\Customer;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Gate;
use Tests\TestCase;

class FifoInventoryBugTest extends TestCase
{
    use \Illuminate\Foundation\Testing\DatabaseTransactions;

    private function seedAccounts(Farm $farm, User $user): void
    {
        foreach ([
            ['code' => '1001', 'name' => 'Cash'],
            ['code' => '1003', 'name' => 'Accounts Receivable'],
            ['code' => '4003', 'name' => 'Inventory SalesItem'],
            ['code' => '4002', 'name' => 'Animal Sales'],
        ] as $acc) {
            ChartOfAccount::create([
                'farm_id' => $farm->id,
                'user_id' => $user->id,
                'code' => $acc['code'],
                'name' => $acc['name'],
                'type' => 'asset',
                'is_system' => true,
                'is_active' => true,
            ]);
        }
    }

    public function test_fifo_logic_fails_when_multiple_in_movements_share_same_batch_no()
    {
        $this->withoutMiddleware();
        $user = User::factory()->create();
        $farm = Farm::factory()->create();
        $user->farm_id = $farm->id;
        $user->save();

        $this->seedAccounts($farm, $user);
        $customer = Customer::factory()->create(['farm_id' => $farm->id]);
        $item = InventoryItem::factory()->create(['farm_id' => $farm->id]);

        // 1. Purchase 1: 100 units, Batch NULL
        StockMovement::create([
            'farm_id' => $farm->id,
            'item_type' => InventoryItem::class,
            'item_id' => $item->id,
            'movement_type' => 'in',
            'source_type' => InventoryItem::class,
            'source_event_type' => 'purchase',
            'source_id' => 1,
            'quantity' => 100,
            'unit_cost' => 10,
            'batch_no' => null,
            'movement_date' => now()->subDays(2)->toDateString(),
            'user_id' => $user->id,
        ]);

        // 2. Purchase 2: 50 units, Batch NULL
        StockMovement::create([
            'farm_id' => $farm->id,
            'item_type' => InventoryItem::class,
            'item_id' => $item->id,
            'movement_type' => 'in',
            'source_type' => InventoryItem::class,
            'source_event_type' => 'purchase',
            'source_id' => 2,
            'quantity' => 50,
            'unit_cost' => 20,
            'batch_no' => null,
            'movement_date' => now()->subDays(1)->toDateString(),
            'user_id' => $user->id,
        ]);

        Gate::before(fn() => true);

        // 3. Sale 1: 110 units.
        // Should consume 100 from P1 and 10 from P2.
        $payload = [
            'customer_id' => $customer->id,
            'invoice_date' => now()->toDateString(),
            'status' => 'unpaid',
            'total_amount' => 1100,
            'paid_amount' => 0,
            'sales_items' => [
                [
                    'item_id' => $item->id,
                    'item_type' => InventoryItem::class,
                    'quantity' => 110,
                    'unit_price' => 10,
                    'total_price' => 1100,
                ],
            ],
        ];

        $response = $this->actingAs($user)->post(route('sales.store'), $payload);

        if ($response->isRedirect()) {
            $response->assertSessionHasNoErrors();
        } else {
            $this->fail("Sale failed with status " . $response->status() . ": " . $response->getContent());
        }

        // Verify total OUT quantity is 110
        $this->assertEquals(110, StockMovement::where('movement_type', 'out')->sum('quantity'));

        // 4. Sale 2: 20 units.
        // Should consume remaining 40 from P2.
        $payload2 = [
            'customer_id' => $customer->id,
            'invoice_date' => now()->toDateString(),
            'status' => 'unpaid',
            'total_amount' => 200,
            'paid_amount' => 0,
            'sales_items' => [
                [
                    'item_id' => $item->id,
                    'item_type' => InventoryItem::class,
                    'quantity' => 20,
                    'unit_price' => 10,
                    'total_price' => 200,
                ],
            ],
        ];

        $response2 = $this->actingAs($user)->post(route('sales.store'), $payload2);
        
        if ($response2->isRedirect()) {
            $response2->assertSessionHasNoErrors();
        } else {
            $this->fail("Second sale failed: " . json_encode(session('errors') ? session('errors')->all() : 'Unknown error'));
        }

        $this->assertEquals(130, StockMovement::where('movement_type', 'out')->sum('quantity'));
    }

    public function test_animal_fifo_logic()
    {
        $this->withoutMiddleware();
        $user = User::factory()->create();
        $farm = Farm::factory()->create();
        $user->farm_id = $farm->id;
        $user->save();

        $this->seedAccounts($farm, $user);
        $customer = Customer::factory()->create(['farm_id' => $farm->id]);
        
        // Use a real animal instead of just factory if possible, but factory should be fine
        $animal = \App\Models\Animal::factory()->create([
            'farm_id' => $farm->id,
            'status' => \App\Enums\AnimalStatus::Active->value
        ]);

        // 1. Purchase Animal Movement: 1 unit, Batch NULL
        StockMovement::create([
            'farm_id' => $farm->id,
            'item_type' => \App\Models\Animal::class,
            'item_id' => $animal->id,
            'movement_type' => 'in',
            'source_type' => \App\Models\Animal::class,
            'source_event_type' => 'purchase',
            'source_id' => 1,
            'quantity' => 1,
            'unit_cost' => 500,
            'batch_no' => null,
            'movement_date' => now()->subDays(2)->toDateString(),
            'user_id' => $user->id,
        ]);

        Gate::before(fn() => true);

        // 3. Sale: 1 unit.
        $payload = [
            'customer_id' => $customer->id,
            'invoice_date' => now()->toDateString(),
            'status' => 'unpaid',
            'total_amount' => 600,
            'paid_amount' => 0,
            'sales_items' => [
                [
                    'item_id' => $animal->id,
                    'item_type' => \App\Models\Animal::class,
                    'quantity' => 1,
                    'unit_price' => 600,
                    'total_price' => 600,
                ],
            ],
        ];

        $response = $this->actingAs($user)->post(route('sales.store'), $payload);

        if ($response->isRedirect()) {
            $response->assertSessionHasNoErrors();
        } else {
            $this->fail("Animal Sale failed with status " . $response->status() . ": " . $response->getContent());
        }

        // Verify total OUT quantity is 1
        $this->assertEquals(1, StockMovement::where('item_type', \App\Models\Animal::class)->where('movement_type', 'out')->sum('quantity'));
        
        // Verify animal status is Sold
        $this->assertEquals(\App\Enums\AnimalStatus::Sold->value, $animal->fresh()->status);
    }
}
