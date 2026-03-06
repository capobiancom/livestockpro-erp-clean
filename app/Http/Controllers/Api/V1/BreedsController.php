<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\V1\Breeds\StoreBreedRequest;
use App\Http\Requests\Api\V1\Breeds\UpdateBreedRequest;
use App\Http\Resources\Api\V1\BreedResource;
use App\Models\Breed;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class BreedsController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $this->authorize('viewAny', Breed::class);

        $perPage = (int) $request->query('per_page', 15);
        $perPage = max(1, min(100, $perPage));

        $query = Breed::query();

        // Farm scoping: farm owners are restricted to their farm.
        if ($request->user()?->hasRole('farm owner') && $request->user()?->farm_id) {
            $query->where('farm_id', $request->user()->farm_id);
        }

        if ($search = $request->query('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('code', 'like', "%{$search}%")
                    ->orWhere('origin', 'like', "%{$search}%");
            });
        }

        if ($animalType = $request->query('animal_type')) {
            $query->where('animal_type', $animalType);
        }

        $paginator = $query
            ->orderByDesc('id')
            ->paginate($perPage);

        return response()->json([
            'data' => BreedResource::collection($paginator->items()),
            'meta' => [
                'current_page' => $paginator->currentPage(),
                'per_page' => $paginator->perPage(),
                'total' => $paginator->total(),
                'last_page' => $paginator->lastPage(),
            ],
        ]);
    }

    public function store(StoreBreedRequest $request): JsonResponse
    {
        $data = $request->validated();

        // Enforce farm_id from authenticated user (do not accept from client)
        $data['farm_id'] = $request->user()->farm_id;
        $data['user_id'] = $request->user()->id;

        $breed = Breed::create($data);

        return response()->json([
            'data' => new BreedResource($breed),
        ], 201);
    }

    public function show(Request $request, Breed $breed): JsonResponse
    {
        $this->authorize('view', $breed);

        return response()->json([
            'data' => new BreedResource($breed),
        ]);
    }

    public function update(UpdateBreedRequest $request, Breed $breed): JsonResponse
    {
        $data = $request->validated();

        // Prevent cross-farm updates
        unset($data['farm_id'], $data['user_id']);

        $breed->update($data);

        return response()->json([
            'data' => new BreedResource($breed->refresh()),
        ]);
    }

    public function destroy(Request $request, Breed $breed): JsonResponse
    {
        $this->authorize('delete', $breed);

        $breed->delete();

        return response()->json([
            'message' => 'Deleted.',
        ]);
    }
}
