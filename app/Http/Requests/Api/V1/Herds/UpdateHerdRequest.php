<?php

namespace App\Http\Requests\Api\V1\Herds;

use Illuminate\Foundation\Http\FormRequest;

class UpdateHerdRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        $herd = $this->route('herd');

        if ($herd instanceof \App\Models\Herd) {
            return $this->user()?->can('update', $herd) ?? false;
        }

        return false;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'name' => ['sometimes', 'required', 'string', 'max:255'],
            'code' => ['sometimes', 'nullable', 'string', 'max:50'],
            'description' => ['sometimes', 'nullable', 'string'],
        ];
    }
}
