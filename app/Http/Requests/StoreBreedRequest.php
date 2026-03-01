<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreBreedRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $user = $this->user();
        return [
            'name' => 'required|string|unique:breeds,name,NULL,id,farm_id,' . $user->farm_id,
            'code' => 'nullable|string',
            'description' => 'nullable|string',
            'characteristics' => 'nullable|array',
            'origin' => 'required|in:local,exotic,cross',
            'animal_type' => 'required|in:cow,bull',
        ];
    }
}
