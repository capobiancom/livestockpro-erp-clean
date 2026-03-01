<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreLeaveTypeRequest extends FormRequest
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
            'name' => ['required', 'string', 'max:255', 'unique:leave_types,name'],
            'farm_id' => ['required', 'exists:farms,id'],
            'user_id' => ['required', 'exists:users,id'],
            'paid' => ['required', 'boolean'],
            'max_days_per_year' => ['required', 'integer', 'min:0'],
        ];
    }

    public function prepareForValidation()
    {
        $this->merge([
            'farm_id' => $this->farm_id ?? auth()->user()->farm_id,
            'user_id' => $this->user_id ?? auth()->user()->id
        ]);
    }
}
