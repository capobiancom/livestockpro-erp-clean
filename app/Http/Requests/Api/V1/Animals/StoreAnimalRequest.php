<?php

namespace App\Http\Requests\Api\V1\Animals;

use Illuminate\Foundation\Http\FormRequest;

class StoreAnimalRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return $this->user()?->can('create', \App\Models\Animal::class) ?? false;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'tag' => ['required', 'string', 'max:255'],
            'name' => ['nullable', 'string', 'max:255'],
            'animal_type' => ['required', 'string', 'max:50'],
            'sex' => ['required', 'string', 'max:20'],
            'dob' => ['nullable', 'date'],
            'breed_id' => ['nullable', 'integer', 'exists:breeds,id'],
            'herd_id' => ['nullable', 'integer', 'exists:herds,id'],
            'status' => ['nullable', 'string', 'max:50'],
            'current_weight_kg' => ['nullable', 'numeric'],
            'color' => ['nullable', 'string', 'max:100'],
            'acquired_at' => ['nullable', 'date'],
            'purchase_price' => ['nullable', 'numeric'],
            'supplier_id' => ['nullable', 'integer', 'exists:suppliers,id'],
            'attributes' => ['nullable', 'array'],
            'notes' => ['nullable', 'string'],
            'image' => ['nullable', 'string', 'max:2048'],
        ];
    }
}
