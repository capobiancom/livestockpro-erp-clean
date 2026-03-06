<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\V1\ArtificialInseminations\StoreArtificialInseminationRequest;
use App\Http\Requests\Api\V1\ArtificialInseminations\UpdateArtificialInseminationRequest;
use App\Http\Resources\Api\V1\ArtificialInseminationResource;
use App\Models\ArtificialInsemination;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ArtificialInseminationsController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $this->authorize('viewAny', ArtificialInsemination::class);

        $perPage = (int) $request->query('per_page', 15);
        $perPage = max(1, min(100, $perPage));

        $query = ArtificialInsemination::query();

        if ($request->user()?->hasRole('farm owner') && $request->user()?->farm_id) {
            $query->where('farm_id', $request->user()->farm_id);
        }

        if ($breedId = $request->query('breed_id')) {
            $query->where('breed_id', (int) $breedId);
        }

        $paginator = $query
            ->orderByDesc('id')
            ->paginate($perPage);

        return response()->json([
            'data' => ArtificialInseminationResource::collection($paginator->items()),
            'meta' => [
                'current_page' => $paginator->currentPage(),
                'per_page' => $paginator->perPage(),
                'total' => $paginator->total(),
                'last_page' => $paginator->lastPage(),
            ],
        ]);
    }

    public function store(StoreArtificialInseminationRequest $request): JsonResponse
    {
        $data = $request->validated();

        $data['farm_id'] = $request->user()->farm_id;
        $data['user_id'] = $request->user()->id;

        $ai = ArtificialInsemination::create($data);

        return response()->json([
            'data' => new ArtificialInseminationResource($ai),
        ], 201);
    }

    public function show(Request $request, ArtificialInsemination $artificial_insemination): JsonResponse
    {
        $this->authorize('view', $artificial_insemination);

        return response()->json([
            'data' => new ArtificialInseminationResource($artificial_insemination),
        ]);
    }

    public function update(UpdateArtificialInseminationRequest $request, ArtificialInsemination $artificial_insemination): JsonResponse
    {
        $data = $request->validated();

        unset($data['farm_id'], $data['user_id']);

        $artificial_insemination->update($data);

        return response()->json([
            'data' => new ArtificialInseminationResource($artificial_insemination->refresh()),
        ]);
    }

    public function destroy(Request $request, ArtificialInsemination $artificial_insemination): JsonResponse
    {
        $this->authorize('delete', $artificial_insemination);

        $artificial_insemination->delete();

        return response()->json([
            'message' => 'Deleted.',
        ]);
    }
}
