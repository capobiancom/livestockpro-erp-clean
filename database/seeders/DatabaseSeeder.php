<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $super = User::firstOrCreate(
            ['email' => 'superuser@livestockproerp.com'],
            ['name' => 'Super Admin', 'password' => bcrypt('password')] // Add a default password
        );

        // Core seeders
        $this->call([
            FarmSeeder::class,

            // roles & permissions
            RoleSeeder::class,
            PermissionSeeder::class,
            \Database\Seeders\ChartOfAccountSeeder::class,

            // subscriptions & billing
            SubscriptionCatalogSeeder::class,
            PaymentGatewayConfigSeeder::class,

            // Demo data for reports
            AnimalSeeder::class,
            ArtificialInseminationSeeder::class,
            ReproductionSeeder::class,
            PregnancySeeder::class,
            CalvingRecordSeeder::class,
        ]);

        // make sure there's at least one farm owner tied to a farm; this helps
        // seeded reports display results when logging in as that user.
        $farm = \App\Models\Farm::first();
        if ($farm) {
            User::firstOrCreate(
                ['email' => 'farmowner@livestockproerp.com'],
                [
                    'name' => 'Farm Owner',
                    'password' => bcrypt('password'),
                    'farm_id' => $farm->id,
                ]
            );
        }
    }
}
