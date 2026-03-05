<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Models\User;
use App\Models\Farm;
use App\Models\Breed;
use App\Models\InventoryItem;
use App\Models\Purchase;
use App\Models\FeedingRecord;
use App\Models\VaccinationRecord;
use App\Models\StaffProfile;
use App\Models\ChartOfAccount;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class PoliciesEndpointsTest extends TestCase
{
    use RefreshDatabase;

    public function setUp(): void
    {
        parent::setUp();
        $this->seed(); // seeds roles & permissions
    }

    public function test_user_with_farms_permission_can_create_farm()
    {
        $perm = Permission::firstOrCreate(['name' => 'farms.create']);
        $user = User::factory()->create();
        $user->givePermissionTo($perm);

        $this->actingAs($user);
        $response = $this->post(route('farms.store'), ['name' => 'Test Farm']);
        $response->assertRedirect();
        $this->assertDatabaseHas('farms', ['name' => 'Test Farm']);
    }

    public function test_user_without_farms_permission_cannot_create_farm()
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        $response = $this->post(route('farms.store'), ['name' => 'Forbidden Farm']);
        $response->assertStatus(403);
        $this->assertDatabaseMissing('farms', ['name' => 'Forbidden Farm']);
    }

    public function test_user_with_breeds_permission_can_create_breed()
    {
        $perm = Permission::firstOrCreate(['name' => 'breeds.create']);
        $user = User::factory()->create();
        $user->givePermissionTo($perm);

        $farm = Farm::factory()->create();
        $user->farm_id = $farm->id;
        $user->save();

        $this->actingAs($user);
        $response = $this->post(route('breeds.store'), [
            'name' => 'Test Breed',
            'origin' => 'local',
            'animal_type' => 'cow',
        ]);
        $response->assertSessionHasNoErrors();
        $response->assertRedirect();
        $this->assertDatabaseHas('breeds', ['name' => 'Test Breed']);
    }

    public function test_user_without_breeds_permission_cannot_create_breed()
    {
        $user = User::factory()->create();
        $this->actingAs($user);
        $response = $this->post(route('breeds.store'), ['name' => 'Blocked Breed']);
        $response->assertStatus(403);
    }

    public function test_user_with_inventory_permission_can_create_item()
    {
        $perm = Permission::firstOrCreate(['name' => 'inventory.create']);
        $user = User::factory()->create();
        $user->givePermissionTo($perm);

        $farm = Farm::factory()->create();
        $user->farm_id = $farm->id;
        $user->save();

        $this->actingAs($user);
        $response = $this->post(route('inventory.store'), [
            'name' => 'Feed',
            'quantity' => 10,
            'unit' => 'kg',
        ]);
        $response->assertSessionHasNoErrors();
        $response->assertRedirect();
        $this->assertDatabaseHas('inventory_items', ['name' => 'Feed']);
    }

    public function test_user_without_inventory_permission_cannot_create_item()
    {
        $user = User::factory()->create();
        $this->actingAs($user);
        $response = $this->post(route('inventory.store'), ['name' => 'Blocked Item']);
        $response->assertStatus(403);
    }

    public function test_user_with_purchases_permission_can_create_purchase()
    {
        $perm = Permission::firstOrCreate(['name' => 'purchases.create']);
        $user = User::factory()->create();
        $user->givePermissionTo($perm);

        $farm = Farm::factory()->create();
        $user->farm_id = $farm->id;
        $user->save();

        $item = InventoryItem::factory()->create(['farm_id' => $farm->id]);

        // Ensure required chart of accounts exist for this farm (DemoSeedingController uses these codes)
        ChartOfAccount::query()->firstOrCreate(
            ['farm_id' => $farm->id, 'code' => '2001'],
            ['user_id' => $user->id, 'name' => 'Accounts Payable', 'type' => 'liability', 'is_active' => true]
        );
        ChartOfAccount::query()->firstOrCreate(
            ['farm_id' => $farm->id, 'code' => '1005'],
            ['user_id' => $user->id, 'name' => 'Feed Inventory', 'type' => 'asset', 'is_active' => true]
        );
        ChartOfAccount::query()->firstOrCreate(
            ['farm_id' => $farm->id, 'code' => '1009'],
            ['user_id' => $user->id, 'name' => 'Veterinary Supplies Inventory', 'type' => 'asset', 'is_active' => true]
        );
        ChartOfAccount::query()->firstOrCreate(
            ['farm_id' => $farm->id, 'code' => '1010'],
            ['user_id' => $user->id, 'name' => 'Farm Equipment Inventory', 'type' => 'asset', 'is_active' => true]
        );
        ChartOfAccount::query()->firstOrCreate(
            ['farm_id' => $farm->id, 'code' => '1008'],
            ['user_id' => $user->id, 'name' => 'Medicine Inventory', 'type' => 'asset', 'is_active' => true]
        );

        $this->actingAs($user);
        $response = $this->post(route('purchases.store'), [
            'supplier_id' => null,
            'farm_id' => $farm->id,
            'purchased_at' => now()->toDateString(),
            'notes' => null,
            'total_amount' => 50,
            'discount' => 0,
            'discount_type' => 'Fixed',
            'tax' => 0,
            'tax_percentage' => 0,
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
        $this->assertDatabaseHas('purchases', ['farm_id' => $farm->id]);
    }

    public function test_user_without_purchases_permission_cannot_create_purchase()
    {
        $user = User::factory()->create();
        $this->actingAs($user);
        $response = $this->post(route('purchases.store'), ['quantity' => 1]);
        $response->assertStatus(403);
    }

    public function test_user_with_feedings_permission_can_record_feeding()
    {
        // FeedingRecordPolicy::create checks for `feedings.create`
        $perm = Permission::firstOrCreate(['name' => 'feedings.create']);
        $user = User::factory()->create();
        $user->givePermissionTo($perm);

        $animal = \App\Models\Animal::factory()->create();
        $item = \App\Models\InventoryItem::factory()->create();

        // Ensure required chart of accounts exist for this farm (FeedingRecordController posts to 5001 and uses 1005)
        ChartOfAccount::query()->firstOrCreate(
            ['farm_id' => $animal->farm_id, 'code' => '5001'],
            ['user_id' => $user->id, 'name' => 'Feed Expense', 'type' => 'expense', 'is_active' => true]
        );
        ChartOfAccount::query()->firstOrCreate(
            ['farm_id' => $animal->farm_id, 'code' => '1005'],
            ['user_id' => $user->id, 'name' => 'Feed Inventory', 'type' => 'asset', 'is_active' => true]
        );

        // Ensure there is stock to consume (FeedingRecordController consumes from StockMovement "in" batches)
        \App\Models\StockMovement::factory()->create([
            'item_type' => \App\Models\InventoryItem::class,
            'item_id' => $item->id,
            'farm_id' => $animal->farm_id,
            'movement_type' => 'in',
            'quantity' => 10,
            'movement_date' => now()->subDay()->toDateString(),
        ]);

        $this->actingAs($user);
        $response = $this->post(route('feedings.store'), [
            'animal_id' => $animal->id,
            'feeding_date' => now()->toDateString(),
            'feeding_time' => 'morning',
            'feeding_items' => [
                [
                    'item_id' => $item->id,
                    'quantity' => 1,
                ],
            ],
        ]);

        // If validation fails, the controller redirects back with errors and no record is created.
        $response->assertSessionHasNoErrors();
        $response->assertRedirect();
        $this->assertDatabaseHas('feeding_records', ['animal_id' => $animal->id]);
    }

    public function test_user_without_feedings_permission_cannot_record_feeding()
    {
        $user = User::factory()->create();
        $animal = \App\Models\Animal::factory()->create();
        $this->actingAs($user);
        $response = $this->post(route('feedings.store'), ['animal_id' => $animal->id]);
        $response->assertStatus(403);
    }

    public function test_user_with_vaccination_permission_can_record_vaccination()
    {
        $perm = Permission::firstOrCreate(['name' => 'vaccinations.create']);
        $user = User::factory()->create();
        $user->givePermissionTo($perm);

        $animal = \App\Models\Animal::factory()->create();

        $this->actingAs($user);
        $response = $this->post(route('vaccinations.store'), ['animal_id' => $animal->id]);
        $response->assertRedirect();
        $this->assertDatabaseHas('vaccination_records', ['animal_id' => $animal->id]);
    }

    public function test_user_without_vaccination_permission_cannot_record_vaccination()
    {
        $user = User::factory()->create();
        $animal = \App\Models\Animal::factory()->create();
        $this->actingAs($user);
        $response = $this->post(route('vaccinations.store'), ['animal_id' => $animal->id]);
        $response->assertStatus(403);
    }

    public function test_user_with_staff_permission_can_create_staff()
    {
        $perm = Permission::firstOrCreate(['name' => 'staff.manage']);
        $user = User::factory()->create();
        $user->givePermissionTo($perm);

        $this->actingAs($user);
        $response = $this->post(route('staff.store'), ['first_name' => 'John']);
        $response->assertRedirect();
        $this->assertDatabaseHas('staff_profiles', ['first_name' => 'John']);
    }

    public function test_user_without_staff_permission_cannot_create_staff()
    {
        $user = User::factory()->create();
        $this->actingAs($user);
        $response = $this->post(route('staff.store'), ['first_name' => 'Nope']);
        $response->assertStatus(403);
    }
}
