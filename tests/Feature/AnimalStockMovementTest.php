<?php

namespace Tests\Feature;

use App\Models\Animal;
use App\Models\Breed;
use App\Models\Farm;
use App\Models\StockMovement;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AnimalStockMovementTest extends TestCase
{
    use RefreshDatabase;

    public function test_creating_an_animal_creates_initial_stock_movement_in(): void
    {
        $this->seed();

        $user = User::factory()->create();
        $user->givePermissionTo('animals.create');
        $user->givePermissionTo('animals.update');

        $farm = Farm::query()->first() ?? Farm::factory()->create();
        $breed = Breed::query()->first() ?? Breed::factory()->create([
            'farm_id' => $farm->id,
        ]);

        $payload = [
            'name' => 'Test Animal',
            'animal_type' => 'cow',
            'sex' => 'female',
            'dob' => now()->subYears(2)->toDateString(),
            'breed_id' => $breed->id,
            'farm_id' => $farm->id,
            'herd_id' => null,
            'status' => 'active',
            'current_weight_kg' => 250,
            'color' => 'brown',
            'acquired_at' => now()->subDay()->toDateString(),
            'purchase_price' => 1234.5,
            'supplier_id' => null,
            'attributes' => [],
            'notes' => null,
        ];

        $this->actingAs($user);

        $response = $this->post(route('animals.store'), $payload);
        $response->assertRedirect(route('animals.index'));

        $animal = Animal::query()->latest('id')->first();
        $this->assertNotNull($animal);

        $this->assertDatabaseHas('stock_movements', [
            'item_type' => Animal::class,
            'item_id' => $animal->id,
            'movement_type' => 'in',
            'quantity' => 1,
        ]);
    }

    public function test_updating_an_animal_inserts_stock_movement_if_missing_and_sets_type_from_status(): void
    {
        $this->seed();

        $user = User::factory()->create();
        $user->givePermissionTo('animals.create');
        $user->givePermissionTo('animals.update');

        $farm = Farm::query()->first() ?? Farm::factory()->create();
        $breed = Breed::query()->first() ?? Breed::factory()->create([
            'farm_id' => $farm->id,
        ]);

        $animal = Animal::factory()->create([
            'farm_id' => $farm->id,
            'breed_id' => $breed->id,
            'status' => 'active',
            'animal_type' => 'cow',
            'sex' => 'female',
        ]);

        // Ensure no stock movement exists
        StockMovement::query()
            ->where('item_type', Animal::class)
            ->where('item_id', $animal->id)
            ->delete();

        $this->actingAs($user);

        $payload = [
            'tag' => $animal->tag,
            'name' => $animal->name,
            'animal_type' => $animal->animal_type ?? 'cow',
            'sex' => $animal->sex ?? 'female',
            'dob' => optional($animal->dob)->toDateString(),
            'breed_id' => $animal->breed_id,
            'farm_id' => $animal->farm_id,
            'herd_id' => $animal->herd_id,
            'status' => 'sold',
            'current_weight_kg' => $animal->current_weight_kg,
            'color' => $animal->color,
            'acquired_at' => optional($animal->acquired_at)->toDateString(),
            'purchase_price' => $animal->purchase_price,
            'supplier_id' => $animal->supplier_id,
            'attributes' => $animal->attributes ?? [],
            'notes' => $animal->notes,
        ];

        $response = $this->put(route('animals.update', $animal), $payload);
        $response->assertRedirect(route('animals.show', $animal));

        $this->assertDatabaseHas('stock_movements', [
            'item_type' => Animal::class,
            'item_id' => $animal->id,
            'movement_type' => 'out',
            'quantity' => 1,
        ]);
    }
}
