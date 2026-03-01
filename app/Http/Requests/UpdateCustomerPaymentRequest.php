<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

class UpdateCustomerPaymentRequest extends FormRequest
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
        $user = Auth::user();
        $farmId = $user->farm_id;

        return [
            'customer_id' => [
                'required',
                Rule::exists('customers', 'id')->where(function ($query) use ($farmId) {
                    $query->where('farm_id', $farmId);
                }),
            ],
            'payable_id' => ['required', 'numeric'],
            'payable_type' => ['required', 'string', Rule::in(['App\\Models\\Sale', 'App\\Models\\MilkSale'])],
            'transaction_date' => ['required', 'date'],
            'amount' => ['required', 'numeric'],
            'payment_method' => ['required', 'string', Rule::in(['Cash', 'Bank Transfer', 'Mobile Banking'])],
            'reference_number' => ['nullable', 'string', 'max:255'],
            'notes' => ['nullable', 'string'],
        ];
    }
}
