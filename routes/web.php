<?php

use App\Http\Controllers\Admin\PermissionController;
use App\Http\Controllers\Admin\RoleController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Admin\SubscriptionPlanController;
use App\Http\Controllers\Admin\SubscriptionFeatureController;
use App\Http\Controllers\Admin\AdminFarmsController;
use App\Http\Controllers\BreedController;
use App\Http\Controllers\DiseaseTreatmentController;
use App\Http\Controllers\FeedTypeController;
use App\Http\Controllers\HerdController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\PurchaseController;
use App\Http\Controllers\VaccineTypeController;
use App\Http\Controllers\FarmSwitchController;
use App\Models\User;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Schema;
use App\Http\Controllers\AnimalHealthReportController;
use App\Http\Controllers\FeedingCostAnalysisReportController;
use App\Http\Controllers\VaccinationDueReportController;
use App\Http\Controllers\FinancialReportsController;
use App\Http\Controllers\FarmProductivityDashboardController;
use App\Http\Controllers\DemoSeedingController;
use App\Http\Controllers\CashAccountController;
use App\Http\Controllers\CashTransactionController;
use App\Http\Controllers\JournalEntryController;
use App\Http\Controllers\BalanceSheetController;
use App\Http\Controllers\CashFlowController;
use App\Http\Controllers\ProfitLossController;
use App\Http\Controllers\TrialBalanceController;
use App\Http\Controllers\ConceptionSuccessRateReportController;
use App\Http\Controllers\PregnancyLossAnalysisReportController;
use App\Http\Controllers\AiVsNaturalBreedingSuccessReportController;
use App\Http\Controllers\CalvingIntervalReportController;
use App\Http\Controllers\FertilityPerformancePerCowReportController;
use App\Http\Controllers\CurrentStockByItemReportController;
use App\Http\Controllers\LowStockAlertsReportController;
use App\Http\Controllers\ExpiredMedicineReportController;
use App\Http\Controllers\CostOfFeedPerCowReportController;
use App\Http\Controllers\FeedConsumedPerAnimalReportController;
use App\Http\Controllers\MedicineUsedPerDiseaseReportController;
use App\Http\Controllers\MonthlyConsumptionSummaryReportController;
use App\Http\Controllers\TreatmentCostPerAnimalReportController;
use App\Http\Controllers\WastageLossReportController;
use Inertia\Inertia;
use Spatie\Permission\Models\Role;

// ── Installation Wizard ────────────────────────────────────────────────────
// Accessible only before the app is installed (storage/installed lock file absent).
Route::middleware('not.installed')
    ->prefix('install')
    ->name('install.')
    ->group(function () {
        Route::get('/',            [\App\Http\Controllers\InstallController::class, 'welcome'])->name('welcome');
        Route::get('/requirements', [\App\Http\Controllers\InstallController::class, 'requirements'])->name('requirements');
        Route::get('/database',    [\App\Http\Controllers\InstallController::class, 'database'])->name('database');
        Route::post('/database',   [\App\Http\Controllers\InstallController::class, 'saveDatabase'])->name('database.save');
        Route::get('/environment', [\App\Http\Controllers\InstallController::class, 'environment'])->name('environment');
        Route::post('/environment', [\App\Http\Controllers\InstallController::class, 'saveEnvironment'])->name('environment.save');
        Route::get('/migrate',     [\App\Http\Controllers\InstallController::class, 'migrate'])->name('migrate');
        Route::post('/migrate',    [\App\Http\Controllers\InstallController::class, 'runMigrations'])->name('migrate.run');
        Route::get('/admin',       [\App\Http\Controllers\InstallController::class, 'admin'])->name('admin');
        Route::post('/admin',      [\App\Http\Controllers\InstallController::class, 'createAdmin'])->name('admin.create');
        Route::get('/complete',    [\App\Http\Controllers\InstallController::class, 'complete'])->name('complete');
    });

// ─────────────────────────────────────────────────────────────────────────────

Route::post('/request-demo', [\App\Http\Controllers\DemoRequestController::class, 'store'])
    ->name('demo-requests.store');

Route::get('/', function () {
    $allPlans = [];

    if (Schema::hasTable('subscription_plans')) {
        $allPlans = \App\Models\SubscriptionPlan::query()
            ->with(['enabledFeatures'])
            ->where('is_active', true)
            ->orderBy('sort_order')
            ->get()
            ->map(function (\App\Models\SubscriptionPlan $plan) {
                $monthly = $plan->monthly_price_cents;
                $yearlySubtotal = $monthly * 12;
                $discount = (int) round($yearlySubtotal * ($plan->yearly_discount_percent / 100));
                $yearlyTotal = max(0, $yearlySubtotal - $discount);

                return [
                    'id' => $plan->id,
                    'name' => $plan->name,
                    'slug' => $plan->slug,
                    'monthly_price_cents' => $monthly,
                    'yearly_discount_percent' => $plan->yearly_discount_percent,
                    'yearly_price_cents' => $yearlyTotal,
                    'features' => $plan->enabledFeatures
                        ?->map(fn($f) => [
                            'id' => $f->id,
                            'name' => $f->name,
                            'key' => $f->key,
                            'description' => $f->description,
                        ])
                        ?->values()
                        ?->toArray() ?? [],
                ];
            });
    }

    $setting = Schema::hasTable('settings') ? \App\Models\Setting::first() : null;

    return Inertia::render('Welcome', [
        'canLogin' => Route::has('login'),
        'canRegister' => Route::has('register'),
        'laravelVersion' => Application::VERSION,
        'phpVersion' => PHP_VERSION,
        'allPlans' => $allPlans,
        'websiteSettings' => [
            'site_title' => $setting?->site_title,
            'site_description' => $setting?->site_description,
            'currency' => $setting?->website_currency,
            'logo_url' => $setting?->website_logo_path
                ? \Illuminate\Support\Facades\Storage::url($setting?->website_logo_path)
                : null,
        ],
    ]);
});




Route::middleware(['auth', 'subscription.active'])->group(function () {

    Route::middleware('saas.only')->group(function () {
        Route::get('/admin/dashboard', [\App\Http\Controllers\Admin\AdminDashboardController::class, 'index'])
            ->middleware('role:admin|Super Admin')
            ->name('admin.dashboard');

        Route::get('/admin/collections', [\App\Http\Controllers\Admin\AdminCollectionsController::class, 'index'])
            ->middleware('role:admin|Super Admin')
            ->name('admin.collections');

        Route::post('/admin/collections/invoices/{invoice}/collect', [\App\Http\Controllers\Admin\AdminCollectionsController::class, 'collectInvoice'])
            ->middleware('role:admin|Super Admin')
            ->name('admin.collections.invoices.collect');

        Route::post('/admin/farms/{farm}/subscription/toggle', [\App\Http\Controllers\Admin\AdminFarmSubscriptionController::class, 'toggle'])
            ->middleware('role:admin|Super Admin')
            ->name('admin.farms.subscriptions.toggle');

        Route::post('/admin/farms/{farm}/notifications/send', [\App\Http\Controllers\Admin\AdminFarmNotificationController::class, 'send'])
            ->middleware('role:admin|Super Admin')
            ->name('admin.farms.notifications.send');

        // Super Admin: Farms directory (list + details with user login identifiers/roles)
        Route::get('/admin/farms', [AdminFarmsController::class, 'index'])
            ->middleware('role:Super Admin')
            ->name('admin.farms.index');

        Route::get('/admin/farms/{farm}', [AdminFarmsController::class, 'show'])
            ->middleware('role:Super Admin')
            ->name('admin.farms.show');
    });

    // Super Admin: Website settings (affects public website only)
    Route::get('/admin/settings/website', [\App\Http\Controllers\Admin\SuperAdminSettingsController::class, 'edit'])
        ->middleware('role:Super Admin')
        ->name('admin.settings.website.edit');

    Route::post('/admin/settings/website', [\App\Http\Controllers\Admin\SuperAdminSettingsController::class, 'update'])
        ->middleware('role:Super Admin')
        ->name('admin.settings.website.update');

    // Super Admin: Email settings (used for demo presentation emails)
    Route::get('/admin/settings/email', [\App\Http\Controllers\Admin\SuperAdminEmailSettingsController::class, 'edit'])
        ->middleware('role:Super Admin')
        ->name('admin.settings.email.edit');

    Route::post('/admin/settings/email', [\App\Http\Controllers\Admin\SuperAdminEmailSettingsController::class, 'update'])
        ->middleware('role:Super Admin')
        ->name('admin.settings.email.update');

    // Super Admin: Demo requests inbox
    Route::get('/admin/demo-requests', [\App\Http\Controllers\Admin\DemoRequestAdminController::class, 'index'])
        ->middleware('role:Super Admin')
        ->name('admin.demo-requests.index');

    Route::post('/admin/demo-requests/{demoRequest}/send-email', [\App\Http\Controllers\Admin\DemoRequestAdminController::class, 'sendEmail'])
        ->middleware('role:Super Admin')
        ->name('admin.demo-requests.send-email');
    Route::post('/register/{farm}/seed-demo', [DemoSeedingController::class, 'seed'])
        ->name('register.seed-demo');

    Route::post('/register/{farm}/skip-demo', [DemoSeedingController::class, 'skip'])
        ->name('register.skip-demo');

    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // Application statistical dashboard
    Route::middleware(['subscription.feature:dashboard'])->group(function () {

        Route::get('/dashboard', [\App\Http\Controllers\DashboardController::class, 'index'])
            ->middleware(['auth', 'verified', 'role:farm owner'])
            ->name('dashboard');

        // SaaS mode only (hide/disable in single-license mode)
        Route::get('/farm-productivity-dashboard', [FarmProductivityDashboardController::class, 'index'])
            ->middleware(['auth', 'verified', 'role:farm owner', 'saas.only'])
            ->name('farm-productivity-dashboard.index');
    });

    // Application resources (feature-gated)
    Route::middleware(['subscription.feature:animals'])->group(function () {
        Route::resource('animals', \App\Http\Controllers\AnimalController::class);
        Route::resource('breeds', BreedController::class);
        Route::resource('herds', HerdController::class);

        // Explicitly define the index route for reproduction records to ensure it's correctly resolved
        Route::get('reproduction-records', [\App\Http\Controllers\ReproductionRecordController::class, 'index'])
            ->name('reproduction-records.index');
        Route::resource('reproduction-records', \App\Http\Controllers\ReproductionRecordController::class)->except(['index']);

        Route::resource('artificial-inseminations', \App\Http\Controllers\ArtificialInseminationController::class);
        Route::resource('pregnancies', \App\Http\Controllers\PregnancyController::class);
        Route::resource('pregnancy-checkups', \App\Http\Controllers\PregnancyCheckupController::class);
        Route::resource('calving-records', \App\Http\Controllers\CalvingRecordController::class);
        Route::resource('calves', \App\Http\Controllers\CalfController::class);
    });

    Route::middleware(['subscription.feature:feedings'])->group(function () {
        Route::resource('feedings', \App\Http\Controllers\FeedingRecordController::class);
        Route::resource('feed-types', FeedTypeController::class);
    });

    Route::middleware(['subscription.feature:healths'])->group(function () {
        Route::resource('vaccinations', \App\Http\Controllers\VaccinationController::class);
        Route::resource('health-issues', \App\Http\Controllers\HealthIssueController::class);
        Route::resource('health-events', \App\Http\Controllers\HealthEventController::class);
        Route::resource('disease-treatments', DiseaseTreatmentController::class);
        Route::resource('treatments', \App\Http\Controllers\TreatmentController::class);
        Route::resource('event-types', \App\Http\Controllers\EventTypeController::class);
        Route::resource('diseases', \App\Http\Controllers\DiseaseController::class);
        Route::resource('vaccine-types', VaccineTypeController::class);

        // New route to fetch health events by health issue
        Route::get('/disease-treatments/health-events-by-issue/{healthIssue}', [\App\Http\Controllers\DiseaseTreatmentController::class, 'getHealthEventsByHealthIssue'])
            ->name('disease-treatments.health-events-by-issue');

        // New route to fetch medications for a specific treatment within DiseaseTreatment context
        Route::get('/disease-treatments/get-treatment-medications/{treatment}', [\App\Http\Controllers\DiseaseTreatmentController::class, 'getTreatmentMedications'])
            ->name('disease-treatments.get-treatment-medications');

        // Existing route to fetch medications by treatment (if used elsewhere)
        Route::get('/treatments/{treatment}/medications', [\App\Http\Controllers\TreatmentController::class, 'getMedicationsByTreatment'])
            ->name('treatments.medications-by-treatment');
    });

    Route::middleware(['subscription.feature:inventory'])->group(function () {
        Route::resource('inventory', \App\Http\Controllers\InventoryController::class);
        Route::resource('categories', \App\Http\Controllers\InventoryCategoryController::class);
        Route::resource('medicines', \App\Http\Controllers\MedicineController::class);
        Route::resource('medicine-groups', \App\Http\Controllers\MedicineGroupController::class);
        Route::get('stock-movements/unit/{unit}', [\App\Http\Controllers\StockMovementController::class, 'unitDetails'])
            ->name('stock-movements.unit-details');
        Route::resource('stock-movements', \App\Http\Controllers\StockMovementController::class);
        Route::resource('suppliers', \App\Http\Controllers\SupplierController::class);

        // Supplier Payments
        Route::resource('supplier-payments', \App\Http\Controllers\SupplierPaymentController::class);
    });

    Route::middleware(['subscription.feature:customers'])->group(function () {
        Route::resource('customers', \App\Http\Controllers\CustomerController::class);

        // Customer Payments
        Route::resource('customer-payments', \App\Http\Controllers\CustomerPaymentController::class);
    });

    Route::middleware(['subscription.feature:finance'])->group(function () {
        Route::resource('expenses', \App\Http\Controllers\ExpenseController::class);
        Route::resource('sales', \App\Http\Controllers\SaleController::class);
        Route::get('/sales/{sale}/print', [\App\Http\Controllers\SaleController::class, 'printInvoice'])
            ->name('sales.print');

        Route::resource('purchases', PurchaseController::class);
        Route::get('/purchases/{purchase}/print', [\App\Http\Controllers\PurchaseController::class, 'printInvoice'])
            ->name('purchases.print');
    });

    Route::middleware(['subscription.feature:operation'])->group(function () {
        Route::resource('logistics', \App\Http\Controllers\LogisticController::class);
        Route::resource('farms', \App\Http\Controllers\FarmController::class);
        Route::resource('staff', \App\Http\Controllers\StaffController::class);

        Route::post('/farm/switch', [FarmSwitchController::class, 'store'])
            ->name('farm.switch');
    });

    Route::middleware(['subscription.feature:productions'])->group(function () {
        Route::resource('milk-records', \App\Http\Controllers\MilkRecordController::class);
        Route::resource('milk-sales', \App\Http\Controllers\MilkSaleController::class);
        Route::get('/milk-sales/{milkSale}/print', [\App\Http\Controllers\MilkSaleController::class, 'printInvoice'])
            ->name('milk-sales.print');
    });

    Route::middleware(['subscription.feature:hr'])->group(function () {
        Route::resource('departments', \App\Http\Controllers\DepartmentController::class);
        Route::resource('designations', \App\Http\Controllers\DesignationController::class);
        Route::resource('employees', \App\Http\Controllers\EmployeeController::class);
        Route::resource('employee-documents', \App\Http\Controllers\EmployeeDocumentController::class);
        Route::resource('shifts', \App\Http\Controllers\ShiftController::class);
        Route::resource('employee-shifts', \App\Http\Controllers\EmployeeShiftController::class);
        Route::resource('attendances', \App\Http\Controllers\AttendanceController::class);
        Route::resource('leave-types', \App\Http\Controllers\HR\LeaveTypeController::class);
        Route::resource('leave-requests', \App\Http\Controllers\HR\LeaveRequestController::class);
        Route::resource('salary-structures', \App\Http\Controllers\SalaryStructureController::class);
        Route::resource('payroll-runs', \App\Http\Controllers\PayrollRunController::class);

        // Move specific route above resource route
        Route::get('/payroll-items/generate-salary-sheet', [\App\Http\Controllers\PayrollItemController::class, 'generateSalarySheet'])
            ->name('payroll-items.generate-salary-sheet');

        Route::resource('payroll-items', \App\Http\Controllers\PayrollItemController::class);
        Route::post('/payroll-runs/{payroll_run}/generate-payroll', [\App\Http\Controllers\PayrollItemController::class, 'generatePayroll'])
            ->name('payroll-runs.generate-payroll');

        Route::get('/payroll-items/{payroll_item}/payslip', [\App\Http\Controllers\PayrollItemController::class, 'showPayslip'])
            ->name('payroll-items.payslip');

        Route::get('/payroll-items/{payroll_item}/print-payslip', [\App\Http\Controllers\PayrollItemController::class, 'printPayslip'])
            ->name('payroll-items.print-payslip');
    });

    Route::middleware(['subscription.feature:accounting'])->group(function () {
        Route::resource('chart-of-accounts', \App\Http\Controllers\ChartOfAccountController::class);
        Route::resource('journal-entries', JournalEntryController::class);
        Route::get('/journal-entries/{journalEntry}/print-voucher', [\App\Http\Controllers\JournalVoucherReportController::class, 'printVoucher'])
            ->name('journal-entries.print-voucher');
        Route::get('/journal-voucher-report', [\App\Http\Controllers\JournalVoucherReportController::class, 'index'])
            ->name('journal-voucher-report.index');

        Route::get('/balance-sheet', [BalanceSheetController::class, 'index'])
            ->name('balance-sheet.index');

        Route::get('/profit-loss', [ProfitLossController::class, 'index'])
            ->name('profit-loss.index');

        Route::get('/cash-flow', [CashFlowController::class, 'index'])
            ->name('cash-flow.index');

        Route::get('/trial-balance', [TrialBalanceController::class, 'index'])
            ->name('trial-balance.index');

        Route::resource('cash-accounts', CashAccountController::class);
        Route::resource('cash-transactions', CashTransactionController::class);
        Route::resource('fixed-assets', \App\Http\Controllers\FixedAssetController::class);
    });

    // Admin role management
    Route::get('admin/roles', [RoleController::class, 'index'])
        ->name('admin.roles.index')
        ->middleware('role:admin|Super Admin');

    Route::post('admin/roles', [RoleController::class, 'store'])
        ->name('admin.roles.store')
        ->middleware('role:admin|Super Admin');

    Route::delete('admin/roles/{role}', [RoleController::class, 'destroy'])
        ->name('admin.roles.destroy')
        ->middleware('role:admin|Super Admin');

    // Admin permission management
    Route::get('admin/permissions', [PermissionController::class, 'index'])
        ->name('admin.permissions.index')
        ->middleware('role:admin|Super Admin');

    Route::post('admin/permissions', [PermissionController::class, 'store'])
        ->name('admin.permissions.store')
        ->middleware('role:admin|Super Admin');

    Route::delete('admin/permissions/{permission}', [PermissionController::class, 'destroy'])
        ->name('admin.permissions.destroy')
        ->middleware('role:admin|Super Admin');

    Route::post('admin/permissions/assign', [PermissionController::class, 'assignToRole'])
        ->name('admin.permissions.assign')
        ->middleware('role:admin|Super Admin');

    // Admin subscription plan management
    Route::get('admin/subscription-plans', [SubscriptionPlanController::class, 'index'])
        ->name('admin.subscription-plans.index')
        ->middleware('role:admin|Super Admin');

    Route::get('admin/subscription-plans/create', [SubscriptionPlanController::class, 'create'])
        ->name('admin.subscription-plans.create')
        ->middleware('role:admin|Super Admin');

    Route::post('admin/subscription-plans', [SubscriptionPlanController::class, 'store'])
        ->name('admin.subscription-plans.store')
        ->middleware('role:admin|Super Admin');

    Route::get('admin/subscription-plans/{subscriptionPlan}/edit', [SubscriptionPlanController::class, 'edit'])
        ->name('admin.subscription-plans.edit')
        ->middleware('role:admin|Super Admin');

    Route::put('admin/subscription-plans/{subscriptionPlan}', [SubscriptionPlanController::class, 'update'])
        ->name('admin.subscription-plans.update')
        ->middleware('role:admin|Super Admin');

    Route::delete('admin/subscription-plans/{subscriptionPlan}', [SubscriptionPlanController::class, 'destroy'])
        ->name('admin.subscription-plans.destroy')
        ->middleware('role:admin|Super Admin');

    // Admin subscription feature management
    Route::get('admin/subscription-features', [SubscriptionFeatureController::class, 'index'])
        ->name('admin.subscription-features.index')
        ->middleware('role:admin|Super Admin');

    Route::get('admin/subscription-features/create', [SubscriptionFeatureController::class, 'create'])
        ->name('admin.subscription-features.create')
        ->middleware('role:admin|Super Admin');

    Route::post('admin/subscription-features', [SubscriptionFeatureController::class, 'store'])
        ->name('admin.subscription-features.store')
        ->middleware('role:admin|Super Admin');

    Route::get('admin/subscription-features/{subscriptionFeature}/edit', [SubscriptionFeatureController::class, 'edit'])
        ->name('admin.subscription-features.edit')
        ->middleware('role:admin|Super Admin');

    Route::put('admin/subscription-features/{subscriptionFeature}', [SubscriptionFeatureController::class, 'update'])
        ->name('admin.subscription-features.update')
        ->middleware('role:admin|Super Admin');

    Route::delete('admin/subscription-features/{subscriptionFeature}', [SubscriptionFeatureController::class, 'destroy'])
        ->name('admin.subscription-features.destroy')
        ->middleware('role:admin|Super Admin');

    // Admin user management
    Route::get('admin/users', [UserController::class, 'index'])
        ->name('admin.users.index')
        ->middleware('role:admin|Super Admin');

    Route::get('admin/assign-roles', [UserController::class, 'assignRoles'])
        ->name('admin.assignRoles')
        ->middleware('role:admin|Super Admin');

    Route::post('admin/users/{user}/roles', [UserController::class, 'updateRoles'])
        ->name('admin.users.updateRoles')
        ->middleware('role:admin|Super Admin');

    Route::post('admin/users/{user}/permissions', [UserController::class, 'updatePermissions'])
        ->name('admin.users.updatePermissions')
        ->middleware('role:admin|Super Admin');

    // Domain resources
    // NOTE: Feature-gated routes are defined above. Duplicating resource routes here would bypass gating.

    // Reports (feature-gated)
    Route::middleware(['subscription.feature:reports'])->group(function () {
        Route::get('/reports/animal-health', [AnimalHealthReportController::class, 'index'])
            ->name('reports.animal-health.index');

        Route::get('/reports/feeding-cost-analysis', [FeedingCostAnalysisReportController::class, 'index'])
            ->name('reports.feeding-cost-analysis.index');

        Route::get('/reports/vaccination-due', [VaccinationDueReportController::class, 'index'])
            ->name('reports.vaccination-due.index');

        Route::get('/reports/financial', [FinancialReportsController::class, 'index'])
            ->name('reports.financial.index');

        Route::get('/reports/conception-success-rate', [ConceptionSuccessRateReportController::class, 'index'])
            ->name('reports.conception-success-rate.index');

        Route::get('/reports/pregnancy-loss-analysis', [PregnancyLossAnalysisReportController::class, 'index'])
            ->name('reports.pregnancy-loss-analysis.index');

        Route::get('/reports/ai-vs-natural-breeding-success', [AiVsNaturalBreedingSuccessReportController::class, 'index'])
            ->name('reports.ai-vs-natural-breeding-success.index');

        Route::get('/reports/calving-interval', [CalvingIntervalReportController::class, 'index'])
            ->name('reports.calving-interval.index');

        Route::get('/reports/fertility-performance-per-cow', [FertilityPerformancePerCowReportController::class, 'index'])
            ->name('reports.fertility-performance-per-cow.index');

        Route::get('/reports/treatment-cost-per-animal', [TreatmentCostPerAnimalReportController::class, 'index'])
            ->name('reports.treatment-cost-per-animal.index');

        // Inventory Reports
        Route::get('/reports/inventory/current-stock-by-item', [CurrentStockByItemReportController::class, 'index'])
            ->name('reports.inventory.current-stock-by-item.index');

        Route::get('/reports/inventory/low-stock-alerts', [LowStockAlertsReportController::class, 'index'])
            ->name('reports.inventory.low-stock-alerts.index');

        Route::get('/reports/inventory/expired-medicine', [ExpiredMedicineReportController::class, 'index'])
            ->name('reports.inventory.expired-medicine.index');

        Route::get('/reports/inventory/feed-consumed-per-animal', [FeedConsumedPerAnimalReportController::class, 'index'])
            ->name('reports.inventory.feed-consumed-per-animal.index');

        Route::get('/reports/inventory/cost-of-feed-per-cow', [CostOfFeedPerCowReportController::class, 'index'])
            ->name('reports.inventory.cost-of-feed-per-cow.index');

        Route::get('/reports/inventory/medicine-used-per-disease', [MedicineUsedPerDiseaseReportController::class, 'index'])
            ->name('reports.inventory.medicine-used-per-disease.index');

        Route::get('/reports/inventory/monthly-consumption-summary', [MonthlyConsumptionSummaryReportController::class, 'index'])
            ->name('reports.inventory.monthly-consumption-summary.index');

        Route::get('/reports/inventory/wastage-loss', [WastageLossReportController::class, 'index'])
            ->name('reports.inventory.wastage-loss.index');
    });

    // Billing routes (allowed even if subscription expired; middleware handles exception)
    Route::get('/billing', [\App\Http\Controllers\BillingController::class, 'index'])
        ->middleware('role:farm owner')
        ->name('billing.index');
    Route::post('/billing/subscribe', [\App\Http\Controllers\BillingController::class, 'subscribe'])
        ->middleware('role:farm owner')
        ->name('billing.subscribe');

    // Payment gateway actions (initiate + callbacks)
    Route::post('/payments/initiate', [\App\Http\Controllers\PaymentGatewayController::class, 'initiate'])
        ->name('payments.initiate');
    Route::get('/payments/return', [\App\Http\Controllers\PaymentGatewayController::class, 'handleReturn'])
        ->name('payments.return');
    Route::post('/payments/webhook/{gateway}', [\App\Http\Controllers\PaymentGatewayController::class, 'webhook'])
        ->name('payments.webhook');

    // Plan page (farm owner only)
    Route::get('/plan', [\App\Http\Controllers\PlanController::class, 'index'])
        ->middleware('role:farm owner')
        ->name('plan.index');

    // Payment gateway settings (configure API keys + default gateway) - SaaS mode only
    Route::middleware('saas.only')->group(function () {
        Route::get('/settings/payment-gateways', [\App\Http\Controllers\PaymentGatewaySettingsController::class, 'index'])
            ->middleware('role:admin|Super Admin')
            ->name('settings.payment-gateways.index');
        Route::post('/settings/payment-gateways', [\App\Http\Controllers\PaymentGatewaySettingsController::class, 'update'])
            ->middleware('role:admin|Super Admin')
            ->name('settings.payment-gateways.update');
    });

    // Settings routes
    Route::get('/settings', [\App\Http\Controllers\SettingsController::class, 'index'])
        ->middleware('role:farm owner')
        ->name('settings.index');
    Route::post('/settings', [\App\Http\Controllers\SettingsController::class, 'update'])
        ->name('settings.update');
});

require __DIR__ . '/auth.php';
