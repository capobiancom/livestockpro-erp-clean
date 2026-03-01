<?php

namespace Database\Factories;

use App\Models\Purchase;
use App\Models\Supplier;
use App\Models\InventoryItem;
use Illuminate\Database\Eloquent\Factories\Factory;

class PurchaseFactory extends Factory
{
    protected $model = Purchase::class;

    public function definition(): array
    {
        $totalAmount = $this->faker->randomFloat(2, 100, 10000);
        $paidAmount = $this->faker->randomFloat(2, 0, $totalAmount);

        return [
            'supplier_id' => Supplier::factory(),
            'invoice_number' => 'INV-' . $this->faker->unique()->numerify('#####'),
            'total_amount' => $totalAmount,
            'paid_amount' => $paidAmount,
            'discount' => $this->faker->randomFloat(2, 0, 500),
            'discount_type' => $this->faker->randomElement(['Percent', 'Fixed']),
            'tax' => $this->faker->randomFloat(2, 0, 500),
            'tax_percentage' => $this->faker->randomFloat(2, 0, 20),
            'purchased_at' => $this->faker->dateTimeBetween('-1 year', 'now')->format('Y-m-d'),
            'notes' => $this->faker->optional()->sentence(),
            'farm_id' => \App\Models\Farm::factory(),
            'user_id' => \App\Models\User::factory(),
        ];
    }

    /**
     * Configure the model factory.
     */
    public function configure(): static
    {
        return $this->afterCreating(function (Purchase $purchase) {
            \App\Models\PurchaseItem::factory()->count($this->faker->numberBetween(1, 5))->create([
                'purchase_id' => $purchase->id,
                'user_id' => $purchase->user_id,
            ]);
        });
    }
}
