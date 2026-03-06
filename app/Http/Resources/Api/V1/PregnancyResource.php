<?php

namespace App\Http\Resources\Api\V1;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PregnancyResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'farm_id' => $this->farm_id,
            'animal_id' => $this->animal_id,
            'reproduction_record_id' => $this->reproduction_record_id,
            'pregnancy_confirmed_date' => $this->pregnancy_confirmed_date?->toDateString(),
            'expected_gestation_days' => $this->expected_gestation_days,
            'expected_calving_date' => $this->expected_calving_date?->toDateString(),
            'pregnancy_status' => $this->pregnancy_status?->value ?? $this->pregnancy_status,
            'health_notes' => $this->health_notes,
            'user_id' => $this->user_id,
            'created_at' => $this->created_at?->toISOString(),
            'updated_at' => $this->updated_at?->toISOString(),
            'deleted_at' => $this->deleted_at?->toISOString(),
        ];
    }
}
