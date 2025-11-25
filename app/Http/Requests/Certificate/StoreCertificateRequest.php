<?php

namespace App\Http\Requests\Certificate;

use App\Enums\CertificateType;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Enum;

class StoreCertificateRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /**
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'resident_id' => ['nullable', 'exists:residents,id'],
            'certificate_type' => ['required', new Enum(CertificateType::class)],
            'purpose' => ['required', 'string', 'max:255'],
            'remarks' => ['nullable', 'string'],
            'fee' => ['nullable', 'numeric', 'min:0'],
        ];
    }
}
