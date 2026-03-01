<?php

namespace App\Http\Controllers;

use App\Models\EventType;
use App\Models\Farm;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;

class EventTypeController extends Controller
{
    public function __construct()
    {
        $this->authorizeResource(EventType::class, 'eventType');
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

        $eventTypes = EventType::when($user->hasRole('farm owner'), function ($query) use ($user) {
            $query->where('farm_id', $user->farm_id);
        })
            ->latest()
            ->paginate(10)
            ->withQueryString();

        return Inertia::render('EventTypes/Index', [
            'eventTypes' => $eventTypes,
            'filters' => $request->only('q'),
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        /** @var User $user */
        $user = Auth::user();
        if (!$user) {
            return redirect()->route('login');
        }
        return Inertia::render('EventTypes/Create');
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

        $validated = $request->validate([
            'name' => 'required|string|max:255',
        ]);

        if ($user->hasRole('farm owner')) {
            $validated['farm_id'] = $user->farm_id;
            $validated['user_id'] = $user->id;
        }

        EventType::create($validated);

        return redirect()->route('event-types.index')->with('success', 'Event type created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(EventType $eventType)
    {
        $user = Auth::user();
        if (!$user) {
            return redirect()->route('login');
        }
        return Inertia::render('EventTypes/Show', ['eventType' => $eventType]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(EventType $eventType)
    {
        $user = Auth::user();
        if (!$user) {
            return redirect()->route('login');
        }
        return Inertia::render('EventTypes/Edit', ['eventType' => $eventType]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, EventType $eventType)
    {
        $user = Auth::user();
        if (!$user) {
            return redirect()->route('login');
        }

        $validated = $request->validate([
            'name' => 'required|string|max:255',
        ]);

        $eventType->update($validated);

        return redirect()->route('event-types.index')->with('success', 'Event type updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(EventType $eventType)
    {
        $user = Auth::user();
        if (!$user) {
            return redirect()->route('login');
        }
        $eventType->delete();
        return redirect()->route('event-types.index')->with('success', 'Event type deleted successfully.');
    }
}
