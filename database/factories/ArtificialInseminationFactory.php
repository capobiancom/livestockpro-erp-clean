<?php

namespace Database\Factories;

use App\Models\ArtificialInsemination;
use App\Models\Farm;
use App\Models\ReproductionRecord;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class ArtificialInseminationFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = ArtificialInsemination::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'farm_id' => Farm::factory(),
            'semen_batch_no' => $this->faker->unique()->bothify('AI-####-????'),
            'reproduction_record_id' => ReproductionRecord::factory(),
            'breed_id' => \App\Models\Breed::factory(), // Use Breed factory for breed_id
            'semen_company' => $this->faker->company,
            'insemination_date' => $this->faker->date(),
            'vet_id' => User::factory(), // Assuming 'vet_id' refers to a User model
            'cost' => $this->faker->randomFloat(2, 50, 500),
            'remarks' => $this->faker->sentence,
            'user_id' => User::factory(),
        ];
    }
}
