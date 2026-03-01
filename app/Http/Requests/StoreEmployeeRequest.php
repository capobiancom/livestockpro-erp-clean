<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreEmployeeRequest extends FormRequest
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
            'employee_code' => [
                'nullable',
                'string',
                'max:255',
                Rule::unique('employees', 'employee_code')
                    ->where(fn($q) => $q->where('farm_id', $this->input('farm_id'))),
            ],
            'farm_id' => ['required', 'exists:farms,id'],
            'user_id' => ['required', 'exists:users,id'],
            'department_id' => ['required', 'exists:departments,id'],
            'designation_id' => ['required', 'exists:designations,id'],
            'first_name' => ['required', 'string', 'max:255'],
            'last_name' => ['required', 'string', 'max:255'],
            'gender' => ['required', 'string', 'in:male,female,other'],
            'date_of_birth' => ['nullable', 'date'],
            'phone' => ['nullable', 'string', 'max:20'],
            'email' => ['nullable', 'string', 'email', 'max:255'],
            'address' => ['nullable', 'string', 'max:255'],
            'join_date' => ['required', 'date'],
            'employment_type' => ['required', 'string', 'in:permanent,contract,daily'],
            'salary_type' => ['required', 'string', 'in:monthly,daily,hourly'],
            'status' => ['required', 'string', 'in:active,inactive,terminated'],
            'bonus' => ['nullable', 'numeric', 'min:0'],
            'festival_bonus' => ['nullable', 'numeric', 'min:0'],
            'performance_incentive' => ['nullable', 'numeric', 'min:0'],
            'tax_amount' => ['nullable', 'numeric', 'min:0'],
            'loan_deduction' => ['nullable', 'numeric', 'min:0'],
            'other_deductions' => ['nullable', 'numeric', 'min:0'],
            'user_email' => [
                'required',
                'string',
                'email',
                'max:255',
                Rule::unique('employees', 'user_email')
                    ->where(fn($q) => $q->where('farm_id', $this->input('farm_id'))),
            ],
            'password' => ['required', 'string', 'min:8'],
        ];
    }
}
