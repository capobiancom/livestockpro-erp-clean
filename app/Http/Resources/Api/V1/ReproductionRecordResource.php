<?php

namespace App\Http\Resources\Api\V1;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ReproductionRecordResource extends JsonResource
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
            'event' => $this->event,
            'partner_id' => $this->partner_id,
            'event_date' => $this->event_date?->toDateString(),
            'outcome' => $this->outcome,
            'notes' => $this->notes,
            'heat_stage' => $this->heat_stage,
            'performed_by' => $this->performed_by,
            'artificial_insemination_id' => $this->artificial_insemination_id,
            'user_id' => $this->user_id,
            'created_at' => $this->created_at?->toISOString(),
            'updated_at' => $this->updated_at?->toISOString(),
        ];
    }
}
