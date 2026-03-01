<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreDiseaseRequest;
use App\Http\Requests\UpdateDiseaseRequest;
use Illuminate\Support\Facades\Auth;
use App\Models\Disease;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class DiseaseController extends Controller
{
    public function __construct()
    {
        $this->authorizeResource(Disease::class, 'disease');
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

        $farmId = $request->user()->farm_id ?? session('farm_id');
        $diseases = Disease::where('farm_id', $farmId)
            ->when($request->search, function ($query, $search) {
                $query->where('name', 'like', '%' . $search . '%');
            })
            ->latest()
            ->paginate(10)
            ->withQueryString();

        return Inertia::render('Diseases/Index', [
            'diseases' => $diseases,
            'filters' => $request->only(['search']),
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $user = Auth::user();
        if (!$user) {
            return redirect()->route('login');
        }
        return Inertia::render('Diseases/Create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreDiseaseRequest $request): \Illuminate\Http\RedirectResponse
    {
        $user = $request->user();
        if (!$user) {
            return redirect()->route('login');
        }

        $data = $request->validated();
        $data = $request->validated();

        Disease::create([
            'user_id' => $user->id,
            'farm_id' => $data['farm_id'],
            'name' => $data['name'],
            'description' => $data['description'] ?? null,
        ]);

        return redirect()->route('diseases.index')->with('success', 'Disease created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Disease $disease)
    {
        $user = Auth::user();
        if (!$user) {
            return redirect()->route('login');
        }

        return Inertia::render('Diseases/Show', [
            'disease' => $disease,
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Disease $disease)
    {
        $user = Auth::user();
        if (!$user) {
            return redirect()->route('login');
        }

        return Inertia::render('Diseases/Edit', [
            'disease' => $disease,
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateDiseaseRequest $request, Disease $disease): \Illuminate\Http\RedirectResponse
    {
        $user = $request->user();
        if (!$user) {
            return redirect()->route('login');
        }

        $data = $request->validated();

        $disease->update([
            'user_id' => $user->id,
            'farm_id' => $data['farm_id'],
            'name' => $data['name'],
            'description' => $data['description'] ?? null,
        ]);

        return redirect()->route('diseases.index')->with('success', 'Disease updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Disease $disease): \Illuminate\Http\RedirectResponse
    {
        $user = Auth::user();
        if (!$user) {
            return redirect()->route('login');
        }

        $disease->delete();

        return redirect()->route('diseases.index')->with('success', 'Disease deleted successfully.');
    }
}
