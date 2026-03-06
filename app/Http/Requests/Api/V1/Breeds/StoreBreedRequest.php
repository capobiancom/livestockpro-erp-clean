<?php

namespace App\Http\Requests\Api\V1\Breeds;

use Illuminate\Foundation\Http\FormRequest;

class StoreBreedRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return $this->user()?->can('create', \App\Models\Breed::class) ?? false;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'code' => ['nullable', 'string', 'max:50'],
            'description' => ['nullable', 'string'],
            'characteristics' => ['nullable', 'array'],
            'origin' => ['nullable', 'string', 'max:255'],
            'animal_type' => ['nullable', 'string', 'max:50'],
        ];
    }
}
