<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Disease>
 */
class DiseaseFactory extends Factory
{
    protected $model = \App\Models\Disease::class;
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'user_id' => \App\Models\User::factory(),
            'farm_id' => \App\Models\Farm::factory(),
            'name' => $this->faker->randomElement([
                'Foot-and-Mouth Disease',
                'Bovine Spongiform Encephalopathy (BSE)',
                'Avian Influenza',
                'Swine Flu',
                'Rabies',
                'Mastitis',
                'Newcastle Disease',
                'African Swine Fever',
                'Bluetongue',
                'Contagious Bovine Pleuropneumonia (CBPP)',
            ]),
            'description' => $this->faker->randomElement([
                'A severe, highly contagious viral disease of cloven-hoofed animals.',
                'A progressive neurological disorder of cattle.',
                'A highly pathogenic viral infection affecting birds.',
                'A respiratory disease of pigs caused by influenza viruses.',
                'A viral disease that causes acute encephalitis in warm-blooded animals.',
                'Inflammation of the mammary gland in livestock, typically due to bacterial infection.',
                'A contagious bird disease affecting many domestic and wild bird species.',
                'A highly contagious and deadly viral disease affecting domestic and wild pigs.',
                'A non-contagious, insect-borne viral disease of ruminants.',
                'A highly contagious bacterial disease affecting the lungs and pleura of cattle.',
            ]),
        ];
    }
}
