<?php

namespace Database\Factories;

use App\Models\InventoryItem;
use App\Models\Supplier;
use Illuminate\Database\Eloquent\Factories\Factory;

class InventoryItemFactory extends Factory
{
    protected $model = InventoryItem::class;

    public function definition(): array
    {
        return [
            'sku' => strtoupper($this->faker->unique()->bothify('SKU-####')),
            'name' => $this->faker->word(),
            'category' => $this->faker->randomElement(['medication', 'feed', 'equipment', 'consumable']),
            'quantity' => $this->faker->randomFloat(3, 0, 1000),
            'unit' => $this->faker->randomElement(['unit', 'kg', 'liter', 'bag']),
            'min_quantity' => $this->faker->randomFloat(3, 1, 50),
            'supplier_id' => Supplier::factory(),
            'notes' => $this->faker->optional()->sentence(),
            'user_id' => \App\Models\User::factory(),
            'farm_id' => \App\Models\Farm::factory(),
        ];
    }
}
