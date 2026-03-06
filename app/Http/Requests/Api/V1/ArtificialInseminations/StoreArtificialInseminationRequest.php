<?php

namespace App\Http\Requests\Api\V1\ArtificialInseminations;

use Illuminate\Foundation\Http\FormRequest;

class StoreArtificialInseminationRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return $this->user()?->can('create', \App\Models\ArtificialInsemination::class) ?? false;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'semen_batch_no' => ['required', 'string', 'max:255'],
            'breed_id' => ['nullable', 'integer', 'exists:breeds,id'],
            'semen_company' => ['nullable', 'string', 'max:255'],
            'insemination_date' => ['required', 'date'],
            'reproduction_record_id' => ['nullable', 'integer', 'exists:reproduction_records,id'],
            'vet_id' => ['nullable', 'integer', 'exists:users,id'],
            'cost' => ['nullable', 'numeric'],
            'remarks' => ['nullable', 'string'],
        ];
    }
}
