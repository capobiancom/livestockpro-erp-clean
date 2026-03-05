<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateEmployeeDocumentRequest extends FormRequest
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
            'employee_id' => ['required', 'exists:employees,id'],
            'farm_id' => ['required', 'exists:farms,id'],
            'user_id' => ['required', 'exists:users,id'],
            'document_type' => ['required', 'string', 'in:NID,License,Certificate'],
            'document_number' => ['nullable', 'string', 'max:255'],
            'expiry_date' => ['nullable', 'date'],
            'file_path' => ['nullable', 'string', 'max:255'],
        ];
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'user_id' => $this->user()->id,
            'farm_id' => $this->user()->farm_id,
        ]);
    }
}
