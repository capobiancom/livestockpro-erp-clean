<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\V1\PregnancyRecords\StorePregnancyRecordRequest;
use App\Http\Requests\Api\V1\PregnancyRecords\UpdatePregnancyRecordRequest;
use App\Http\Resources\Api\V1\PregnancyRecordResource;
use App\Models\Pregnancy;
use App\Models\PregnancyCheckup;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class PregnancyRecordsController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $this->authorize('viewAny', PregnancyCheckup::class);

        $perPage = (int) $request->query('per_page', 15);
        $perPage = max(1, min(100, $perPage));

        $query = PregnancyCheckup::query()->with('pregnancy:id,farm_id');

        // Farm scoping: farm owners are restricted to their farm.
        if ($request->user()?->hasRole('farm owner') && $request->user()?->farm_id) {
            $query->where('farm_id', $request->user()->farm_id);
        }

        if ($pregnancyId = $request->query('pregnancy_id')) {
            $query->where('pregnancy_id', $pregnancyId);
        }

        if ($checkupResult = $request->query('checkup_result')) {
            $query->where('checkup_result', $checkupResult);
        }

        $paginator = $query
            ->orderByDesc('id')
            ->paginate($perPage);

        return response()->json([
            'data' => PregnancyRecordResource::collection($paginator->items()),
            'meta' => [
                'current_page' => $paginator->currentPage(),
                'per_page' => $paginator->perPage(),
                'total' => $paginator->total(),
                'last_page' => $paginator->lastPage(),
            ],
        ]);
    }

    public function store(StorePregnancyRecordRequest $request): JsonResponse
    {
        $this->authorize('create', PregnancyCheckup::class);

        $data = $request->validated();

        $pregnancy = Pregnancy::query()
            ->when(
                $request->user()?->hasRole('farm owner') && $request->user()?->farm_id,
                fn($q) => $q->where('farm_id', $request->user()->farm_id),
            )
            ->findOrFail($data['pregnancy_id']);

        // Enforce farm_id/user_id from authenticated user (do not accept from client)
        $data['farm_id'] = $pregnancy->farm_id;
        $data['user_id'] = $request->user()->id;

        $record = PregnancyCheckup::create($data);

        return response()->json([
            'data' => new PregnancyRecordResource($record),
        ], 201);
    }

    public function show(Request $request, PregnancyCheckup $pregnancy_record): JsonResponse
    {
        $this->authorize('view', $pregnancy_record);

        return response()->json([
            'data' => new PregnancyRecordResource($pregnancy_record),
        ]);
    }

    public function update(UpdatePregnancyRecordRequest $request, PregnancyCheckup $pregnancy_record): JsonResponse
    {
        $this->authorize('update', $pregnancy_record);

        $data = $request->validated();

        // Prevent cross-farm / cross-pregnancy updates
        unset($data['farm_id'], $data['user_id'], $data['pregnancy_id']);

        $pregnancy_record->update($data);

        return response()->json([
            'data' => new PregnancyRecordResource($pregnancy_record->refresh()),
        ]);
    }

    public function destroy(Request $request, PregnancyCheckup $pregnancy_record): JsonResponse
    {
        $this->authorize('delete', $pregnancy_record);

        $pregnancy_record->delete();

        return response()->json([
            'message' => 'Deleted.',
        ]);
    }
}
