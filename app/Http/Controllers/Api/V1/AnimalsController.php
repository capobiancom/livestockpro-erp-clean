<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\V1\Animals\StoreAnimalRequest;
use App\Http\Requests\Api\V1\Animals\UpdateAnimalRequest;
use App\Http\Resources\Api\V1\AnimalResource;
use App\Models\Animal;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class AnimalsController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $this->authorize('viewAny', Animal::class);

        $perPage = (int) $request->query('per_page', 15);
        $perPage = max(1, min(100, $perPage));

        $query = Animal::query();

        // Farm scoping: farm owners are restricted to their farm.
        if ($request->user()?->hasRole('farm owner') && $request->user()?->farm_id) {
            $query->where('farm_id', $request->user()->farm_id);
        }

        // Basic search
        if ($search = $request->query('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('tag', 'like', "%{$search}%")
                    ->orWhere('name', 'like', "%{$search}%");
            });
        }

        $paginator = $query
            ->orderByDesc('id')
            ->paginate($perPage);

        return response()->json([
            'data' => AnimalResource::collection($paginator->items()),
            'meta' => [
                'current_page' => $paginator->currentPage(),
                'per_page' => $paginator->perPage(),
                'total' => $paginator->total(),
                'last_page' => $paginator->lastPage(),
            ],
        ]);
    }

    public function store(StoreAnimalRequest $request): JsonResponse
    {
        $data = $request->validated();

        // Enforce farm_id from authenticated user (do not accept from client)
        $data['farm_id'] = $request->user()->farm_id;
        $data['user_id'] = $request->user()->id;

        $animal = Animal::create($data);

        return response()->json([
            'data' => new AnimalResource($animal),
        ], 201);
    }

    public function show(Request $request, Animal $animal): JsonResponse
    {
        $this->authorize('view', $animal);

        return response()->json([
            'data' => new AnimalResource($animal),
        ]);
    }

    public function update(UpdateAnimalRequest $request, Animal $animal): JsonResponse
    {
        $data = $request->validated();

        // Prevent cross-farm updates
        unset($data['farm_id'], $data['user_id']);

        $animal->update($data);

        return response()->json([
            'data' => new AnimalResource($animal->refresh()),
        ]);
    }

    public function destroy(Request $request, Animal $animal): JsonResponse
    {
        $this->authorize('delete', $animal);

        $animal->delete();

        return response()->json([
            'message' => 'Deleted.',
        ]);
    }
}
