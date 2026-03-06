<?php

namespace App\Http\Resources\Api\V1;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ArtificialInseminationResource extends JsonResource
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
            'semen_batch_no' => $this->semen_batch_no,
            'breed_id' => $this->breed_id,
            'semen_company' => $this->semen_company,
            'insemination_date' => $this->insemination_date?->toDateString(),
            'reproduction_record_id' => $this->reproduction_record_id,
            'vet_id' => $this->vet_id,
            'cost' => $this->cost,
            'remarks' => $this->remarks,
            'user_id' => $this->user_id,
            'created_at' => $this->created_at?->toISOString(),
            'updated_at' => $this->updated_at?->toISOString(),
        ];
    }
}
