<?php

namespace App\Http\Controllers;

use App\Models\PregnancyCheckup;
use App\Models\Pregnancy;
use App\Models\User;
use App\Enums\CheckupResult;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;
use Illuminate\Support\Facades\DB;

class PregnancyCheckupController extends Controller
{
    public function __construct()
    {
        $this->authorizeResource(PregnancyCheckup::class, 'pregnancy_checkup');
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        /** @var User $user */
        $user = Auth::user();
        if (!$user) {
            return redirect()->route('login');
        }

        $q = $request->input('q');
        $baseQuery = PregnancyCheckup::when($user->hasRole('farm owner'), function ($query) use ($user) {
            $query->whereHas('pregnancy', fn($pq) => $pq->where('farm_id', $user->farm_id));
        });

        $totalCheckups = $baseQuery->count();
        $normalCheckups = (clone $baseQuery)->where('checkup_result', CheckupResult::Normal->value)->count();
        $riskCheckups = (clone $baseQuery)->where('checkup_result', CheckupResult::Risk->value)->count();
        $criticalCheckups = (clone $baseQuery)->where('checkup_result', CheckupResult::Critical->value)->count();

        $pregnancyCheckups = $baseQuery->with(['pregnancy.animal', 'checkedBy'])
            ->when($q, fn($qb) => $qb->whereHas('pregnancy.animal', fn($aq) => $aq->where('tag', 'like', "%$q%")->orWhere('name', 'like', "%$q%"))
                ->orWhere('checkup_result', 'like', "%$q%"))
            ->latest('checkup_date')
            ->paginate(20)
            ->withQueryString();

        return Inertia::render('PregnancyCheckups/Index', [
            'pregnancyCheckups' => $pregnancyCheckups,
            'filters' => $request->only('q'),
            'statistics' => [
                'total_checkups' => $totalCheckups,
                'normal_checkups' => $normalCheckups,
                'risk_checkups' => $riskCheckups,
                'critical_checkups' => $criticalCheckups,
            ],
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(Request $request)
    {
        /** @var User $user */
        $user = Auth::user();
        if (!$user) {
            return redirect()->route('login');
        }

        $this->authorize('create', PregnancyCheckup::class);

        $pregnancies = Pregnancy::select('id', 'animal_id', 'pregnancy_confirmed_date')
            ->when($user->hasRole('farm owner'), function ($query) use ($user) {
                $query->where('farm_id', $user->farm_id);
            })
            ->with('animal:id,tag,name')
            ->get()
            ->map(function ($pregnancy) {
                $pregnancy->display_name = "{$pregnancy->animal->tag} - Confirmed: {$pregnancy->pregnancy_confirmed_date->format('Y-m-d')}";
                return $pregnancy;
            });

        $vets = User::select('id', 'name')
            ->when($user->hasRole('farm owner'), function ($query) use ($user) {
                $query->where('farm_id', $user->farm_id);
            })
            ->whereHas('roles', function ($query) {
                $query->where('name', 'vet');
            })
            ->get();

        $checkupResults = CheckupResult::cases();

        return Inertia::render('PregnancyCheckups/Create', [
            'pregnancies' => $pregnancies,
            'vets' => $vets,
            'checkupResults' => $checkupResults,
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        /** @var User $user */
        $user = Auth::user();
        if (!$user) {
            return redirect()->route('login');
        }

        $this->authorize('create', PregnancyCheckup::class);

        $validated = $request->validate([
            'pregnancy_id' => 'required|exists:pregnancies,id',
            'checkup_date' => 'required|date_format:Y-m-d H:i:s',
            'checkup_result' => 'required|in:' . implode(',', array_column(CheckupResult::cases(), 'value')),
            'observations' => 'nullable|string',
            'checked_by' => 'nullable|exists:users,id',
        ]);

        // Enforce farm_id for farm owners
        if ($user->hasRole('farm owner')) {
            $validated['farm_id'] = $user->farm_id;
            $validated['user_id'] = $user->id;
        }

        $pregnancyCheckup = PregnancyCheckup::create($validated);

        // Automation: Update pregnancy status if checkup result is Aborted
        if ($validated['checkup_result'] === CheckupResult::Aborted->value) {
            $pregnancy = Pregnancy::find($validated['pregnancy_id']);
            if ($pregnancy) {
                $pregnancy->update(['pregnancy_status' => \App\Enums\PregnancyStatus::Aborted->value]);
            }
        }

        return redirect()->route('pregnancy-checkups')->with('success', 'Pregnancy checkup recorded successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(PregnancyCheckup $pregnancyCheckup)
    {
        $this->authorize('view', $pregnancyCheckup);

        $pregnancyCheckup->load(['pregnancy.animal', 'checkedBy']);

        return Inertia::render('PregnancyCheckups/Show', [
            'pregnancyCheckup' => $pregnancyCheckup,
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(PregnancyCheckup $pregnancyCheckup)
    {
        /** @var User $user */
        $user = Auth::user();
        if (!$user) {
            return redirect()->route('login');
        }

        $this->authorize('update', $pregnancyCheckup);

        $pregnancies = Pregnancy::select('id', 'animal_id', 'pregnancy_confirmed_date')
            ->when($user->hasRole('farm owner'), function ($query) use ($user) {
                $query->where('farm_id', $user->farm_id);
            })
            ->with('animal:id,tag,name')
            ->get()
            ->map(function ($pregnancy) {
                $pregnancy->display_name = "{$pregnancy->animal->tag} - Confirmed: {$pregnancy->pregnancy_confirmed_date->format('Y-m-d')}";
                return $pregnancy;
            });

        $vets = User::select('id', 'name')
            ->when($user->hasRole('farm owner'), function ($query) use ($user) {
                $query->where('farm_id', $user->farm_id);
            })
            ->whereHas('roles', function ($query) {
                $query->where('name', 'vet');
            })
            ->get();

        $checkupResults = CheckupResult::cases();

        return Inertia::render('PregnancyCheckups/Edit', [
            'pregnancyCheckup' => $pregnancyCheckup,
            'pregnancies' => $pregnancies,
            'vets' => $vets,
            'checkupResults' => $checkupResults,
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, PregnancyCheckup $pregnancyCheckup)
    {
        /** @var User $user */
        $user = Auth::user();
        if (!$user) {
            return redirect()->route('login');
        }

        $this->authorize('update', $pregnancyCheckup);

        $validated = $request->validate([
            'pregnancy_id' => 'required|exists:pregnancies,id',
            'checkup_date' => 'required|date_format:Y-m-d H:i:s',
            'checkup_result' => 'required|in:' . implode(',', array_column(CheckupResult::cases(), 'value')),
            'observations' => 'nullable|string',
            'checked_by' => 'nullable|exists:users,id',
        ]);

        $pregnancyCheckup->update($validated);

        return redirect()->route('pregnancy-checkups.show', $pregnancyCheckup->pregnancy_id)->with('success', 'Pregnancy checkup updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(PregnancyCheckup $pregnancyCheckup)
    {
        $this->authorize('delete', $pregnancyCheckup);

        $pregnancyId = $pregnancyCheckup->pregnancy_id;
        $pregnancyCheckup->delete();

        return redirect()->route('pregnancies.show', $pregnancyId)->with('success', 'Pregnancy checkup deleted successfully.');
    }
}
