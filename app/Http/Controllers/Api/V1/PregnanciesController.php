<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\V1\Pregnancies\StorePregnancyRequest;
use App\Http\Requests\Api\V1\Pregnancies\UpdatePregnancyRequest;
use App\Http\Resources\Api\V1\PregnancyResource;
use App\Models\Pregnancy;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class PregnanciesController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $this->authorize('viewAny', Pregnancy::class);

        $perPage = (int) $request->query('per_page', 15);
        $perPage = max(1, min(100, $perPage));

        $query = Pregnancy::query();

        // Farm scoping: farm owners are restricted to their farm.
        if ($request->user()?->hasRole('farm owner') && $request->user()?->farm_id) {
            $query->where('farm_id', $request->user()->farm_id);
        }

        if ($animalId = $request->query('animal_id')) {
            $query->where('animal_id', $animalId);
        }

        if ($status = $request->query('pregnancy_status')) {
            $query->where('pregnancy_status', $status);
        }

        $paginator = $query
            ->orderByDesc('id')
            ->paginate($perPage);

        return response()->json([
            'data' => PregnancyResource::collection($paginator->items()),
            'meta' => [
                'current_page' => $paginator->currentPage(),
                'per_page' => $paginator->perPage(),
                'total' => $paginator->total(),
                'last_page' => $paginator->lastPage(),
            ],
        ]);
    }

    public function store(StorePregnancyRequest $request): JsonResponse
    {
        $data = $request->validated();

        // Enforce farm_id/user_id from authenticated user (do not accept from client)
        $data['farm_id'] = $request->user()->farm_id;
        $data['user_id'] = $request->user()->id;

        $pregnancy = Pregnancy::create($data);

        return response()->json([
            'data' => new PregnancyResource($pregnancy),
        ], 201);
    }

    public function show(Request $request, Pregnancy $pregnancy): JsonResponse
    {
        $this->authorize('view', $pregnancy);

        return response()->json([
            'data' => new PregnancyResource($pregnancy),
        ]);
    }

    public function update(UpdatePregnancyRequest $request, Pregnancy $pregnancy): JsonResponse
    {
        $data = $request->validated();

        // Prevent cross-farm updates
        unset($data['farm_id'], $data['user_id']);

        $pregnancy->update($data);

        return response()->json([
            'data' => new PregnancyResource($pregnancy->refresh()),
        ]);
    }

    public function destroy(Request $request, Pregnancy $pregnancy): JsonResponse
    {
        $this->authorize('delete', $pregnancy);

        $pregnancy->delete();

        return response()->json([
            'message' => 'Deleted.',
        ]);
    }
}
