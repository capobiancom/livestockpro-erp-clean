<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class UpdateSuperAdminEmailSettingsRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->hasRole('Super Admin') ?? false;
    }

    public function rules(): array
    {
        return [
            'super_admin_mail_mailer' => ['nullable', 'string', 'max:50'],
            'super_admin_mail_host' => ['nullable', 'string', 'max:190'],
            'super_admin_mail_port' => ['nullable', 'integer', 'min:1', 'max:65535'],
            'super_admin_mail_username' => ['nullable', 'string', 'max:190'],
            'super_admin_mail_password' => ['nullable', 'string', 'max:500'],
            'super_admin_mail_encryption' => ['nullable', 'string', 'max:20'],

            'super_admin_mail_from_address' => ['nullable', 'email:rfc,dns', 'max:190'],
            'super_admin_mail_from_name' => ['nullable', 'string', 'max:190'],
        ];
    }
}
