<?php

namespace App\Http\Resources\Api\V1;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class AnimalResource extends JsonResource
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
            'tag' => $this->tag,
            'name' => $this->name,
            'animal_type' => $this->animal_type,
            'sex' => $this->sex,
            'dob' => $this->dob?->toDateString(),
            'breed_id' => $this->breed_id,
            'farm_id' => $this->farm_id,
            'herd_id' => $this->herd_id,
            'status' => $this->status,
            'current_weight_kg' => $this->current_weight_kg,
            'color' => $this->color,
            'acquired_at' => $this->acquired_at?->toDateString(),
            'purchase_price' => $this->purchase_price,
            'supplier_id' => $this->supplier_id,
            'attributes' => $this->attributes,
            'notes' => $this->notes,
            'image' => $this->image,
            'user_id' => $this->user_id,
            'created_at' => $this->created_at?->toISOString(),
            'updated_at' => $this->updated_at?->toISOString(),
        ];
    }
}
