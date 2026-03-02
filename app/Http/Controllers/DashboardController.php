<?php

namespace App\Http\Controllers;

use App\Models\Animal;
use App\Models\FeedingRecord;
use App\Models\VaccinationRecord;
use App\Models\StaffProfile;
use App\Models\Farm;
use App\Models\HealthEvent;
use App\Models\HealthIssue;
use App\Models\Expense;
use App\Models\Sale;
use App\Models\Purchase;
use App\Models\JournalEntryLine;
use App\Models\ChartOfAccount;
use App\Enums\JournalEntryStatus;
use App\Enums\ChartOfAccountType;
use App\Models\MilkRecord;
use App\Models\MilkSale;
use App\Models\Logistic;
use App\Models\Pregnancy; // Import Pregnancy model
use App\Models\StockMovement; // Import StockMovement model
use App\Models\InventoryItem; // Import InventoryItem model
use App\Models\Medicine; // Import Medicine model
use App\Models\PregnancyCheckup; // Import PregnancyCheckup model
use App\Models\CalvingRecord; // Import CalvingRecord model
use App\Models\Calf; // Import Calf model
use App\Enums\PregnancyStatus; // Import PregnancyStatus enum
use App\Enums\CheckupResult; // Import CheckupResult enum
use App\Enums\CalvingOutcome; // Import CalvingOutcome enum
use App\Enums\HealthStatus; // Import HealthStatus enum
use Illuminate\Http\Request;
use Inertia\Inertia;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth; // Ensure Auth facade is imported

class DashboardController extends Controller
{
    public function index(Request $request) // Inject Request
    {
        // Get current user
        $user = Auth::user(); // Use Auth facade for consistency

        // If no user is authenticated, redirect to login
        if (!$user) {
            return redirect()->route('login');
        }

        // Allow farm owners to view dashboards for users within their farm by passing ?user_id=...
        // Allow Super Admin to view any user's dashboard by passing ?user_id=...
        $targetUserId = $request->integer('user_id');

        if ($targetUserId) {
            $targetUser = \App\Models\User::find($targetUserId);

            if (!$targetUser) {
                abort(404);
            }


            if ($user->hasRole('Super Admin')) {
                $user = $targetUser;
            } elseif ($user->hasRole('farm owner') && $targetUser->farm_id === $user->farm_id) {
                $user = $targetUser;
            } else {
                abort(403);
            }
        }

        $farm = $user->farm; // Assuming a user has one farm, or get the active farm


        // If the user has no associated farm, redirect to a page to create one or handle appropriately
        if (!$farm) {
            // You might want to redirect to a farm creation page or show a message
            // For now, we'll redirect to dashboard without farm data
            return Inertia::render('Dashboard', [
                'stats' => [],
                'financial' => [],
                'production' => [],
                'health' => [],
                'operations' => [],
                'milkSales' => [],
                'animalsByStatus' => [],
                'recentFeedings' => [],
                'recentHealthEvents' => [],
                'upcomingVaccinations' => [],
                'lowStockItems' => [],
                'feedingTrend' => [],
                'milkProductionTrend' => [],
                'animalsByFarm' => [],
                'revenueTrend' => [],
                'monthlyComparison' => [],
                'reproductionStats' => [ // Initialize with default values
                    'total_pregnancies' => 0,
                    'ongoing_pregnancies' => 0,
                    'aborted_pregnancies' => 0,
                    'completed_pregnancies' => 0,
                    'total_pregnancy_checkups' => 0,
                    'normal_checkups' => 0,
                    'risk_checkups' => 0,
                    'critical_checkups' => 0,
                    'total_calving_records' => 0,
                    'successful_calvings' => 0,
                    'stillbirth_calvings' => 0,
                    'complication_calvings' => 0,
                    'total_calves_born' => 0,
                    'healthy_calves' => 0,
                    'weak_calves' => 0,
                    'critical_calves' => 0,
                ],
                'show_demo_seeding_popup' => false,
                'farm_id' => null,
            ]);
        }

        // Ensure the farm model is reloaded with the latest data, especially after a redirect
        $farm->refresh();

        $showDemoSeedingPopup = false;
        if ($request->boolean('show_demo_seeding_popup') && !$farm->demo_data_seeded) {
            $showDemoSeedingPopup = true;
        }

        // Main Statistics Cards
        $stats = [
            'total_animals' => Animal::where('farm_id', $farm->id)->count(),
            'active_animals' => Animal::where('farm_id', $farm->id)->where('status', 'active')->count(),
            'total_staff' => StaffProfile::where('farm_id', $farm->id)->count(),
            'total_farms' => Farm::where('id', $farm->id)->count(), // Only count the current farm
            'low_stock_items' => InventoryItem::where('farm_id', $farm->id)->whereColumn('quantity', '<=', 'min_quantity')->count(),
            'feedings_today' => FeedingRecord::where('farm_id', $farm->id)->whereDate('feeding_date', today())->count(),
            'vaccinations_due' => VaccinationRecord::where('farm_id', $farm->id)->where('next_due_at', '<=', now()->addDays(7))->count(),
            'active_health_issues' => HealthIssue::where('farm_id', $farm->id)->where('status', 'active')->count(),
        ];

        // Reproduction Statistics
        $reproductionStats = [
            'total_pregnancies' => Pregnancy::where('farm_id', $farm->id)->count(),
            'ongoing_pregnancies' => Pregnancy::where('farm_id', $farm->id)->where('pregnancy_status', PregnancyStatus::Ongoing->value)->count(),
            'aborted_pregnancies' => Pregnancy::where('farm_id', $farm->id)->where('pregnancy_status', PregnancyStatus::Aborted->value)->count(),
            'completed_pregnancies' => Pregnancy::where('farm_id', $farm->id)->where('pregnancy_status', PregnancyStatus::Completed->value)->count(),
            'total_pregnancy_checkups' => PregnancyCheckup::whereHas('pregnancy', fn($q) => $q->where('farm_id', $farm->id))->count(),
            'normal_checkups' => PregnancyCheckup::whereHas('pregnancy', fn($q) => $q->where('farm_id', $farm->id))->where('checkup_result', CheckupResult::Normal->value)->count(),
            'risk_checkups' => PregnancyCheckup::whereHas('pregnancy', fn($q) => $q->where('farm_id', $farm->id))->where('checkup_result', CheckupResult::Risk->value)->count(),
            'critical_checkups' => PregnancyCheckup::whereHas('pregnancy', fn($q) => $q->where('farm_id', $farm->id))->where('checkup_result', CheckupResult::Critical->value)->count(),
            'total_calving_records' => CalvingRecord::where('farm_id', $farm->id)->count(),
            'successful_calvings' => CalvingRecord::where('farm_id', $farm->id)->where('calving_outcome', CalvingOutcome::Successful->value)->count(),
            'stillbirth_calvings' => CalvingRecord::where('farm_id', $farm->id)->where('calving_outcome', CalvingOutcome::Stillbirth->value)->count(),
            'complication_calvings' => CalvingRecord::where('farm_id', $farm->id)->where('calving_outcome', CalvingOutcome::Complication->value)->count(),
            'total_calves_born' => Calf::where('farm_id', $farm->id)->count(),
            'healthy_calves' => Calf::where('farm_id', $farm->id)->where('health_status', HealthStatus::Healthy->value)->count(),
            'weak_calves' => Calf::where('farm_id', $farm->id)->where('health_status', HealthStatus::Weak->value)->count(),
            'critical_calves' => Calf::where('farm_id', $farm->id)->where('health_status', HealthStatus::Critical->value)->count(),
        ];

        // Financial Overview (account-based, from posted journal entries)
        // NOTE: This makes dashboard stats consistent with accounting reports (P&L, etc.)
        $baseLines = JournalEntryLine::query()
            ->whereHas('journalEntry', function ($q) use ($farm) {
                $q->where('farm_id', $farm->id)
                    ->where('status', JournalEntryStatus::Posted->value);
            })
            ->whereHas('account', function ($q) {
                $q->whereIn('type', [
                    ChartOfAccountType::Income->value,
                    ChartOfAccountType::Expense->value,
                ]);
            });


        $incomeAllTime = (clone $baseLines)
            ->whereHas('account', fn($q) => $q->where('type', ChartOfAccountType::Income->value))
            ->sum('credit_amount');

        $expenseAllTime = (clone $baseLines)
            ->whereHas('account', fn($q) => $q->where('type', ChartOfAccountType::Expense->value))
            ->sum('debit_amount');

        $monthStart = now()->startOfMonth();
        $monthEnd = now()->endOfMonth();

        $incomeThisMonth = (clone $baseLines)
            ->whereBetween('created_at', [$monthStart, $monthEnd])
            ->whereHas('account', fn($q) => $q->where('type', ChartOfAccountType::Income->value))
            ->sum('credit_amount');

        $expenseThisMonth = (clone $baseLines)
            ->whereBetween('created_at', [$monthStart, $monthEnd])
            ->whereHas('account', fn($q) => $q->where('type', ChartOfAccountType::Expense->value))
            ->sum('debit_amount');

        // Purchases: sum of posted lines hitting purchase-related expense accounts.
        // We treat "purchase" as a subset of expenses (e.g., inventory/COGS/stock purchases).
        // If you have a dedicated account type for purchases later, we can switch to that.
        $purchaseAccountIds = ChartOfAccount::query()
            ->where('farm_id', $farm->id)
            ->where('type', ChartOfAccountType::Expense->value)
            ->where(function ($q) {
                $q->where('name', 'like', '%purchase%')
                    ->orWhere('name', 'like', '%inventory%')
                    ->orWhere('name', 'like', '%stock%')
                    ->orWhere('name', 'like', '%cogs%')
                    ->orWhere('code', 'like', '%PUR%');
            })
            ->pluck('id');

        $purchaseAllTime = 0;
        $purchaseThisMonth = 0;

        if ($purchaseAccountIds->isNotEmpty()) {
            $purchaseAllTime = JournalEntryLine::query()
                ->whereIn('account_id', $purchaseAccountIds)
                ->whereHas('journalEntry', function ($q) use ($farm) {
                    $q->where('farm_id', $farm->id)
                        ->where('status', JournalEntryStatus::Posted->value);
                })
                ->sum('debit_amount');

            $purchaseThisMonth = JournalEntryLine::query()
                ->whereIn('account_id', $purchaseAccountIds)
                ->whereHas('journalEntry', function ($q) use ($farm) {
                    $q->where('farm_id', $farm->id)
                        ->where('status', JournalEntryStatus::Posted->value);
                })
                ->whereBetween('created_at', [$monthStart, $monthEnd])
                ->sum('debit_amount');
        }

        $financial = [
            'total_revenue' => $incomeAllTime,
            'total_expenses' => $expenseAllTime,
            'total_purchases' => $purchaseAllTime,
            'this_month_revenue' => $incomeThisMonth,
            'this_month_expenses' => $expenseThisMonth,
            'this_month_purchases' => $purchaseThisMonth,
            'profit' => $incomeAllTime - $expenseAllTime,
        ];

        // Production Statistics
        $production = [
            'total_milk_production' => MilkRecord::where('farm_id', $farm->id)->sum('quantity_liters'),
            'today_milk_production' => MilkRecord::where('farm_id', $farm->id)->whereDate('date', today())->sum('quantity_liters'),
            'this_month_milk' => MilkRecord::where('farm_id', $farm->id)->whereMonth('date', now()->month)->whereYear('date', now()->year)->sum('quantity_liters'),
            'avg_daily_milk' => MilkRecord::where('farm_id', $farm->id)->whereMonth('date', now()->month)->whereYear('date', now()->year)->avg('quantity_liters'),
        ];

        // Milk Sales Statistics
        $milkSales = [
            'total_sales' => MilkSale::where('farm_id', $farm->id)->count(),
            'total_quantity_sold' => MilkSale::where('farm_id', $farm->id)->sum('quantity'),
            'total_revenue' => MilkSale::where('farm_id', $farm->id)->sum('total_price'),
            'this_month_sales' => MilkSale::where('farm_id', $farm->id)->whereMonth('sale_date', now()->month)->whereYear('sale_date', now()->year)->count(),
            'this_month_quantity' => MilkSale::where('farm_id', $farm->id)->whereMonth('sale_date', now()->month)->whereYear('sale_date', now()->year)->sum('quantity'),
            'this_month_revenue' => MilkSale::where('farm_id', $farm->id)->whereMonth('sale_date', now()->month)->whereYear('sale_date', now()->year)->sum('total_price'),
            'today_sales' => MilkSale::where('farm_id', $farm->id)->whereDate('sale_date', today())->count(),
            'today_revenue' => MilkSale::where('farm_id', $farm->id)->whereDate('sale_date', today())->sum('total_price'),
        ];

        // Health Overview
        $health = [
            'total_health_events' => HealthEvent::where('farm_id', $farm->id)->count(),
            'active_health_events' => HealthEvent::where('farm_id', $farm->id)->whereNull('resolved_at')->count(),
            'resolved_health_events' => HealthEvent::where('farm_id', $farm->id)->whereNotNull('resolved_at')->count(),
            'total_health_cost' => HealthEvent::where('farm_id', $farm->id)->sum('cost'),
            'active_health_issues' => HealthIssue::where('farm_id', $farm->id)->where('status', 'active')->count(),
            'recovered_animals' => HealthIssue::where('farm_id', $farm->id)->where('status', 'recovered')->count(),
        ];

        // Operations Overview
        $operations = [
            'total_logistics' => Logistic::where('farm_id', $farm->id)->count(),
            'completed_trips' => Logistic::where('farm_id', $farm->id)->whereNotNull('arrival_at')->count(),
            'pending_trips' => Logistic::where('farm_id', $farm->id)->whereNull('arrival_at')->count(),
            'animals_transported' => Logistic::where('farm_id', $farm->id)->sum('animals_count'),
        ];

        // Stock Movements by Unit
        $inventoryItemMovements = StockMovement::where('stock_movements.farm_id', $farm->id)
            ->where('stock_movements.item_type', InventoryItem::class)
            ->join('inventory_items', 'stock_movements.item_id', '=', 'inventory_items.id')
            ->selectRaw('inventory_items.unit, stock_movements.movement_type, SUM(stock_movements.quantity) as total_quantity')
            ->groupBy('inventory_items.unit', 'stock_movements.movement_type');

        $medicineMovements = StockMovement::where('stock_movements.farm_id', $farm->id)
            ->where('stock_movements.item_type', Medicine::class)
            ->join('medicines', 'stock_movements.item_id', '=', 'medicines.id')
            ->selectRaw('medicines.unit, stock_movements.movement_type, SUM(stock_movements.quantity) as total_quantity')
            ->groupBy('medicines.unit', 'stock_movements.movement_type');

        $combinedStockMovements = $inventoryItemMovements->unionAll($medicineMovements);

        // Execute the combined query and group by unit
        $rawGroupedMovements = $combinedStockMovements->get();

        $stockMovementsByUnit = $rawGroupedMovements->groupBy('unit');

        $formattedStockMovements = [];
        foreach ($stockMovementsByUnit as $unit => $movements) {
            $totalIn = $movements->where('movement_type', 'in')->sum('total_quantity');
            $totalOut = $movements->where('movement_type', 'out')->sum('total_quantity');
            $formattedStockMovements[] = [
                'unit' => $unit,
                'total_in' => $totalIn,
                'total_out' => $totalOut,
            ];
        }

        // Animal status breakdown
        $animalsByStatus = Animal::where('farm_id', $farm->id)->selectRaw('status, COUNT(*) as count')
            ->groupBy('status')
            ->get()
            ->pluck('count', 'status');

        // Recent Activities
        $recentFeedings = FeedingRecord::with(['animal'])
            ->where('farm_id', $farm->id)
            ->orderBy('feeding_date', 'desc')
            ->limit(5)
            ->get();

        $recentHealthEvents = HealthEvent::with(['animal'])
            ->where('farm_id', $farm->id)
            ->orderBy('occurred_at', 'desc')
            ->limit(5)
            ->get();

        $upcomingVaccinations = VaccinationRecord::with(['animal'])
            ->where('farm_id', $farm->id)
            ->where('next_due_at', '>=', now())
            ->where('next_due_at', '<=', now()->addDays(30))
            ->orderBy('next_due_at', 'asc')
            ->limit(5)
            ->get();

        $lowStockItems = InventoryItem::where('farm_id', $farm->id)->whereColumn('quantity', '<=', 'min_quantity')
            ->orderBy('quantity', 'asc')
            ->limit(5)
            ->get();

        // Trends (last 7 days)
        $feedingTrend = [];
        for ($i = 6; $i >= 0; $i--) {
            $date = Carbon::today()->subDays($i);
            $feedingTrend[] = [
                'date' => $date->format('M d'),
                'count' => FeedingRecord::where('farm_id', $farm->id)->whereDate('feeding_date', $date)->count(),
            ];
        }

        $milkProductionTrend = [];
        for ($i = 6; $i >= 0; $i--) {
            $date = Carbon::today()->subDays($i);
            $milkProductionTrend[] = [
                'date' => $date->format('M d'),
                'quantity' => MilkRecord::where('farm_id', $farm->id)->whereDate('date', $date)->sum('quantity_liters'),
            ];
        }

        // Animals by farm
        $animalsByFarm = Farm::withCount('animals')
            ->where('id', $farm->id) // Only include the current farm
            ->get()
            ->filter(fn($farm) => $farm->animals_count > 0)
            ->sortByDesc('animals_count')
            ->map(fn($farm) => [
                'name' => $farm->name,
                'count' => $farm->animals_count,
            ])
            ->values();

        // Revenue trend (last 7 days)
        $revenueTrend = [];
        for ($i = 6; $i >= 0; $i--) {
            $date = Carbon::today()->subDays($i);
            $revenueTrend[] = [
                'date' => $date->format('M d'),
                'amount' => Sale::where('farm_id', $farm->id)->whereDate('sold_at', $date)->sum('price') + MilkSale::where('farm_id', $farm->id)->whereDate('sale_date', $date)->sum('total_price'),
            ];
        }

        // Monthly comparison (last 6 months)
        $monthlyComparison = [];
        for ($i = 5; $i >= 0; $i--) {
            $date = Carbon::now()->subMonths($i);
            $monthlyComparison[] = [
                'month' => $date->format('M'),
                'revenue' => Sale::where('farm_id', $farm->id)->whereYear('sold_at', $date->year)->whereMonth('sold_at', $date->month)->sum('price') + MilkSale::where('farm_id', $farm->id)->whereYear('sale_date', $date->year)->whereMonth('sale_date', $date->month)->sum('total_price'),
                'expenses' => Expense::where('farm_id', $farm->id)->whereYear('incurred_on', $date->year)->whereMonth('incurred_on', $date->month)->sum('amount'),
            ];
        }

        $farmUsers = [];

        if ($user->hasRole('farm owner') && $user->farm_id) {
            $farmUsers = \App\Models\User::query()
                ->where('farm_id', $user->farm_id)
                ->orderBy('name')
                ->get(['id', 'name', 'email']);
        }

        return Inertia::render('Dashboard', [
            'stats' => $stats,
            'financial' => $financial,
            'production' => $production,
            'health' => $health,
            'operations' => $operations,
            'milkSales' => $milkSales,
            'animalsByStatus' => $animalsByStatus,
            'recentFeedings' => $recentFeedings,
            'recentHealthEvents' => $recentHealthEvents,
            'upcomingVaccinations' => $upcomingVaccinations,
            'lowStockItems' => $lowStockItems,
            'feedingTrend' => $feedingTrend,
            'milkProductionTrend' => $milkProductionTrend,
            'animalsByFarm' => $animalsByFarm,
            'revenueTrend' => $revenueTrend,
            'monthlyComparison' => $monthlyComparison,
            'reproductionStats' => $reproductionStats, // Pass reproduction statistics
            'show_demo_seeding_popup' => $showDemoSeedingPopup, // Pass the determined value
            'farm_id' => $farm ? $farm->id : null, // Pass the farm_id
            'stockMovementsByUnit' => $formattedStockMovements, // Pass unit-wise stock movements
            'farmUsers' => $farmUsers,
        ]);
    }
}
