<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreFeedingRecordRequest extends FormRequest
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
            'animal_id' => 'nullable|exists:animals,id',
            'group_id' => 'nullable|exists:herds,id',
            'feeding_date' => 'required|date',
            'feeding_time' => 'required|in:morning,evening',
            'notes' => 'nullable|string|max:1000',
            'feeding_items' => 'required|array|min:1',
            'feeding_items.*.item_id' => 'required|exists:inventory_items,id',
            'feeding_items.*.quantity' => 'required|numeric|min:0.01',
        ];
    }

    public function withValidator($validator)
    {
        $validator->after(function ($validator) {
            if (!$this->input('animal_id') && !$this->input('group_id')) {
                $validator->errors()->add('animal_id', 'Either an animal or a group must be selected.');
            }
        });
    }
}
