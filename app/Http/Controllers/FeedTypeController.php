<?php

namespace App\Http\Controllers;

use App\Models\FeedType;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth; // Import Auth facade
use Inertia\Inertia;

class FeedTypeController extends Controller
{
    public function __construct()
    {
        $this->authorizeResource(FeedType::class, 'feed_type');
    }

    public function index(Request $request)
    {
        $user = Auth::user();
        if (!$user) {
            return redirect()->route('login');
        }
        $q = $request->input('q');
        $feedTypes = FeedType::when($user->hasRole('farm owner'), function ($query) use ($user) {
            $query->where('farm_id', $user->farm_id);
        })
            ->when($q, fn($qb) => $qb->where('name', 'like', "%$q%"))
            ->latest()
            ->paginate(20)
            ->withQueryString();
        return Inertia::render('FeedTypes/Index', [
            'feedTypes' => $feedTypes,
            'filters' => $request->only('q'),
        ]);
    }

    public function create()
    {
        $user = Auth::user();
        if (!$user) {
            return redirect()->route('login');
        }
        return Inertia::render('FeedTypes/Create');
    }

    public function store(Request $request)
    {
        $user = Auth::user();
        if (!$user) {
            return redirect()->route('login');
        }
        $validated = $request->validate([
            'name' => 'required|string|max:100',
            'category' => 'required|in:grass,grain,hay,silage,supplements,concentrates,other',
            'unit' => 'nullable|string|max:20',
            'unit_cost' => 'nullable|numeric|min:0|max:9999999.99',
            'description' => 'nullable|string|max:500',
            'nutrient_info' => 'nullable|array',
        ]);

        // Enforce farm_id for farm owners
        if ($user->hasRole('farm owner')) {
            $validated['farm_id'] = $user->farm_id;
            $validated['user_id'] = $user->id;
        }

        FeedType::create($validated);

        return redirect()->route('feed-types.index')->with('success', 'Feed type created.');
    }

    public function show(FeedType $feed_type)
    {
        return Inertia::render('FeedTypes/Show', ['feedType' => $feed_type]);
    }

    public function edit(FeedType $feed_type)
    {
        $user = Auth::user();
        if (!$user) {
            return redirect()->route('login');
        }
        return Inertia::render('FeedTypes/Edit', ['feedType' => $feed_type]);
    }

    public function update(Request $request, FeedType $feed_type)
    {
        $user = Auth::user();
        if (!$user) {
            return redirect()->route('login');
        }
        $validated = $request->validate([
            'name' => 'required|string|max:100',
            'category' => 'required|in:grass,grain,hay,silage,supplements,concentrates,other',
            'unit' => 'nullable|string|max:20',
            'unit_cost' => 'nullable|numeric|min:0|max:9999999.99',
            'description' => 'nullable|string|max:500',
            'nutrient_info' => 'nullable|array',
        ]);

        // Enforce farm_id for farm owners
        if ($user->hasRole('farm owner')) {
            $validated['farm_id'] = $user->farm_id;
        }

        $feed_type->update($validated);

        return redirect()->route('feed-types.index')->with('success', 'Feed type updated.');
    }

    public function destroy(FeedType $feed_type)
    {
        $feed_type->delete();
        return redirect()->route('feed-types.index')->with('success', 'Feed type deleted.');
    }
}
