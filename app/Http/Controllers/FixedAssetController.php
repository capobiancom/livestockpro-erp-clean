<?php

namespace App\Http\Controllers;

use App\Models\Farm;
use App\Models\FixedAsset;
use App\Models\JournalEntry;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;

class FixedAssetController extends Controller
{
    public function index(Request $request)
    {
        $this->authorize('fixedAsset', JournalEntry::class);

        /** @var \App\Models\User $user */
        $user = Auth::user();
        if (!$user) {
            return redirect()->route('login');
        }

        $q = $request->input('q');

        $fixedAssets = FixedAsset::with(['farm'])
            ->when($user->hasRole('farm owner'), function ($query) use ($user) {
                $query->where('farm_id', $user->farm_id);
            })
            ->when($q, fn($qb) => $qb->where('name', 'like', "%$q%")
                ->orWhere('serial_number', 'like', "%$q%")
                ->orWhere('location', 'like', "%$q%"))
            ->orderBy('id', 'desc')
            ->paginate(15)
            ->withQueryString();

        return Inertia::render('FixedAssets/Index', [
            'fixedAssets' => $fixedAssets,
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

        $farms = Farm::when($user->hasRole('farm owner'), function ($query) use ($user) {
            $query->where('id', $user->farm_id);
        })->get();

        return Inertia::render('FixedAssets/Create', [
            'farms' => $farms,
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
            'name' => 'required|string|max:200',
            'asset_type' => 'required|in:machinery,shed,vehicle,equipment,land,building,other',
            'farm_id' => 'required|exists:farms,id',
            'purchase_value' => 'required|numeric|min:0|max:999999999.99',
            'purchase_date' => 'required|date|before_or_equal:today',
            'useful_life_years' => 'required|integer|min:1|max:100',
            'depreciation_method' => 'required|in:straight_line',
            'status' => 'required|in:active,disposed,under_maintenance,sold',
            'location' => 'nullable|string|max:200',
            'serial_number' => 'nullable|string|max:100',
            'notes' => 'nullable|string|max:2000',
        ]);

        if ($user->hasRole('farm owner')) {
            $validated['farm_id'] = $user->farm_id;
        }

        $validated['user_id'] = $user->id;

        FixedAsset::create($validated);

        return redirect()->route('fixed-assets.index')->with('success', 'Fixed asset registered successfully!');
    }

    public function show(FixedAsset $fixedAsset)
    {
        $fixedAsset->load(['farm']);
        return Inertia::render('FixedAssets/Show', [
            'fixedAsset' => $fixedAsset->append(['annual_depreciation', 'current_book_value']),
        ]);
    }

    public function edit(FixedAsset $fixedAsset)
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();
        if (!$user) {
            return redirect()->route('login');
        }

        $farms = Farm::when($user->hasRole('farm owner'), function ($query) use ($user) {
            $query->where('id', $user->farm_id);
        })->get();

        return Inertia::render('FixedAssets/Edit', [
            'fixedAsset' => $fixedAsset,
            'farms' => $farms,
        ]);
    }

    public function update(Request $request, FixedAsset $fixedAsset)
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();
        if (!$user) {
            return redirect()->route('login');
        }

        $validated = $request->validate([
            'name' => 'required|string|max:200',
            'asset_type' => 'required|in:machinery,shed,vehicle,equipment,land,building,other',
            'farm_id' => 'required|exists:farms,id',
            'purchase_value' => 'required|numeric|min:0|max:999999999.99',
            'purchase_date' => 'required|date|before_or_equal:today',
            'useful_life_years' => 'required|integer|min:1|max:100',
            'depreciation_method' => 'required|in:straight_line',
            'status' => 'required|in:active,disposed,under_maintenance,sold',
            'location' => 'nullable|string|max:200',
            'serial_number' => 'nullable|string|max:100',
            'notes' => 'nullable|string|max:2000',
        ]);

        if ($user->hasRole('farm owner')) {
            $validated['farm_id'] = $user->farm_id;
        }

        $fixedAsset->update($validated);

        return redirect()->route('fixed-assets.show', $fixedAsset)->with('success', 'Fixed asset updated successfully!');
    }

    public function destroy(FixedAsset $fixedAsset)
    {
        $fixedAsset->delete();

        return redirect()->route('fixed-assets.index')->with('success', 'Fixed asset removed successfully!');
    }
}
