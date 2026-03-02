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

        // Explicitly create Carbon instances for maximum compatibility
        if (isset($validated['from']) && !empty($validated['from'])) {
            $from = Carbon::make($validated['from'])->startOfDay();
        } else {
            $from = Carbon::now()->subDays(30)->startOfDay();
        }

        if (isset($validated['to']) && !empty($validated['to'])) {
            $to = Carbon::make($validated['to'])->endOfDay();
        } else {
            $to = Carbon::now()->endOfDay();
        }

        // For database queries: convert to strings for PHP 8.3 compatibility
        $fromForDb = $from->format('Y-m-d H:i:s');
        $toForDb = $to->format('Y-m-d H:i:s');

        //dd($from, $to);

        $activeAnimals = Animal::query()
            ->where('farm_id', $farm->id)
            ->where('status', 'active')
            ->count();

        $milkTotal = MilkRecord::query()
            ->where('farm_id', $farm->id)
            ->whereBetween('date', [$fromForDb, $toForDb])
            ->sum('quantity_liters');

        $days = max(1, $from->copy()->startOfDay()->diffInDays($to->copy()->startOfDay()) + 1);
        $milkAvgDaily = $milkTotal / $days;

        $milkSalesRevenue = MilkSale::query()
            ->where('farm_id', $farm->id)
            ->whereBetween('sale_date', [$fromForDb, $toForDb])
            ->sum('total_price');

        $feedingsCount = FeedingRecord::query()
            ->where('farm_id', $farm->id)
            ->whereBetween('feeding_date', [$fromForDb, $toForDb])
            ->count();

        $healthEventsCount = HealthEvent::query()
            ->where('farm_id', $farm->id)
            ->whereBetween('occurred_at', [$fromForDb, $toForDb])
            ->count();

        $vaccinationsDue7d = VaccinationRecord::query()
            ->where('farm_id', $farm->id)
            ->whereNotNull('next_due_at')
            ->where('next_due_at', '<=', now()->addDays(7))
            ->count();

        $expensesTotal = Expense::query()
            ->where('farm_id', $farm->id)
            ->whereBetween('incurred_on', [$fromForDb, $toForDb])
            ->sum('amount');

        // Trends (daily)
        $milkTrend = [];
        $revenueTrend = [];
        $feedingTrend = [];

        $cursor = $from->copy()->startOfDay();
        while ($cursor->lte($to)) {
            $dayStart = $cursor->copy()->startOfDay();
            $dayEnd = $cursor->copy()->endOfDay();
            $dayStartForDb = $dayStart->format('Y-m-d H:i:s');
            $dayEndForDb = $dayEnd->format('Y-m-d H:i:s');

            $milkTrend[] = [
                'date' => $cursor->toDateString(),
                'liters' => (float) MilkRecord::where('farm_id', $farm->id)
                    ->whereBetween('date', [$dayStartForDb, $dayEndForDb])
                    ->sum('quantity_liters'),
            ];

            $revenueTrend[] = [
                'date' => $cursor->toDateString(),
                'amount' => (float) MilkSale::where('farm_id', $farm->id)
                    ->whereBetween('sale_date', [$dayStartForDb, $dayEndForDb])
                    ->sum('total_price'),
            ];

            $feedingTrend[] = [
                'date' => $cursor->toDateString(),
                'count' => (int) FeedingRecord::where('farm_id', $farm->id)
                    ->whereBetween('feeding_date', [$dayStartForDb, $dayEndForDb])
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
