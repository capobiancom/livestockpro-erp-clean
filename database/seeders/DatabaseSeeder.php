<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * The super-admin user is created by the Installation Wizard (InstallController).
     * This seeder handles only structural/catalog data.
     */
    public function run(): void
    {
        $this->call([
            // roles & permissions
            RoleSeeder::class,
            PermissionSeeder::class,

            // chart of accounts (system-level accounts)
            \Database\Seeders\ChartOfAccountSeeder::class,

            // subscriptions & billing catalog
            SubscriptionCatalogSeeder::class,
            PaymentGatewayConfigSeeder::class,
        ]);
    }
}
