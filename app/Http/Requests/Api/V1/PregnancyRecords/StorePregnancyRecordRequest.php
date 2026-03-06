<?php

namespace App\Http\Requests\Api\V1\PregnancyRecords;

use Illuminate\Foundation\Http\FormRequest;

class StorePregnancyRecordRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return $this->user() !== null;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'pregnancy_id' => ['required', 'integer', 'exists:pregnancies,id'],
            'checkup_date' => ['required', 'date'],
            'checkup_result' => ['required', 'string'],
            'observations' => ['nullable', 'string'],
            'checked_by' => ['nullable', 'integer', 'exists:users,id'],
        ];
    }
}
