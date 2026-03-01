<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateEmployeeRequest extends FormRequest
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
        $employee = $this->route('employee');
        $employeeId = $employee->id;
        $userId = $employee->user_id;
        $farmId = $this->input('farm_id');

        return [
            'employee_code' => [
                'nullable',
                'string',
                'max:255',
                Rule::unique('employees', 'employee_code')
                    ->ignore($employeeId)
                    ->where(fn($q) => $q->where('farm_id', $farmId)),
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
                    ->ignore($employeeId)
                    ->where(fn($q) => $q->where('farm_id', $farmId)),
            ],
            'password' => ['nullable', 'string', 'min:8'], // Password can be nullable on update
        ];
    }
}
