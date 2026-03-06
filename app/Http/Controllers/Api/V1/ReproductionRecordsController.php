<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\V1\ReproductionRecords\StoreReproductionRecordRequest;
use App\Http\Requests\Api\V1\ReproductionRecords\UpdateReproductionRecordRequest;
use App\Http\Resources\Api\V1\ReproductionRecordResource;
use App\Models\ReproductionRecord;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ReproductionRecordsController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $this->authorize('viewAny', ReproductionRecord::class);

        $perPage = (int) $request->query('per_page', 15);
        $perPage = max(1, min(100, $perPage));

        $query = ReproductionRecord::query();

        if ($request->user()?->hasRole('farm owner') && $request->user()?->farm_id) {
            $query->where('farm_id', $request->user()->farm_id);
        }

        if ($animalId = $request->query('animal_id')) {
            $query->where('animal_id', (int) $animalId);
        }

        $paginator = $query
            ->orderByDesc('id')
            ->paginate($perPage);

        return response()->json([
            'data' => ReproductionRecordResource::collection($paginator->items()),
            'meta' => [
                'current_page' => $paginator->currentPage(),
                'per_page' => $paginator->perPage(),
                'total' => $paginator->total(),
                'last_page' => $paginator->lastPage(),
            ],
        ]);
    }

    public function store(StoreReproductionRecordRequest $request): JsonResponse
    {
        $data = $request->validated();

        $data['farm_id'] = $request->user()->farm_id;
        $data['user_id'] = $request->user()->id;

        $record = ReproductionRecord::create($data);

        return response()->json([
            'data' => new ReproductionRecordResource($record),
        ], 201);
    }

    public function show(Request $request, ReproductionRecord $reproduction_record): JsonResponse
    {
        $this->authorize('view', $reproduction_record);

        return response()->json([
            'data' => new ReproductionRecordResource($reproduction_record),
        ]);
    }

    public function update(UpdateReproductionRecordRequest $request, ReproductionRecord $reproduction_record): JsonResponse
    {
        $data = $request->validated();

        unset($data['farm_id'], $data['user_id']);

        $reproduction_record->update($data);

        return response()->json([
            'data' => new ReproductionRecordResource($reproduction_record->refresh()),
        ]);
    }

    public function destroy(Request $request, ReproductionRecord $reproduction_record): JsonResponse
    {
        $this->authorize('delete', $reproduction_record);

        $reproduction_record->delete();

        return response()->json([
            'message' => 'Deleted.',
        ]);
    }
}
