<?php

namespace App\Http\Requests;

use App\Enums\CalfGender;
use App\Enums\HealthStatus;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Auth;

class CalfRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return Auth::check();
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $user = Auth::user();
        $calfId = $this->route('calf') ? $this->route('calf')->id : null;

        return [
            'mother_id' => ['required', 'exists:animals,id'],
            'father_id' => ['nullable', 'exists:animals,id'],
            'gender' => ['required', Rule::enum(CalfGender::class)],
            'birth_date' => ['required', 'date'],
            'birth_weight' => ['nullable', 'numeric', 'min:0'],
            'health_status' => ['required', Rule::enum(HealthStatus::class)],
        ];
    }
}
