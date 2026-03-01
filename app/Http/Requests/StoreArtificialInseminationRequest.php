<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreArtificialInseminationRequest extends FormRequest
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
        return [
            'animal_id' => 'required|exists:animals,id',
            'event' => 'required|in:mating,insemination,pregnancy_check,calving,abortion,other',
            'semen_batch_no' => 'required|string|max:255',
            'breed_id' => 'required|exists:breeds,id',
            'semen_company' => 'nullable|string|max:255',
            'insemination_date' => 'required|date',
            'vet_id' => 'nullable|exists:users,id',
            'cost' => 'nullable|numeric|min:0',
            'remarks' => 'nullable|string',
        ];
    }
}
