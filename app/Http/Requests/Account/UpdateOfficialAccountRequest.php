<?php

namespace App\Http\Requests\Account;

use App\Enums\UserRole;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateOfficialAccountRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->canManageAccounts() ?? false;
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        $official = $this->route('official');

        return [
            'name' => ['required', 'string', 'max:120'],
            'email' => ['required', 'email', 'max:255', Rule::unique('users', 'email')->ignore($official)],
            'role' => ['required', Rule::in(UserRole::staffRoles())],
            'password' => ['nullable', 'string', 'min:8'],
            'phone' => ['nullable', 'string', 'max:20'],
            'purok' => ['nullable', 'string', 'max:50'],
            'address_line' => ['nullable', 'string', 'max:255'],
            'is_active' => ['required', 'boolean'],
        ];
    }
}
