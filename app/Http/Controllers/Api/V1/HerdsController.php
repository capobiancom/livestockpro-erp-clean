<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\V1\Herds\StoreHerdRequest;
use App\Http\Requests\Api\V1\Herds\UpdateHerdRequest;
use App\Http\Resources\Api\V1\HerdResource;
use App\Models\Herd;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class HerdsController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $this->authorize('viewAny', Herd::class);

        $perPage = (int) $request->query('per_page', 15);
        $perPage = max(1, min(100, $perPage));

        $query = Herd::query();

        if ($request->user()?->hasRole('farm owner') && $request->user()?->farm_id) {
            $query->where('farm_id', $request->user()->farm_id);
        }

        if ($search = $request->query('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('code', 'like', "%{$search}%");
            });
        }

        $paginator = $query
            ->orderByDesc('id')
            ->paginate($perPage);

        return response()->json([
            'data' => HerdResource::collection($paginator->items()),
            'meta' => [
                'current_page' => $paginator->currentPage(),
                'per_page' => $paginator->perPage(),
                'total' => $paginator->total(),
                'last_page' => $paginator->lastPage(),
            ],
        ]);
    }

    public function store(StoreHerdRequest $request): JsonResponse
    {
        $data = $request->validated();

        $data['farm_id'] = $request->user()->farm_id;
        $data['user_id'] = $request->user()->id;

        $herd = Herd::create($data);

        return response()->json([
            'data' => new HerdResource($herd),
        ], 201);
    }

    public function show(Request $request, Herd $herd): JsonResponse
    {
        $this->authorize('view', $herd);

        return response()->json([
            'data' => new HerdResource($herd),
        ]);
    }

    public function update(UpdateHerdRequest $request, Herd $herd): JsonResponse
    {
        $data = $request->validated();

        unset($data['farm_id'], $data['user_id']);

        $herd->update($data);

        return response()->json([
            'data' => new HerdResource($herd->refresh()),
        ]);
    }

    public function destroy(Request $request, Herd $herd): JsonResponse
    {
        $this->authorize('delete', $herd);

        $herd->delete();

        return response()->json([
            'message' => 'Deleted.',
        ]);
    }
}
