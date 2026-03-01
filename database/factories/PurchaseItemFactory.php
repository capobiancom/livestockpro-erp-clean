<?php

namespace Database\Factories;

use App\Models\PurchaseItem;
use App\Models\Purchase;
use App\Models\InventoryItem;
use App\Models\Medicine;
use App\Models\FeedType;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class PurchaseItemFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = PurchaseItem::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        $itemTypes = [
            InventoryItem::class,
            Medicine::class,
            FeedType::class,
        ];
        $itemType = $this->faker->randomElement($itemTypes);
        $item = $itemType::factory()->create();
        $itemId = $item->id;
        $quantity = $this->faker->randomFloat(2, 1, 100);
        $unitPrice = $this->faker->randomFloat(2, 10, 1000);
        $subTotal = $quantity * $unitPrice;

        $attributes = [
            'purchase_id' => Purchase::factory(),
            'item_type' => $itemType,
            'item_id' => $itemId,
            'quantity' => $quantity,
            'unit_price' => $unitPrice,
            'sub_total' => $subTotal,
            'user_id' => User::factory(),
        ];

        if ($itemType === Medicine::class) {
            $attributes['batch_no'] = $this->faker->bothify('BATCH-###???');
            $attributes['expiry_date'] = $this->faker->dateTimeBetween('now', '+5 years')->format('Y-m-d');
        }

        return $attributes;
    }
}
