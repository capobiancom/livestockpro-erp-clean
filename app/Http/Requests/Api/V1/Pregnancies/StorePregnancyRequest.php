<?php

namespace App\Http\Requests\Api\V1\Pregnancies;

use App\Models\Pregnancy;
use Illuminate\Foundation\Http\FormRequest;

class StorePregnancyRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->can('create', Pregnancy::class) ?? false;
    }

    public function rules(): array
    {
        return [
            'animal_id' => ['required', 'integer', 'exists:animals,id'],
            'reproduction_record_id' => ['nullable', 'integer', 'exists:reproduction_records,id'],
            'pregnancy_confirmed_date' => ['nullable', 'date'],
            'expected_gestation_days' => ['nullable', 'integer', 'min:1', 'max:400'],
            'expected_calving_date' => ['nullable', 'date'],
            'pregnancy_status' => ['nullable', 'string', 'max:50'],
            'health_notes' => ['nullable', 'string'],
        ];
    }
}
