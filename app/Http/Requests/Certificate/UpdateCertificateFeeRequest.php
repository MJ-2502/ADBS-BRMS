<?php

namespace App\Http\Requests\Certificate;

use App\Enums\CertificateType;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class UpdateCertificateFeeRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->isAdmin() ?? false;
    }

    /**
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'fees' => [
                'required',
                'array',
                function (string $attribute, mixed $value, callable $fail): void {
                    $allowed = collect(CertificateType::cases())->map->value->all();
                    foreach (array_keys($value ?? []) as $type) {
                        if (!in_array($type, $allowed, true)) {
                            $fail('Invalid certificate type provided.');
                        }
                    }
                },
            ],
            'fees.*' => ['required', 'numeric', 'min:0'],
        ];
    }

    public function validatedFees(): array
    {
        return collect($this->validated('fees'))
            ->map(fn ($amount) => (float) $amount)
            ->all();
    }
}
