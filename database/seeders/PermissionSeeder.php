<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class PermissionSeeder extends Seeder
{
    public function run(): void
    {
        $permissions = [
            // animals
            'animals.manage',
            'animals.view',
            'animals.create',
            'animals.update',
            'animals.delete',
            'animals.restore',
            'animals.forceDelete',

            'artificial-inseminations.manage',
            'artificial-inseminations.view',
            'artificial-inseminations.create',
            'artificial-inseminations.update',
            'artificial-inseminations.delete',
            'artificial-inseminations.restore',
            'artificial-inseminations.forceDelete',
            'artificial-inseminations.aiVsNaturalBreedingSuccessReport',


            'attendances.manage',
            'attendances.view',
            'attendances.create',
            'attendances.update',
            'attendances.delete',
            'attendances.restore',
            'attendances.forceDelete',

            'leave-types.manage',
            'leave-types.view',
            'leave-types.create',
            'leave-types.update',
            'leave-types.delete',
            'leave-types.restore',
            'leave-types.forceDelete',

            'leave-requests.manage',
            'leave-requests.view',
            'leave-requests.create',
            'leave-requests.update',
            'leave-requests.delete',
            'leave-requests.restore',
            'leave-requests.forceDelete',

            'salary-structures.manage',
            'salary-structures.view',
            'salary-structures.create',
            'salary-structures.update',
            'salary-structures.delete',
            'salary-structures.restore',
            'salary-structures.forceDelete',

            'payroll-runs.manage',
            'payroll-runs.view',
            'payroll-runs.create',
            'payroll-runs.update',
            'payroll-runs.delete',
            'payroll-runs.restore',
            'payroll-runs.forceDelete',

            'payroll-items.manage',
            'payroll-items.view',
            'payroll-items.create',
            'payroll-items.update',
            'payroll-items.delete',
            'payroll-items.restore',
            'payroll-items.forceDelete',

            'milk-records.manage',
            'milk-records.view',
            'milk-records.create',
            'milk-records.update',
            'milk-records.delete',
            'milk-records.restore',
            'milk-records.forceDelete',

            'milk-sales.manage',
            'milk-sales.view',
            'milk-sales.create',
            'milk-sales.update',
            'milk-sales.delete',
            'milk-sales.restore',
            'milk-sales.forceDelete',

            'sales.manage',
            'sales.view',
            'sales.create',
            'sales.update',
            'sales.delete',
            'sales.restore',
            'sales.forceDelete',


            // feedings & vaccinations
            'feedings.manage',
            'feedings.view',
            'feedings.create',
            'feedings.update',
            'feedings.delete',
            'feedings.restore',
            'feedings.forceDelete',
            'feedings.feedingCostAnalysis',

            'vaccinations.manage',
            'vaccinations.view',
            'vaccinations.create',
            'vaccinations.update',
            'vaccinations.delete',
            'vaccinations.restore',
            'vaccinations.forceDelete',
            'vaccinations.vaccinationDueReport',

            // inventory & purchases
            'inventory.manage',
            'inventory.view',
            'inventory.create',
            'inventory.update',
            'inventory.delete',
            'inventory.restore',
            'inventory.forceDelete',

            'medicines.manage',
            'medicines.view',
            'medicines.create',
            'medicines.update',
            'medicines.delete',
            'medicines.restore',
            'medicines.forceDelete',

            'categories.manage',
            'categories.view',
            'categories.create',
            'categories.update',
            'categories.delete',
            'categories.restore',
            'categories.forceDelete',

            'suppliers.manage',
            'suppliers.view',
            'suppliers.create',
            'suppliers.update',
            'suppliers.delete',
            'suppliers.restore',
            'suppliers.forceDelete',

            'supplier-payments.manage',
            'supplier-payments.view',
            'supplier-payments.create',
            'supplier-payments.update',
            'supplier-payments.delete',
            'supplier-payments.restore',
            'supplier-payments.forceDelete',

            'medicine-groups.manage',
            'medicine-groups.view',
            'medicine-groups.create',
            'medicine-groups.update',
            'medicine-groups.delete',
            'medicine-groups.restore',
            'medicine-groups.forceDelete',

            'stock-movements.manage',
            'stock-movements.view',
            'stock-movements.create',
            'stock-movements.update',
            'stock-movements.delete',
            'stock-movements.restore',
            'stock-movements.forceDelete',
            'stock-movements.currentStockByItemReport',
            'stock-movements.lowStockAlertReport',
            'stock-movements.expiredMedicineAlertReport',
            'stock-movements.feedConsumedPerAnimalReport',
            'stock-movements.costOfFeedPerCowReport',
            'stock-movements.monthlyConsumptionSummeryReport',
            'stock-movements.wastageAndLossReport',

            'purchases.manage',
            'purchases.view',
            'purchases.create',
            'purchases.update',
            'purchases.delete',
            'purchases.restore',
            'purchases.forceDelete',

            'customers.manage',
            'customers.view',
            'customers.create',
            'customers.update',
            'customers.delete',
            'customers.restore',
            'customers.forceDelete',

            // reproduction reports
            'reproduction-records.conseption_success_rate_reports',

            'customer-payments.manage',
            'customer-payments.view',
            'customer-payments.create',
            'customer-payments.update',
            'customer-payments.delete',
            'customer-payments.restore',
            'customer-payments.forceDelete',

            // domain management
            'breeds.manage',
            'breeds.view',
            'breeds.create',
            'breeds.update',
            'breeds.delete',
            'breeds.restore',
            'breeds.forceDelete',

            'calves.manage',
            'calves.view',
            'calves.create',
            'calves.update',
            'calves.delete',
            'calves.restore',
            'calves.forceDelete',
            'calves.calvingIntervalReport',

            'calving-records.manage',
            'calving-records.view',
            'calving-records.create',
            'calving-records.update',
            'calving-records.delete',
            'calving-records.restore',
            'calving-records.forceDelete',

            'cash-accounts.manage',
            'cash-accounts.view',
            'cash-accounts.create',
            'cash-accounts.update',
            'cash-accounts.delete',
            'cash-accounts.restore',
            'cash-accounts.forceDelete',


            'chart-of-accounts.manage',
            'chart-of-accounts.view',
            'chart-of-accounts.create',
            'chart-of-accounts.update',
            'chart-of-accounts.delete',
            'chart-of-accounts.restore',
            'chart-of-accounts.forceDelete',

            'journal-entries.manage',
            'journal-entries.view',
            'journal-entries.create',
            'journal-entries.update',
            'journal-entries.delete',
            'journal-entries.restore',
            'journal-entries.forceDelete',
            'journal-entries.financial_report',
            'journal-entries.voucher_report',
            'journal-entries.balance_sheet',
            'journal-entries.profit_loss',
            'journal-entries.cash_flow',
            'journal-entries.trial_balance',
            'journal-entries.fixed_asset',

            'expenses.manage',
            'expenses.view',
            'expenses.create',
            'expenses.update',
            'expenses.delete',
            'expenses.restore',
            'expenses.forceDelete',

            'health-issues.manage',
            'health-issues.view',
            'health-issues.create',
            'health-issues.update',
            'health-issues.delete',
            'health-issues.restore',
            'health-issues.forceDelete',


            'health-events.manage',
            'health-events.view',
            'health-events.create',
            'health-events.update',
            'health-events.delete',
            'health-events.restore',
            'health-events.forceDelete',
            'health-events.animalHealthReport',

            'diseases.manage',
            'diseases.view',
            'diseases.create',
            'diseases.update',
            'diseases.delete',
            'diseases.restore',
            'diseases.forceDelete',

            'disease-treatments.manage',
            'disease-treatments.view',
            'disease-treatments.create',
            'disease-treatments.update',
            'disease-treatments.delete',
            'disease-treatments.restore',
            'disease-treatments.forceDelete',
            'disease-treatments.treatmentCostPerCowReport',
            'disease-treatments.medicineUseedPerDiseaseReport',

            'treatments.manage',
            'treatments.view',
            'treatments.create',
            'treatments.update',
            'treatments.delete',
            'treatments.restore',
            'treatments.forceDelete',

            'logistics.manage',
            'logistics.view',
            'logistics.create',
            'logistics.update',
            'logistics.delete',
            'logistics.restore',
            'logistics.forceDelete',

            'farms.manage',
            'farms.view',
            'farms.create',
            'farms.update',
            'farms.delete',
            'farms.restore',
            'farms.forceDelete',

            'departments.manage',
            'departments.view',
            'departments.create',
            'departments.update',
            'departments.delete',
            'departments.restore',
            'departments.forceDelete',

            'designations.manage',
            'designations.view',
            'designations.create',
            'designations.update',
            'designations.delete',
            'designations.restore',
            'designations.forceDelete',



            'reproduction-records.manage',
            'reproduction-records.view',
            'reproduction-records.create',
            'reproduction-records.update',
            'reproduction-records.delete',
            'reproduction-records.restore',
            'reproduction-records.forceDelete',
            'reproduction-records.conseptionSuccessRateReport',

            'pregnancies.manage',
            'pregnancies.view',
            'pregnancies.create',
            'pregnancies.update',
            'pregnancies.delete',
            'pregnancies.restore',
            'pregnancies.forceDelete',
            'pregnancies.pregnancyLossAnalysis',
            'pregnancies.fertilityPerformancePerCowReport',

            'event-types.manage',
            'event-types.view',
            'event-types.create',
            'event-types.update',
            'event-types.delete',
            'event-types.restore',
            'event-types.forceDelete',

            'pregnancy-checkups.manage',
            'pregnancy-checkups.view',
            'pregnancy-checkups.create',
            'pregnancy-checkups.update',
            'pregnancy-checkups.delete',
            'pregnancy-checkups.restore',
            'pregnancy-checkups.forceDelete',

            'herds.manage',
            'herds.view',
            'herds.create',
            'herds.update',
            'herds.delete',
            'herds.restore',
            'herds.forceDelete',

            'farms.manage',
            'farms.view',
            'farms.create',
            'farms.update',
            'farms.delete',
            'farms.restore',
            'farms.forceDelete',

            'vaccine_types.manage',
            'vaccine_types.view',
            'vaccine_types.create',
            'vaccine_types.update',
            'vaccine_types.delete',
            'vaccine_types.restore',
            'vaccine_types.forceDelete',

            'employees.manage',
            'employees.view',
            'employees.create',
            'employees.update',
            'employees.delete',
            'employees.restore',
            'employees.forceDelete',

            'employee-documents.manage',
            'employee-documents.view',
            'employee-documents.create',
            'employee-documents.update',
            'employee-documents.delete',
            'employee-documents.restore',
            'employee-documents.forceDelete',

            'shifts.manage',
            'shifts.view',
            'shifts.create',
            'shifts.update',
            'shifts.delete',
            'shifts.restore',
            'shifts.forceDelete',

            'employee-shifts.manage',
            'employee-shifts.view',
            'employee-shifts.create',
            'employee-shifts.update',
            'employee-shifts.delete',
            'employee-shifts.restore',
            'employee-shifts.forceDelete',
            // admin
            'roles.manage',
            'permissions.manage',
            'users.manage'
        ];

        foreach ($permissions as $name) {
            Permission::firstOrCreate(['name' => $name]);
        }

        // Give admin role all permissions by default (if role exists)
        $allPermissions = Permission::all();
        if ($allPermissions->isEmpty()) {
            return;
        }

        $admin = Role::where('name', 'admin')->first();
        if ($admin && $admin->exists) {
            // sync using actual Permission models to avoid collection/guard issues
            // also make sure model is fresh and caches are cleared
            $admin->syncPermissions($allPermissions);
            $admin->forgetCachedPermissions();
        }
    }
}
