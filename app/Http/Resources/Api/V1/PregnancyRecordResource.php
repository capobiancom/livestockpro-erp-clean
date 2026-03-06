<?php

namespace App\Http\Resources\Api\V1;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PregnancyRecordResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        /** @var \App\Models\PregnancyCheckup $record */
        $record = $this->resource;

        return [
            'id' => $record->id,
            'farm_id' => $record->farm_id,
            'pregnancy_id' => $record->pregnancy_id,
            'checkup_date' => optional($record->checkup_date)->toISOString(),
            'checkup_result' => $record->checkup_result?->value ?? $record->checkup_result,
            'observations' => $record->observations,
            'checked_by' => $record->checked_by,
            'user_id' => $record->user_id,
            'created_at' => optional($record->created_at)->toISOString(),
            'updated_at' => optional($record->updated_at)->toISOString(),
        ];
    }
}
