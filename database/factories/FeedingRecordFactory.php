<?php

namespace Database\Factories;

use App\Models\FeedingRecord;
use App\Models\Animal;
use App\Models\Herd;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class FeedingRecordFactory extends Factory
{
    protected $model = FeedingRecord::class;

    public function definition(): array
    {
        return [
            'animal_id' => $this->faker->boolean(50) ? Animal::factory() : null, // 50% chance to have an animal
            'group_id' => $this->faker->boolean(50) ? Herd::factory() : null, // 50% chance to have a group
            'feeding_date' => $this->faker->dateTimeBetween('-1 month', 'now'),
            'feeding_time' => $this->faker->randomElement(['morning', 'evening']),
            'notes' => $this->faker->optional()->sentence(),
            'farm_id' => \App\Models\Farm::factory(),
            'user_id' => User::factory(),
        ];
    }

    public function configure(): static
    {
        return $this->afterCreating(function (FeedingRecord $feedingRecord) {
            // Create 1 to 3 feeding items for each feeding record
            $numberOfItems = $this->faker->numberBetween(1, 3);
            for ($i = 0; $i < $numberOfItems; $i++) {
                $inventoryItem = \App\Models\InventoryItem::factory()->create();
                $feedingRecord->feedingItems()->create([
                    'item_id' => $inventoryItem->id,
                    'quantity' => $this->faker->randomFloat(2, 0.1, 10),
                ]);
            }
        });
    }
}
