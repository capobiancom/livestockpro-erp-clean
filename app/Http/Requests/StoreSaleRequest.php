<?php

namespace App\Http\Requests;

use App\Enums\SaleStatus;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

class StoreSaleRequest extends FormRequest
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
        $farmId = $user->farm_id;

        return [
            'customer_id' => [
                'required',
                Rule::exists('customers', 'id')->where(function ($query) use ($farmId) {
                    $query->where('farm_id', $farmId);
                }),
            ],
            'invoice_date' => ['required', 'date'],
            'total_amount' => ['required', 'numeric', 'min:0'],
            'paid_amount' => ['required', 'numeric', 'min:0', 'lte:total_amount'],
            'status' => ['required', Rule::enum(SaleStatus::class)],
            'sales_items' => ['required', 'array', 'min:1'],
            'sales_items.*.item_type' => ['required', 'string', Rule::in(['App\\Models\\InventoryItem', 'App\\Models\\Animal', 'App\\Models\\MilkSale'])],
            'sales_items.*.item_id' => [
                'required',
                'integer',
                function ($attribute, $value, $fail) use ($farmId) {
                    $index = explode('.', $attribute)[1];
                    $itemType = $this->input("sales_items.{$index}.item_type");

                    if ($itemType === 'App\\Models\\InventoryItem') {
                        if (! \App\Models\InventoryItem::where('id', $value)->where('farm_id', $farmId)->exists()) {
                            $fail("The selected inventory item is invalid for this farm.");
                        }
                    } elseif ($itemType === 'App\\Models\\Animal') {
                        if (! \App\Models\Animal::where('id', $value)->where('farm_id', $farmId)->exists()) {
                            $fail("The selected animal is invalid for this farm.");
                        }
                    } elseif ($itemType === 'App\\Models\\MilkSale') {
                        if (! \App\Models\MilkSale::where('id', $value)->where('farm_id', $farmId)->exists()) {
                            $fail("The selected milk sale is invalid for this farm.");
                        }
                    }
                },
            ],
            'sales_items.*.quantity' => ['required', 'integer', 'min:1'],
            'invoice_number' => ['nullable', 'string', 'max:255', 'unique:sales,invoice_number'],
            'sales_items.*.unit_price' => ['required', 'numeric', 'min:0'],
            'sales_items.*.total_price' => ['required', 'numeric', 'min:0'],
        ];
    }

    protected function prepareForValidation(): void
    {
        $user = Auth::user();
        $this->merge([
            'farm_id' => $user->farm_id,
            'user_id' => $user->id,
            'invoice_number' => $this->invoice_number ?? \App\Models\Sale::generateInvoiceNumber(),
        ]);
    }
}
