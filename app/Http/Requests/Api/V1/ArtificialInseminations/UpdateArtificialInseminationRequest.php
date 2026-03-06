<?php

namespace App\Http\Requests\Api\V1\ArtificialInseminations;

use Illuminate\Foundation\Http\FormRequest;

class UpdateArtificialInseminationRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        $ai = $this->route('artificial_insemination');

        if ($ai instanceof \App\Models\ArtificialInsemination) {
            return $this->user()?->can('update', $ai) ?? false;
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
            'semen_batch_no' => ['sometimes', 'required', 'string', 'max:255'],
            'breed_id' => ['sometimes', 'nullable', 'integer', 'exists:breeds,id'],
            'semen_company' => ['sometimes', 'nullable', 'string', 'max:255'],
            'insemination_date' => ['sometimes', 'required', 'date'],
            'reproduction_record_id' => ['sometimes', 'nullable', 'integer', 'exists:reproduction_records,id'],
            'vet_id' => ['sometimes', 'nullable', 'integer', 'exists:users,id'],
            'cost' => ['sometimes', 'nullable', 'numeric'],
            'remarks' => ['sometimes', 'nullable', 'string'],
        ];
    }
}
