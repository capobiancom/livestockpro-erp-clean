<?php

namespace App\Http\Controllers;

use App\Models\Animal;
use App\Models\Expense;
use App\Models\FeedingRecord;
use App\Models\HealthEvent;
use App\Models\MilkRecord;
use App\Models\MilkSale;
use App\Models\VaccinationRecord;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;

class FarmProductivityDashboardController extends Controller
{
    public function index(Request $request)
    {
        /** @var \App\Models\User&\Spatie\Permission\Traits\HasRoles $user */
        $user = Auth::user();
        if (!$user) {
            return redirect()->route('login');
        }

        // Allow super admin to view a farm owner's dashboard by passing ?farm_id=...
        // Farm owners (and other farm-scoped users) will default to their own farm.
        $farmId = $request->integer('farm_id');

        if ($farmId && $user->hasRole('super admin')) {
            $farm = \App\Models\Farm::find($farmId);
        } else {
            $farm = $user->farm;
        }
        if (!$farm) {
            return Inertia::render('Dashboards/FarmProductivity', [
                'filters' => [
                    'from' => now()->subDays(30)->toDateString(),
                    'to' => now()->toDateString(),
                ],
                'kpis' => [
                    'milk_total_liters' => 0,
                    'milk_avg_daily_liters' => 0,
                    'milk_sales_revenue' => 0,
                    'feedings_count' => 0,
                    'health_events_count' => 0,
                    'vaccinations_due_7d' => 0,
                    'expenses_total' => 0,
                    'active_animals' => 0,
                ],
                'trends' => [
                    'milk' => [],
                    'revenue' => [],
                    'feedings' => [],
                ],
                'tables' => [
                    'upcoming_vaccinations' => [],
                    'recent_health_events' => [],
                ],
            ]);
        }

        $validated = $request->validate([
            'from' => ['nullable', 'date'],
            'to' => ['nullable', 'date', 'after_or_equal:from'],
        ]);

        $from = isset($validated['from'])
            ? Carbon::parse($validated['from'])->startOfDay()
            : now()->subDays(30)->startOfDay();

        $to = isset($validated['to'])
            ? Carbon::parse($validated['to'])->endOfDay()
            : now()->endOfDay();

        //dd($from, $to);

        $activeAnimals = Animal::query()
            ->where('farm_id', $farm->id)
            ->where('status', 'active')
            ->count();

        $milkTotal = MilkRecord::query()
            ->where('farm_id', $farm->id)
            ->whereBetween('date', [$from, $to])
            ->sum('quantity_liters');

        $days = max(1, $from->copy()->startOfDay()->diffInDays($to->copy()->startOfDay()) + 1);
        $milkAvgDaily = $milkTotal / $days;

        $milkSalesRevenue = MilkSale::query()
            ->where('farm_id', $farm->id)
            ->whereBetween('sale_date', [$from, $to])
            ->sum('total_price');

        $feedingsCount = FeedingRecord::query()
            ->where('farm_id', $farm->id)
            ->whereBetween('feeding_date', [$from, $to])
            ->count();

        $healthEventsCount = HealthEvent::query()
            ->where('farm_id', $farm->id)
            ->whereBetween('occurred_at', [$from, $to])
            ->count();

        $vaccinationsDue7d = VaccinationRecord::query()
            ->where('farm_id', $farm->id)
            ->whereNotNull('next_due_at')
            ->where('next_due_at', '<=', now()->addDays(7))
            ->count();

        $expensesTotal = Expense::query()
            ->where('farm_id', $farm->id)
            ->whereBetween('incurred_on', [$from, $to])
            ->sum('amount');

        // Trends (daily)
        $milkTrend = [];
        $revenueTrend = [];
        $feedingTrend = [];

        $cursor = $from->copy()->startOfDay();
        while ($cursor->lte($to)) {
            $dayStart = $cursor->copy()->startOfDay();
            $dayEnd = $cursor->copy()->endOfDay();

            $milkTrend[] = [
                'date' => $cursor->toDateString(),
                'liters' => (float) MilkRecord::where('farm_id', $farm->id)
                    ->whereBetween('date', [$dayStart, $dayEnd])
                    ->sum('quantity_liters'),
            ];

            $revenueTrend[] = [
                'date' => $cursor->toDateString(),
                'amount' => (float) MilkSale::where('farm_id', $farm->id)
                    ->whereBetween('sale_date', [$dayStart, $dayEnd])
                    ->sum('total_price'),
            ];

            $feedingTrend[] = [
                'date' => $cursor->toDateString(),
                'count' => (int) FeedingRecord::where('farm_id', $farm->id)
                    ->whereBetween('feeding_date', [$dayStart, $dayEnd])
                    ->count(),
            ];

            $cursor->addDay();
        }

        $upcomingVaccinations = VaccinationRecord::query()
            ->with(['animal:id,tag,name', 'disease:id,name'])
            ->where('farm_id', $farm->id)
            ->whereNotNull('next_due_at')
            ->whereBetween('next_due_at', [now()->startOfDay(), now()->addDays(30)->endOfDay()])
            ->orderBy('next_due_at')
            ->limit(10)
            ->get()
            ->map(function ($v) {
                $tag = $v->animal?->tag ?? '';
                return [
                    'due_date' => optional($v->next_due_at)->toDateString(),
                    'animal' => trim($tag . ' ' . ($v->animal?->name ?? '')) ?: '—',
                    'disease' => $v->disease?->name ?? '—',
                ];
            });

        $recentHealthEvents = HealthEvent::query()
            ->with(['animal:id,tag,name'])
            ->where('farm_id', $farm->id)
            ->orderByDesc('occurred_at')
            ->limit(10)
            ->get()
            ->map(function ($e) {
                $tag = $e->animal?->tag ?? '';
                return [
                    'date' => optional($e->occurred_at)->toDateString(),
                    'animal' => trim($tag . ' ' . ($e->animal?->name ?? '')) ?: '—',
                    'type' => $e->event_type ?? '—',
                    'notes' => $e->notes ?? null,
                ];
            });

        return Inertia::render('Dashboards/FarmProductivity', [
            'filters' => [
                'from' => $from->toDateString(),
                'to' => $to->toDateString(),
            ],
            'kpis' => [
                'milk_total_liters' => (float) $milkTotal,
                'milk_avg_daily_liters' => (float) $milkAvgDaily,
                'milk_sales_revenue' => (float) $milkSalesRevenue,
                'feedings_count' => (int) $feedingsCount,
                'health_events_count' => (int) $healthEventsCount,
                'vaccinations_due_7d' => (int) $vaccinationsDue7d,
                'expenses_total' => (float) $expensesTotal,
                'active_animals' => (int) $activeAnimals,
            ],
            'trends' => [
                'milk' => $milkTrend,
                'revenue' => $revenueTrend,
                'feedings' => $feedingTrend,
            ],
            'tables' => [
                'upcoming_vaccinations' => $upcomingVaccinations,
                'recent_health_events' => $recentHealthEvents,
            ],
        ]);
    }
}
