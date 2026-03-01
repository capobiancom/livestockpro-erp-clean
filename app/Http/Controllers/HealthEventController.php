<?php

namespace App\Http\Controllers;

use App\Models\HealthEvent;
use App\Models\Animal;
use App\Models\EventType;
use App\Models\HealthIssue; // Import HealthIssue model
use App\Models\StaffProfile;
use App\Models\User; // Import User model
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth; // Import Auth facade
use Inertia\Inertia;

class HealthEventController extends Controller
{
    public function __construct()
    {
        $this->authorizeResource(HealthEvent::class, 'health_event');
    }

    public function index(Request $request)
    {
        /** @var User $user */
        $user = Auth::user();
        if (!$user) {
            return redirect()->route('login');
        }
        $q = $request->input('q');
        $events = HealthEvent::with(['animal', 'treatedBy', 'eventType'])
            ->when($user->hasRole('farm owner'), function ($query) use ($user) {
                $query->where('farm_id', $user->farm_id);
            })
            ->when($q, fn($qb) => $qb->whereHas('animal', fn($aq) => $aq->where('tag', 'like', "%$q%"))
                ->orWhere('title', 'like', "%$q%"))
            ->latest()
            ->paginate(20)
            ->withQueryString();

        // Calculate statistics
        $baseQuery = HealthEvent::when($user->hasRole('farm owner'), function ($query) use ($user) {
            $query->where('farm_id', $user->farm_id);
        });

        $statistics = [
            'total_events' => (clone $baseQuery)->count(),
            'active_events' => (clone $baseQuery)->whereNull('resolved_at')->count(),
            'resolved_events' => (clone $baseQuery)->whereNotNull('resolved_at')->count(),
            'total_cost' => (clone $baseQuery)->sum('cost'),
        ];

        return Inertia::render('HealthEvents/Index', [
            'events' => $events,
            'statistics' => $statistics,
            'filters' => $request->only('q'),
        ]);
    }

    public function create()
    {
        /** @var User $user */
        $user = Auth::user();
        if (!$user) {
            return redirect()->route('login');
        }
        $animals = Animal::select('id', 'tag', 'name')
            ->when($user->hasRole('farm owner'), function ($query) use ($user) {
                $query->where('farm_id', $user->farm_id);
            })->get();

        $staff = StaffProfile::select('id', 'first_name', 'last_name')
            ->when($user->hasRole('farm owner'), function ($query) use ($user) {
                $query->where('farm_id', $user->farm_id);
            })->get()
            ->map(function ($s) {
                return [
                    'id' => $s->id,
                    'name' => trim($s->first_name . ' ' . $s->last_name),
                    'position' => $s->position ?? 'Staff'
                ];
            });

        $eventTypes = EventType::when($user->hasRole('farm owner'), function ($query) use ($user) {
            $query->where('farm_id', $user->farm_id);
        })->get();
        $healthIssues = HealthIssue::with('animal:id,tag,name')
            ->when($user->hasRole('farm owner'), function ($query) use ($user) {
                $query->where('farm_id', $user->farm_id);
            })
            ->get(); // Fetch all health issues with their animal_id, filtered by farm_id

        return Inertia::render('HealthEvents/Create', [
            'animals' => $animals,
            'staff' => $staff,
            'eventTypes' => $eventTypes,
            'healthIssues' => $healthIssues, // Pass health issues to the view
        ]);
    }

    public function store(Request $request)
    {
        /** @var User $user */
        $user = Auth::user();
        if (!$user) {
            return redirect()->route('login');
        }
        $validated = $request->validate([
            'animal_id' => 'required|exists:animals,id',
            'event_type_id' => 'required|exists:event_types,id',
            'health_issue_id' => 'nullable|exists:health_issues,id', // Add validation for health_issue_id
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'occurred_at' => 'required|date',
            'resolved_at' => 'nullable|date',
            'vet_fee' => 'nullable|numeric|min:0',
            'lab_cost' => 'nullable|numeric|min:0',
            'other_cost' => 'nullable|numeric|min:0',
            'treated_by' => 'nullable|exists:staff_profiles,id',
        ]);

        $validated['cost'] = (float) ($validated['vet_fee'] ?? 0)
            + (float) ($validated['lab_cost'] ?? 0)
            + (float) ($validated['other_cost'] ?? 0);

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
        if ($user->hasRole('farm owner') && !empty($validated['treated_by'])) {
            $staffProfile = StaffProfile::where('id', $validated['treated_by'])
                ->where('farm_id', $user->farm_id)
                ->first();
            if (!$staffProfile) {
                return back()->withErrors(['treated_by' => 'The selected staff member does not belong to your farm.'])->withInput();
            }
        }

        HealthEvent::create($validated);

        return redirect()->route('health-events.index')->with('success', 'Health event created successfully.');
    }

    public function show(HealthEvent $healthEvent)
    {
        $healthEvent->load(['animal', 'treatedBy', 'eventType', 'healthIssue']); // Load healthIssue relationship
        return Inertia::render('HealthEvents/Show', ['event' => $healthEvent]);
    }

    public function edit(HealthEvent $healthEvent)
    {
        /** @var User $user */
        $user = Auth::user();
        if (!$user) {
            return redirect()->route('login');
        }
        $animals = Animal::select('id', 'tag', 'name')
            ->when($user->hasRole('farm owner'), function ($query) use ($user) {
                $query->where('farm_id', $user->farm_id);
            })->get();
        $staff = StaffProfile::select('id', 'first_name', 'last_name')
            ->when($user->hasRole('farm owner'), function ($query) use ($user) {
                $query->where('farm_id', $user->farm_id);
            })->get();

        $eventTypes = EventType::when($user->hasRole('farm owner'), function ($query) use ($user) {
            $query->where('farm_id', $user->farm_id);
        })->get();
        $healthIssues = HealthIssue::with('animal:id,tag,name')
            ->when($user->hasRole('farm owner'), function ($query) use ($user) {
                $query->where('farm_id', $user->farm_id);
            })
            ->get(); // Fetch all health issues with their animal_id, filtered by farm_id

        return Inertia::render('HealthEvents/Edit', [
            'event' => $healthEvent,
            'animals' => $animals,
            'staff' => $staff,
            'eventTypes' => $eventTypes,
            'healthIssues' => $healthIssues, // Pass health issues to the view
        ]);
    }

    public function update(Request $request, HealthEvent $healthEvent)
    {
        /** @var User $user */
        $user = Auth::user();
        if (!$user) {
            return redirect()->route('login');
        }
        $validated = $request->validate([
            'animal_id' => 'required|exists:animals,id',
            'event_type_id' => 'required|exists:event_types,id',
            'health_issue_id' => 'nullable|exists:health_issues,id', // Add validation for health_issue_id
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'occurred_at' => 'required|date',
            'resolved_at' => 'nullable|date',
            'vet_fee' => 'nullable|numeric|min:0',
            'lab_cost' => 'nullable|numeric|min:0',
            'other_cost' => 'nullable|numeric|min:0',
            'treated_by' => 'nullable|exists:staff_profiles,id',
        ]);

        $validated['cost'] = (float) ($validated['vet_fee'] ?? 0)
            + (float) ($validated['lab_cost'] ?? 0)
            + (float) ($validated['other_cost'] ?? 0);

        // Enforce farm_id for farm owners
        if ($user->hasRole('farm owner')) {
            $animal = Animal::find($validated['animal_id']);
            if ($animal && $animal->farm_id != $user->farm_id) {
                return back()->withErrors(['animal_id' => 'The selected animal does not belong to your farm.'])->withInput();
            }
            $validated['farm_id'] = $user->farm_id;
        }
        // Validate that the selected staff belongs to the user's farm
        if ($user->hasRole('farm owner') && !empty($validated['treated_by'])) {
            $staffProfile = StaffProfile::where('id', $validated['treated_by'])
                ->where('farm_id', $user->farm_id)
                ->first();
            if (!$staffProfile) {
                return back()->withErrors(['treated_by' => 'The selected staff member does not belong to your farm.'])->withInput();
            }
        }

        $healthEvent->update($validated);

        return redirect()->route('health-events.index')->with('success', 'Health event updated successfully.');
    }

    public function destroy(HealthEvent $healthEvent)
    {
        $healthEvent->delete();
        return redirect()->route('health-events.index')->with('success', 'Health event deleted successfully.');
    }
}
