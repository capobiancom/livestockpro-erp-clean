<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateJournalEntryRequest extends FormRequest
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
            'entry_date' => ['sometimes', 'required', 'date'],
            'reference_type' => ['sometimes', 'required', 'string'],
            'reference_id' => ['nullable', 'integer'],
            'description' => ['nullable', 'string'],
            'status' => ['sometimes', 'required', 'string'],
            'lines' => ['sometimes', 'required', 'array', 'min:1'],
            'lines.*.account_id' => ['required', 'exists:chart_of_accounts,id'],
            'lines.*.debit_amount' => ['required', 'numeric', 'min:0'],
            'lines.*.credit_amount' => ['required', 'numeric', 'min:0'],
            'lines.*.narration' => ['nullable', 'string'],
        ];
    }
}
