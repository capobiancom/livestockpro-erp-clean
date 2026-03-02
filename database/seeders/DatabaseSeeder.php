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
        // Base user
        User::firstOrCreate(
            ['email' => 'superadmin@example.com'],
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
        ]);
    }
}
