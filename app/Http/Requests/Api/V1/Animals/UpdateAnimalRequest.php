<?php

namespace App\Http\Requests\Api\V1\Animals;

use Illuminate\Foundation\Http\FormRequest;

class UpdateAnimalRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        $animal = $this->route('animal');

        if ($animal instanceof \App\Models\Animal) {
            return $this->user()?->can('update', $animal) ?? false;
        }

        return false;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'tag' => ['sometimes', 'required', 'string', 'max:255'],
            'name' => ['sometimes', 'nullable', 'string', 'max:255'],
            'animal_type' => ['sometimes', 'required', 'string', 'max:50'],
            'sex' => ['sometimes', 'required', 'string', 'max:20'],
            'dob' => ['sometimes', 'nullable', 'date'],
            'breed_id' => ['sometimes', 'nullable', 'integer', 'exists:breeds,id'],
            'herd_id' => ['sometimes', 'nullable', 'integer', 'exists:herds,id'],
            'status' => ['sometimes', 'nullable', 'string', 'max:50'],
            'current_weight_kg' => ['sometimes', 'nullable', 'numeric'],
            'color' => ['sometimes', 'nullable', 'string', 'max:100'],
            'acquired_at' => ['sometimes', 'nullable', 'date'],
            'purchase_price' => ['sometimes', 'nullable', 'numeric'],
            'supplier_id' => ['sometimes', 'nullable', 'integer', 'exists:suppliers,id'],
            'attributes' => ['sometimes', 'nullable', 'array'],
            'notes' => ['sometimes', 'nullable', 'string'],
            'image' => ['sometimes', 'nullable', 'string', 'max:2048'],
        ];
    }
}
