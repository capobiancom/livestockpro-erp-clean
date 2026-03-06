<?php

namespace App\Http\Requests\Api\V1\Pregnancies;

use Illuminate\Foundation\Http\FormRequest;

class UpdatePregnancyRequest extends FormRequest
{
    public function authorize(): bool
    {
        $pregnancy = $this->route('pregnancy');

        if ($pregnancy instanceof \App\Models\Pregnancy) {
            return $this->user()?->can('update', $pregnancy) ?? false;
        }

        return false;
    }

    public function rules(): array
    {
        return [
            'animal_id' => ['sometimes', 'required', 'integer', 'exists:animals,id'],
            'reproduction_record_id' => ['sometimes', 'nullable', 'integer', 'exists:reproduction_records,id'],
            'pregnancy_confirmed_date' => ['sometimes', 'nullable', 'date'],
            'expected_gestation_days' => ['sometimes', 'nullable', 'integer', 'min:1', 'max:400'],
            'expected_calving_date' => ['sometimes', 'nullable', 'date'],
            'pregnancy_status' => ['sometimes', 'nullable', 'string', 'max:50'],
            'health_notes' => ['sometimes', 'nullable', 'string'],
        ];
    }
}
