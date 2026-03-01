<?php

namespace Database\Factories;

use App\Models\Farm;
use App\Models\InventoryItem;
use App\Models\Medicine;
use App\Models\StockMovement;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class StockMovementFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = StockMovement::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        $item = $this->faker->randomElement([
            InventoryItem::factory(),
            Medicine::factory(),
        ])->create();

        $movementType = $this->faker->randomElement(['in', 'out', 'adjustment']);
        $sourceEventType = $this->faker->randomElement(['purchase', 'consumption', 'transfer', 'loss', 'expired']);

        return [
            'farm_id' => Farm::factory(),
            'item_type' => $item::class,
            'item_id' => $item->id,
            'movement_type' => $movementType,
            'source_event_type' => $sourceEventType,
            'source_id' => null, // To be filled by specific seeders if needed
            'source_type' => null, // To be filled by specific seeders if needed
            'quantity' => $this->faker->randomFloat(2, 1, 100),
            'unit_cost' => $this->faker->randomFloat(2, 10, 500),
            'batch_no' => $item::class === Medicine::class ? $this->faker->regexify('[A-Z0-9]{10}') : null,
            'expiry_date' => $item::class === Medicine::class ? $this->faker->date() : null,
            'movement_date' => $this->faker->date(),
            'user_id' => User::factory(),
        ];
    }
}
