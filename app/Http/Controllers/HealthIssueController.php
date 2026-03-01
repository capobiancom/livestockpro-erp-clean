<?php

namespace App\Http\Controllers;

use App\Models\HealthIssue;
use App\Models\HealthEvent; // Import HealthEvent model
use App\Models\EventType; // Import EventType model
use App\Models\Animal;
use App\Models\StaffProfile;
use App\Models\User; // Import User model
use App\Models\Disease; // Import Disease model
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth; // Import Auth facade
use Illuminate\Support\Facades\DB; // Import DB facade
use Inertia\Inertia;

class HealthIssueController extends Controller
{
    public function __construct()
    {
        $this->authorizeResource(HealthIssue::class, 'health_issue');
    }

    public function index(Request $request)
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();
        if (!$user) {
            return redirect()->route('login');
        }
        $q = $request->input('q');
        $healthIssues = HealthIssue::with(['animal', 'diagnosedBy', 'treatments', 'disease'])
            ->when($user->hasRole('farm owner'), function ($query) use ($user) {
                $query->where('farm_id', $user->farm_id);
            })
            ->when($q, fn($qb) => $qb->whereHas('disease', fn($qbd) => $qbd->where('name', 'like', "%$q%")))
            ->latest()
            ->paginate(20)
            ->withQueryString();

        return Inertia::render('HealthIssues/Index', [
            'healthIssues' => $healthIssues,
            'filters' => $request->only('q'),
        ]);
    }

    public function create()
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();
        if (!$user) {
            return redirect()->route('login');
        }
        $staff = StaffProfile::select('id', 'first_name', 'last_name', 'position')
            ->when($user->hasRole('farm owner'), function ($query) use ($user) {
                $query->where('farm_id', $user->farm_id);
            })
            ->orderBy('first_name')
            ->get()
            ->map(function ($s) {
                return [
                    'id' => $s->id,
                    'name' => trim($s->first_name . ' ' . $s->last_name),
                    'position' => $s->position ?? 'Staff'
                ];
            });

        $animals = Animal::select('id', 'tag', 'name')
            ->when($user->hasRole('farm owner'), function ($query) use ($user) {
                $query->where('farm_id', $user->farm_id);
            })->orderBy('tag')->get();

        $diseases = Disease::select('id', 'name')
            ->when($user->hasRole('farm owner'), function ($query) use ($user) {
                $query->where('farm_id', $user->farm_id);
            })->orderBy('name')->get();

        return Inertia::render('HealthIssues/Create', [
            'animals' => $animals,
            'staff' => $staff,
            'diseases' => $diseases,
        ]);
    }

    public function store(Request $request)
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();
        if (!$user) {
            return redirect()->route('login');
        }
        $validated = $request->validate([
            'animal_id' => 'required|exists:animals,id',
            'name' => 'required|string|max:255', // Add validation for 'name'
            'disease_id' => 'nullable|exists:diseases,id',
            'diagnosed_at' => 'nullable|date',
            'severity' => 'nullable|in:mild,moderate,severe',
            'symptoms' => 'nullable|string',
            'diagnosis' => 'nullable|string',
            'status' => 'required|in:active,recovered,chronic,deceased',
            'recovered_at' => 'nullable|date',
            'diagnosed_by' => 'nullable|exists:staff_profiles,id',
            'notes' => 'nullable|string',
        ]);

        // Enforce farm_id for farm owners
        if ($user->hasRole('farm owner')) {
            $animal = Animal::find($validated['animal_id']);
            if ($animal && $animal->farm_id != $user->farm_id) {
                return back()->withErrors(['animal_id' => 'The selected animal does not belong to your farm.'])->withInput();
            }
            $validated['farm_id'] = $user->farm_id;
            $validated['user_id'] = $user->id;
        }
        // Validate that the selected staff belongs to the user's farm
        if ($user->hasRole('farm owner') && !empty($validated['diagnosed_by'])) {
            $staffProfile = StaffProfile::where('id', $validated['diagnosed_by'])
                ->where('farm_id', $user->farm_id)
                ->first();
            if (!$staffProfile) {
                return back()->withErrors(['diagnosed_by' => 'The selected staff member does not belong to your farm.'])->withInput();
            }
        }

        DB::transaction(function () use ($validated, $user) {
            $healthIssue = HealthIssue::create($validated);

            // Create a corresponding HealthEvent
            $healthEventType = EventType::where('name', 'Health Issue')->first();

            if (!$healthEventType) {
                // If the "Health Issue" event type doesn't exist, you may want to create it or handle this case as needed
                $healthEventType = EventType::create([
                    'name' => 'Health Issue',
                    'description' => 'Event type for recording health issues of animals',
                    'farm_id' => $healthIssue->farm_id,
                    'user_id' => $healthIssue->user_id,
                ]);
            }

            if ($healthEventType) {
                HealthEvent::create([
                    'animal_id' => $healthIssue->animal_id,
                    'health_issue_id' => $healthIssue->id,
                    'title' => 'Health Issue: ' . $healthIssue->name,
                    'diagnosed_at' => $healthIssue->diagnosed_at ?? now(),
                    'description' => $healthIssue->diagnosis ?? $healthIssue->symptoms ?? 'Health issue recorded',
                    'event_type_id' => $healthEventType->id,
                    'date' => $healthIssue->diagnosed_at ?? now(),
                    'notes' => 'Health issue recorded: ' . $healthIssue->name . ($healthIssue->disease ? ' (' . $healthIssue->disease->name . ')' : ''),
                    'farm_id' => $healthIssue->farm_id,
                    'user_id' => $healthIssue->user_id,
                ]);
            }
        });

        return redirect()->route('health-issues.index')
            ->with('success', 'Health issue recorded successfully');
    }

    public function show(HealthIssue $healthIssue)
    {
        $this->authorize('view', $healthIssue);
        $healthIssue->load(['animal', 'diagnosedBy', 'treatments.administeredBy']);

        return Inertia::render('HealthIssues/Show', [
            'healthIssue' => $healthIssue
        ]);
    }

    public function edit(HealthIssue $healthIssue)
    {
        $this->authorize('update', $healthIssue);
        /** @var \App\Models\User $user */
        $user = Auth::user();
        if (!$user) {
            return redirect()->route('login');
        }
        $healthIssue->load(['animal', 'diagnosedBy', 'treatments', 'disease']);

        $staff = StaffProfile::select('*')
            ->when($user->hasRole('farm owner'), function ($query) use ($user) {
                $query->where('farm_id', $user->farm_id);
            })
            ->orderBy('first_name')
            ->get()
            ->map(function ($s) {
                return [
                    'id' => $s->id,
                    'name' => trim($s->first_name . ' ' . $s->last_name),
                    'position' => $s->position ?? 'Staff'
                ];
            });


        $animals = Animal::select('id', 'tag', 'name')
            ->when($user->hasRole('farm owner'), function ($query) use ($user) {
                $query->where('farm_id', $user->farm_id);
            })->orderBy('tag')->get();

        $diseases = Disease::select('id', 'name')
            ->when($user->hasRole('farm owner'), function ($query) use ($user) {
                $query->where('farm_id', $user->farm_id);
            })->orderBy('name')->get();

        return Inertia::render('HealthIssues/Edit', [
            'healthIssue' => $healthIssue,
            'animals' => $animals,
            'staff' => $staff,
            'diseases' => $diseases,
        ]);
    }

    public function update(Request $request, HealthIssue $healthIssue)
    {
        $this->authorize('update', $healthIssue);
        /** @var \App\Models\User $user */
        $user = Auth::user();
        if (!$user) {
            return redirect()->route('login');
        }

        $staffProfile = StaffProfile::where('id', (int) $request->input('diagnosed_by'))->first();

        $request->all()['diagnosed_by'] = $staffProfile ? $staffProfile : null;

        $validated = $request->validate([
            'animal_id' => 'required|exists:animals,id',
            'name' => 'required|string|max:255', // Add validation for 'name'
            'disease_id' => 'nullable|exists:diseases,id',
            'diagnosed_at' => 'nullable|date',
            'severity' => 'nullable|in:mild,moderate,severe',
            'symptoms' => 'nullable|string',
            'diagnosis' => 'nullable|string',
            'status' => 'required|in:active,recovered,chronic,deceased',
            'recovered_at' => 'nullable|date',
            'diagnosed_by' => 'nullable|exists:staff_profiles,id',
            'notes' => 'nullable|string',
        ]);

        // Enforce farm_id for farm owners
        if ($user->hasRole('farm owner')) {
            $animal = Animal::find($validated['animal_id']);
            if ($animal && $animal->farm_id != $user->farm_id) {
                return back()->withErrors(['animal_id' => 'The selected animal does not belong to your farm.'])->withInput();
            }
            $validated['farm_id'] = $user->farm_id;
        }
        // Validate that the selected staff belongs to the user's farm
        if ($user->hasRole('farm owner') && !empty($validated['diagnosed_by'])) {
            $staffProfile = StaffProfile::where('id', $validated['diagnosed_by'])
                ->where('farm_id', $user->farm_id)
                ->first();
            if (!$staffProfile) {
                return back()->withErrors(['diagnosed_by' => 'The selected staff member does not belong to your farm.'])->withInput();
            }
        }

        $healthIssue->update($validated);

        return redirect()->route('health-issues.index')
            ->with('success', 'Health issue updated successfully');
    }

    public function destroy(HealthIssue $healthIssue)
    {
        $this->authorize('delete', $healthIssue);
        $healthIssue->delete();

        return redirect()->route('health-issues.index')
            ->with('success', 'Health issue deleted successfully');
    }
}
