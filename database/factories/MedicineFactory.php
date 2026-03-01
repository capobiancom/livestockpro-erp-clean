<?php

namespace Database\Factories;

use App\Models\Medicine;
use App\Models\Farm;
use App\Models\Supplier;
use App\Models\Category; // Import Category model
use Illuminate\Database\Eloquent\Factories\Factory;

class MedicineFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Medicine::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'farm_id' => function () {
                return \App\Models\Farm::factory()->create()->id;
            },
            'name' => $this->faker->word,
            'description' => $this->faker->sentence,
            'medicine_group' => $this->faker->word,
            'supplier_id' => function () {
                return \App\Models\Supplier::factory()->create()->id;
            },
            'quantity' => $this->faker->numberBetween(0, 1000),
            'unit' => $this->faker->randomElement(['ml', 'tablets', 'bottles', 'grams']),
            'min_quantity' => $this->faker->numberBetween(0, 50),
            'unit_cost' => $this->faker->randomFloat(2, 1, 100),
            'inventory_category_id' => function () {
                $category = \App\Models\Category::inRandomOrder()->first();
                if (!$category) {
                    $farm = \App\Models\Farm::inRandomOrder()->first();
                    if (!$farm) {
                        $farm = \App\Models\Farm::factory()->create();
                    }
                    $category = \App\Models\Category::factory()->create(['farm_id' => $farm->id]);
                }
                return $category->id;
            },
            'sku' => $this->faker->unique()->ean8,
            'notes' => $this->faker->paragraph,
            'user_id' => \App\Models\User::factory(),
            'farm_id' => \App\Models\Farm::factory(),
        ];
    }
}
