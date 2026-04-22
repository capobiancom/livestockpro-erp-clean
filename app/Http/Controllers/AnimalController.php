<?php

namespace App\Http\Controllers;

use App\Models\Animal;
use App\Models\Breed;
use App\Models\Farm;
use App\Models\Herd;
use App\Models\Supplier; // Import Supplier model
use App\Models\StockMovement;
use App\Data\AnimalData;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth; // Import Auth facade
use Illuminate\Support\Facades\Storage; // Import Storage facade
use Illuminate\Validation\Rule;
use Inertia\Inertia;

class AnimalController extends Controller
{
    public function __construct()
    {
        $this->authorizeResource(Animal::class, 'animal');
    }

    public function index(Request $request)
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();
        if (!$user) {
            return redirect()->route('login');
        }
        $q = $request->input('q');

        $animals = Animal::with(['breed', 'farm', 'herd', 'supplier']) // Load supplier relationship
            ->when($user->hasRole('farm owner'), function ($query) use ($user) {
                $query->where('farm_id', $user->farm_id);
            })
            ->when($q, fn($qb) => $qb->where('tag', 'like', "%$q%")
                ->orWhere('name', 'like', "%$q%"))
            ->orderBy('id', 'desc')
            ->paginate(15)
            ->withQueryString();

        return Inertia::render('Animals/Index', [
            'animals' => $animals,
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
        $breeds = Breed::when($user->hasRole('farm owner'), function ($query) use ($user) {
            $query->where('farm_id', $user->farm_id);
        })->get();
        $farms = Farm::when($user->hasRole('farm owner'), function ($query) use ($user) {
            $query->where('id', $user->farm_id);
        })->get();
        $herds = Herd::when($user->hasRole('farm owner'), function ($query) use ($user) {
            $query->where('farm_id', $user->farm_id);
        })->get();
        $suppliers = Supplier::when($user->hasRole('farm owner'), function ($query) use ($user) {
            $query->where('farm_id', $user->farm_id);
        })->get();

        return Inertia::render('Animals/Create', [
            'breeds' => $breeds,
            'farms' => $farms,
            'herds' => $herds,
            'suppliers' => $suppliers,
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
            'tag' => [
                'nullable',
                'string',
                'max:50',
                Rule::unique('animals', 'tag')->where(fn($q) => $q->where('farm_id', $request->farm_id))
            ],
            'name' => 'nullable|string|max:100',
            'animal_type' => 'required|in:cow,ox,bull,calf,heifer,buffalo,other',
            'sex' => 'required|in:male,female,unknown',
            'dob' => 'nullable|date|before:today',
            'breed_id' => 'required|exists:breeds,id',
            'farm_id' => 'required|exists:farms,id',
            'herd_id' => 'nullable|exists:herds,id',
            'status' => 'required|in:active,sold,deceased,transferred',
            'current_weight_kg' => 'nullable|numeric|min:0|max:5000',
            'color' => 'nullable|string|max:50',
            'acquired_at' => 'nullable|date|before_or_equal:today',
            'purchase_price' => 'nullable|numeric|min:0|max:9999999.99',
            'supplier_id' => 'nullable|exists:suppliers,id',
            'attributes' => 'nullable|array',
            'notes' => 'nullable|string|max:1000',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        // Set user_id
        $validated['user_id'] = $user->id;

        // Enforce farm_id for farm owners
        if ($user->hasRole('farm owner')) {
            $validated['farm_id'] = $user->farm_id;
        }

        // Generate tag if not provided
        if (empty($validated['tag'])) {
            $validated['tag'] = $this->generateUniqueTagNumber();
        }

        // Handle image upload
        if ($request->hasFile('image')) {
            $imagePath = $request->file('image')->store('animals', 'public');
            $validated['image'] = $imagePath;
        }

        // Business Logic: Set acquired_at to today if not provided
        if (empty($validated['acquired_at'])) {
            $validated['acquired_at'] = now()->toDateString();
        }

        // Business Logic: Default status to 'active' if not provided
        if (empty($validated['status'])) {
            $validated['status'] = 'active';
        }

        // Business Logic: Validate herd belongs to the selected farm
        if (!empty($validated['herd_id']) && !empty($validated['farm_id'])) {
            $herd = Herd::find($validated['herd_id']);
            if ($herd && $herd->farm_id != $validated['farm_id']) {
                return back()->withErrors(['herd_id' => 'The selected herd does not belong to the selected farm.'])->withInput();
            }
        }

        $data = AnimalData::from($validated);

        $animal = Animal::create($data->toArray());

        // Create initial stock movement for this animal (inventory item)
        // NOTE: source_event_type is constrained by DB enum.
        StockMovement::create([
            'farm_id' => $animal->farm_id,
            'item_type' => Animal::class,
            'item_id' => $animal->id,
            'movement_type' => 'in',
            'source_event_type' => 'adjustment',
            'source_id' => $animal->id,
            'source_type' => Animal::class,
            'quantity' => 1,
            'unit_cost' => $animal->purchase_price,
            'movement_date' => $animal->acquired_at ?? now()->toDateString(),
            'user_id' => $user->id,
        ]);

        return redirect()->route('animals.index')->with('success', 'Animal created successfully!');
    }

    public function show(Animal $animal)
    {
        $animal->load(['breed', 'farm', 'herd', 'supplier']); // Load supplier relationship
        return Inertia::render('Animals/Show', ['animal' => $animal]);
    }

    public function edit(Animal $animal)
    {
        $user = Auth::user();
        if (!$user) {
            return redirect()->route('login');
        }
        $breeds = Breed::when($user->hasRole('farm owner'), function ($query) use ($user) {
            $query->where('farm_id', $user->farm_id);
        })->get();
        $farms = Farm::when($user->hasRole('farm owner'), function ($query) use ($user) {
            $query->where('id', $user->farm_id);
        })->get();
        $herds = Herd::when($user->hasRole('farm owner'), function ($query) use ($user) {
            $query->where('farm_id', $user->farm_id);
        })->get();
        $suppliers = Supplier::when($user->hasRole('farm owner'), function ($query) use ($user) {
            $query->where('farm_id', $user->farm_id);
        })->get();

        return Inertia::render('Animals/Edit', [
            'animal' => $animal,
            'breeds' => $breeds,
            'farms' => $farms,
            'herds' => $herds,
            'suppliers' => $suppliers,
        ]);
    }

    public function update(Request $request, Animal $animal)
    {
        $user = Auth::user();
        if (!$user) {
            return redirect()->route('login');
        }
        $validated = $request->validate([
            'tag' => [
                'required',
                'string',
                'max:50',
                Rule::unique('animals', 'tag')
                    ->ignore($animal->id)
                    ->where(fn($q) => $q->where('farm_id', $user->farm_id)),
            ],
            'name' => 'nullable|string|max:100',
            'animal_type' => 'required|in:cow,ox,bull,calf,heifer,buffalo,other',
            'sex' => 'required|in:male,female,unknown',
            'dob' => 'nullable|date|before:today',
            'breed_id' => 'required|exists:breeds,id',
            'farm_id' => 'required|exists:farms,id',
            'herd_id' => 'nullable|exists:herds,id',
            'status' => 'required|in:active,sold,deceased,transferred',
            'current_weight_kg' => 'nullable|numeric|min:0|max:5000',
            'color' => 'nullable|string|max:50',
            'acquired_at' => 'nullable|date|before_or_equal:today',
            'purchase_price' => 'nullable|numeric|min:0|max:9999999.99',
            'supplier_id' => 'nullable|exists:suppliers,id', // Changed 'source' to 'supplier_id'
            'attributes' => 'nullable|array',
            'notes' => 'nullable|string|max:1000',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        // Enforce farm_id for farm owners
        if ($user->hasRole('farm owner')) {
            $validated['farm_id'] = $user->farm_id;
        }

        // Handle image upload
        if ($request->hasFile('image')) {
            // Delete old image if exists
            if ($animal->image && Storage::disk('public')->exists($animal->image)) {
                Storage::disk('public')->delete($animal->image);
            }
            $imagePath = $request->file('image')->store('animals', 'public');
            $validated['image'] = $imagePath;
        }

        // Business Logic: Prevent changing farm if animal has existing records
        if ($animal->farm_id != $validated['farm_id']) {
            // Check if animal has feeding records, vaccinations, etc.
            // For now, we'll allow it but you can add checks here
        }

        // Business Logic: Validate herd belongs to the selected farm
        if (!empty($validated['herd_id']) && !empty($validated['farm_id'])) {
            $herd = Herd::find($validated['herd_id']);
            if ($herd && $herd->farm_id != $validated['farm_id']) {
                return back()->withErrors(['herd_id' => 'The selected herd does not belong to the selected farm.'])->withInput();
            }
        }

        // Business Logic: Track status changes
        if ($animal->status != $validated['status']) {
            // You could log status changes here
            // StatusChangeLog::create(['animal_id' => $animal->id, 'old_status' => $animal->status, 'new_status' => $validated['status']]);
        }

        $data = AnimalData::from($validated);
        $animal->update($data->toArray());

        // Ensure there is a stock movement record for this animal.
        // If missing, insert one. If present, update movement_type based on current status.
        $movementType = match ($animal->status) {
            'active' => 'in',
            'sold', 'transferred', 'deceased' => 'out',
            default => 'in',
        };

        $existing = StockMovement::query()
            ->where('item_type', Animal::class)
            ->where('item_id', $animal->id)
            ->orderByDesc('id')
            ->first();

        if (!$existing) {
            StockMovement::create([
                'farm_id' => $animal->farm_id,
                'item_type' => Animal::class,
                'item_id' => $animal->id,
                'movement_type' => $movementType,
                'source_event_type' => 'adjustment',
                'source_id' => $animal->id,
                'source_type' => Animal::class,
                'quantity' => 1,
                'unit_cost' => $animal->purchase_price,
                'movement_date' => now()->toDateString(),
                'user_id' => $user->id,
            ]);
        } else {
            $existing->update([
                'movement_type' => $movementType,
                'source_event_type' => $animal->status === 'deceased' ? 'loss' : 'adjustment'
            ]);
        }

        return redirect()->route('animals.show', $animal)->with('success', 'Animal updated successfully!');
    }

    /**
     * Generate a unique tag number for a new calf.
     */
    private function generateUniqueTagNumber(): string
    {
        return strtoupper(fake()->bothify('TAG-####') . '-' . uniqid());
    }

    public function destroy(Animal $animal)
    {
        // Business Logic: Soft delete to maintain historical records
        // The Animal model uses SoftDeletes trait

        // Check if animal can be deleted (you can add more business rules here)
        if ($animal->status === 'active') {
            // Optionally warn or prevent deletion of active animals
            // return back()->withErrors(['error' => 'Cannot delete an active animal. Please change status first.']);
        }

        $animal->delete();

        return redirect()->route('animals.index')->with('success', 'Animal removed successfully!');
    }
}
