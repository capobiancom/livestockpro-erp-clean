<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;
use App\Models\LeaveRequest;

class UpdateLeaveRequestRequest extends FormRequest
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
            'employee_id' => ['sometimes', 'required', 'exists:employees,id'],
            'leave_type_id' => ['sometimes', 'required', 'exists:leave_types,id'],
            'start_date' => ['sometimes', 'required', 'date'],
            'end_date' => ['sometimes', 'required', 'date', 'after_or_equal:start_date'],
            'total_days' => ['sometimes', 'required', 'integer', 'min:1'],
            'reason' => ['nullable', 'string', 'max:500'],
            'status' => ['sometimes', 'required', 'in:pending,approved,rejected'],
            'approved_by' => ['nullable', 'exists:employees,id'],
        ];
    }
}
