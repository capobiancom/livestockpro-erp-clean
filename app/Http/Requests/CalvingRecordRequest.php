<?php

namespace App\Http\Requests;

use App\Enums\CalfGender;
use App\Enums\CalvingOutcome;
use App\Enums\CalvingType;
use Illuminate\Foundation\Http\FormRequest;

class CalvingRecordRequest extends FormRequest
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
            'pregnancy_id' => ['required', 'exists:pregnancies,id'],
            'calving_date' => ['required', 'date'],
            'calving_type' => ['required', 'in:' . implode(',', array_column(CalvingType::cases(), 'value'))],
            'calves_count' => ['required', 'integer', 'min:0'],
            'calf_gender' => ['nullable', 'in:' . implode(',', array_column(CalfGender::cases(), 'value'))],
            'calving_outcome' => ['required', 'in:' . implode(',', array_column(CalvingOutcome::cases(), 'value'))],
            'notes' => ['nullable', 'string'],
        ];
    }
}
