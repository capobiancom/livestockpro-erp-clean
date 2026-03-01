<?php

namespace Tests\Feature;

use App\Models\Animal;
use App\Models\ChartOfAccount;
use App\Models\Customer;
use App\Models\Farm;
use App\Models\InventoryItem;
use App\Models\JournalEntry;
use App\Models\Sale;
use App\Models\StockMovement;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Gate;
use Tests\TestCase;

class SaleJournalEntryIntegrationTest extends TestCase
{
    use RefreshDatabase;

    private function seedAccounts(Farm $farm, User $user): void
    {
        // Create leaf accounts with required codes
        foreach (
            [
                ['code' => '1001', 'name' => 'Cash'],
                ['code' => '1003', 'name' => 'Accounts Receivable'],
                ['code' => '4002', 'name' => 'Animal Sales'],
                ['code' => '4003', 'name' => 'Inventory SalesItem'],
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

    public function test_sale_with_zero_paid_amount_creates_only_sale_recognition_journal_lines(): void
    {
        $user = User::factory()->create();
        $farm = Farm::factory()->create(['id' => $user->farm_id ?? null]);
        $user->farm_id = $farm->id;
        $user->save();

        $this->seedAccounts($farm, $user);

        $customer = Customer::factory()->create(['farm_id' => $farm->id]);

        $inventoryItem = InventoryItem::factory()->create(['farm_id' => $farm->id]);

        // Seed stock so SaleController inventory consumption doesn't fail
        StockMovement::create([
            'farm_id' => $farm->id,
            'item_type' => InventoryItem::class,
            'item_id' => $inventoryItem->id,
            'movement_type' => 'in',
            'source_type' => InventoryItem::class,
            'source_event_type' => 'purchase',
            'source_id' => $inventoryItem->id,
            'quantity' => 10,
            'unit_cost' => 50,
            'batch_no' => 'BATCH-1',
            'expiry_date' => now()->addYear()->toDateString(),
            'movement_date' => now()->toDateString(),
            'user_id' => $user->id,
        ]);

        $payload = [
            'customer_id' => $customer->id,
            'invoice_date' => now()->toDateString(),
            'status' => 'unpaid',
            'total_amount' => 100,
            'paid_amount' => 0,
            'sales_items' => [
                [
                    'item_id' => $inventoryItem->id,
                    'item_type' => InventoryItem::class,
                    'quantity' => 1,
                    'unit_price' => 100,
                    'total_price' => 100,
                ],
            ],
        ];

        Gate::before(fn() => true);

        $this->actingAs($user)->post(route('sales.store'), $payload)->assertRedirect(route('sales.index'));

        $sale = Sale::firstOrFail();

        $jes = JournalEntry::where('reference_type', 'sale')
            ->where('reference_id', $sale->id)
            ->get();

        $this->assertCount(1, $jes);

        /** @var \App\Models\JournalEntry $je */
        $je = $jes->firstOrFail();

        $this->assertCount(2, $je->lines);

        $ar = ChartOfAccount::where('code', '1003')->firstOrFail();
        $rev = ChartOfAccount::where('code', '4003')->firstOrFail();

        $this->assertTrue($je->lines->contains(fn($l) => (int) $l->account_id === $ar->id && (float) $l->debit_amount === 100.0 && (float) $l->credit_amount === 0.0));
        $this->assertTrue($je->lines->contains(fn($l) => (int) $l->account_id === $rev->id && (float) $l->credit_amount === 100.0 && (float) $l->debit_amount === 0.0));
    }
}
