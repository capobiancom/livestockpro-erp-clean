<?php

namespace Database\Factories;

use App\Models\Farm;
use App\Models\MilkSale;
use App\Models\Supplier;
use Illuminate\Database\Eloquent\Factories\Factory;

class MilkSaleFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = MilkSale::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'farm_id' => Farm::factory(),
            'user_id' => \App\Models\User::factory(),
            'customer_id' => Supplier::factory(),
            'sale_date' => $this->faker->dateTimeBetween('-1 year', 'now'),
            'quantity' => $this->faker->randomFloat(2, 10, 1000),
            'unit' => $this->faker->randomElement(['Liter', 'Gallon']),
            'unit_price' => $this->faker->randomFloat(2, 0.5, 5.0),
            'total_price' => $this->faker->randomFloat(2, 50, 5000),
            'notes' => $this->faker->sentence(),
        ];
    }
}
