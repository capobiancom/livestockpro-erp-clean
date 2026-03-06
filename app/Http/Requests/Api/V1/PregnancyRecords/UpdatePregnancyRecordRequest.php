<?php

namespace App\Http\Requests\Api\V1\PregnancyRecords;

use Illuminate\Foundation\Http\FormRequest;

class UpdatePregnancyRecordRequest extends FormRequest
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
            // pregnancy_id is immutable via API update (prevents moving record across pregnancies)
            'pregnancy_id' => ['prohibited'],

            'checkup_date' => ['sometimes', 'date'],
            'checkup_result' => ['sometimes', 'string'],
            'observations' => ['sometimes', 'nullable', 'string'],
            'checked_by' => ['sometimes', 'nullable', 'integer', 'exists:users,id'],
            'farm_id' => ['prohibited'],
            'user_id' => ['prohibited'],
        ];
    }
}
