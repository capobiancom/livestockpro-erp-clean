<?php

namespace App\Http\Requests\Api\V1\Breeds;

use Illuminate\Foundation\Http\FormRequest;

class UpdateBreedRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        $breed = $this->route('breed');

        if ($breed instanceof \App\Models\Breed) {
            return $this->user()?->can('update', $breed) ?? false;
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
            'name' => ['sometimes', 'required', 'string', 'max:255'],
            'code' => ['sometimes', 'nullable', 'string', 'max:50'],
            'description' => ['sometimes', 'nullable', 'string'],
            'characteristics' => ['sometimes', 'nullable', 'array'],
            'origin' => ['sometimes', 'nullable', 'string', 'max:255'],
            'animal_type' => ['sometimes', 'nullable', 'string', 'max:50'],
        ];
    }
}
