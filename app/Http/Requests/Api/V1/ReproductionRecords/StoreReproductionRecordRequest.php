<?php

namespace App\Http\Requests\Api\V1\ReproductionRecords;

use Illuminate\Foundation\Http\FormRequest;

class StoreReproductionRecordRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return $this->user()?->can('create', \App\Models\ReproductionRecord::class) ?? false;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'animal_id' => ['required', 'integer', 'exists:animals,id'],
            'event' => ['required', 'string', 'max:100'],
            'partner_id' => ['nullable', 'integer', 'exists:animals,id'],
            'event_date' => ['required', 'date'],
            'outcome' => ['nullable', 'string', 'max:100'],
            'notes' => ['nullable', 'string'],
            'heat_stage' => ['nullable', 'string', 'max:100'],
            'performed_by' => ['nullable', 'integer', 'exists:users,id'],
            'artificial_insemination_id' => ['nullable', 'integer', 'exists:artificial_inseminations,id'],
        ];
    }
}
