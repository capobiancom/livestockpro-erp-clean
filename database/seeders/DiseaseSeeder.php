<?php

namespace Database\Seeders;

use App\Models\Disease;
use App\Models\User;
use App\Models\Farm;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DiseaseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $user = User::first();
        $farm = Farm::first();

        $diseasesData = [
            ['name' => 'Foot-and-Mouth Disease', 'description' => 'A severe, highly contagious viral disease of cloven-hoofed animals.'],
            ['name' => 'Bovine Spongiform Encephalopathy (BSE)', 'description' => 'A progressive neurological disorder of cattle.'],
            ['name' => 'Avian Influenza', 'description' => 'A highly pathogenic viral infection affecting birds.'],
            ['name' => 'Swine Flu', 'description' => 'A respiratory disease of pigs caused by influenza viruses.'],
            ['name' => 'Rabies', 'description' => 'A viral disease that causes acute encephalitis in warm-blooded animals.'],
            ['name' => 'Mastitis', 'description' => 'Inflammation of the mammary gland in livestock, typically due to bacterial infection.'],
            ['name' => 'Newcastle Disease', 'description' => 'A contagious bird disease affecting many domestic and wild bird species.'],
            ['name' => 'African Swine Fever', 'description' => 'A highly contagious and deadly viral disease affecting domestic and wild pigs.'],
            ['name' => 'Bluetongue', 'description' => 'A non-contagious, insect-borne viral disease of ruminants.'],
            ['name' => 'Contagious Bovine Pleuropneumonia (CBPP)', 'description' => 'A highly contagious bacterial disease affecting the lungs and pleura of cattle.'],
        ];

        if ($user && $farm) {
            foreach ($diseasesData as $data) {
                Disease::create([
                    'user_id' => $user->id,
                    'farm_id' => $farm->id,
                    'name' => $data['name'],
                    'description' => $data['description'],
                ]);
            }
        } else {
            // Fallback if user or farm not found, create with generic data
            foreach ($diseasesData as $data) {
                Disease::create([
                    'user_id' => null, // Or a default user if applicable
                    'farm_id' => null, // Or a default farm if applicable
                    'name' => $data['name'],
                    'description' => $data['description'],
                ]);
            }
        }
    }
}
