<?php

namespace Tests\Feature;

use App\Models\Farm;
use App\Models\InventoryItem;
use App\Models\Purchase;
use App\Models\StockMovement;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Permission;
use Tests\TestCase;

class PurchaseDeleteStockMovementTest extends TestCase
{
    use RefreshDatabase;

    public function test_deleting_purchase_deletes_related_stock_movements()
    {
        $this->seed([
            \Database\Seeders\RoleSeeder::class,
            \Database\Seeders\PermissionSeeder::class,
        ]);

        $createPerm = Permission::firstOrCreate(['name' => 'purchases.create']);
        $deletePerm = Permission::firstOrCreate(['name' => 'purchases.delete']);
        $user = User::factory()->create(['farm_id' => null]);
        $user->givePermissionTo([$createPerm, $deletePerm]);

        $farm = Farm::factory()->create(['user_id' => $user->id]);
        $user->farm_id = $farm->id;
        $user->save();

        $item = InventoryItem::factory()->create(['farm_id' => $farm->id]);

        // Minimal chart of accounts required by PurchaseController::store()
        \App\Models\ChartOfAccount::factory()->create([
            'farm_id' => $farm->id,
            'user_id' => $user->id,
            'code' => '2001',
            'name' => 'Accounts Payable',
            'type' => \App\Enums\ChartOfAccountType::Liability,
            'is_system' => true,
            'is_active' => true,
        ]);

        $this->actingAs($user);

        $response = $this->post(route('purchases.store'), [
            'supplier_id' => null,
            'purchased_at' => now()->toDateString(),
            'notes' => null,
            'total_amount' => 50,
            'discount' => 0,
            'discount_type' => 'Fixed',
            'tax' => 0,
            'tax_percentage' => 0,
            'farm_id' => $farm->id,
            'items' => [
                [
                    'item_type' => 'inventory_item',
                    'item_id' => $item->id,
                    'quantity' => 5,
                    'unit_price' => 10,
                    'batch_no' => null,
                    'expiry_date' => null,
                ],
            ],
        ]);

        $response->assertRedirect();

        $purchase = Purchase::query()->latest('id')->first();
        $this->assertNotNull($purchase);

        $this->assertDatabaseHas('stock_movements', [
            'source_type' => Purchase::class,
            'source_id' => $purchase->id,
            'movement_type' => 'in',
        ]);

        $deleteResponse = $this->delete(route('purchases.destroy', $purchase));
        $deleteResponse->assertRedirect();

        $this->assertDatabaseMissing('stock_movements', [
            'source_type' => Purchase::class,
            'source_id' => $purchase->id,
        ]);
    }
}
