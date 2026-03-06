<?php

namespace App\Http\Requests\Api\V1\ReproductionRecords;

use Illuminate\Foundation\Http\FormRequest;

class UpdateReproductionRecordRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        $record = $this->route('reproduction_record');

        if ($record instanceof \App\Models\ReproductionRecord) {
            return $this->user()?->can('update', $record) ?? false;
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
            'animal_id' => ['sometimes', 'required', 'integer', 'exists:animals,id'],
            'event' => ['sometimes', 'required', 'string', 'max:100'],
            'partner_id' => ['sometimes', 'nullable', 'integer', 'exists:animals,id'],
            'event_date' => ['sometimes', 'required', 'date'],
            'outcome' => ['sometimes', 'nullable', 'string', 'max:100'],
            'notes' => ['sometimes', 'nullable', 'string'],
            'heat_stage' => ['sometimes', 'nullable', 'string', 'max:100'],
            'performed_by' => ['sometimes', 'nullable', 'integer', 'exists:users,id'],
            'artificial_insemination_id' => ['sometimes', 'nullable', 'integer', 'exists:artificial_inseminations,id'],
        ];
    }
}
