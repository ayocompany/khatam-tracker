<?php

namespace App\Http\Requests\Auth;

use Illuminate\Foundation\Http\FormRequest;

class SendOtpRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Normalize Indonesian phone number to +62 format.
     */
    protected function prepareForValidation(): void
    {
        $phone = (string) $this->input('phone', '');
        $digits = preg_replace('/\D+/', '', $phone ?? '');

        if (str_starts_with($digits, '0')) {
            $digits = '62'.substr($digits, 1);
        }

        if (str_starts_with($digits, '8')) {
            $digits = '62'.$digits;
        }

        if (! str_starts_with($digits, '62')) {
            $this->merge(['phone' => $digits]);
            return;
        }

        $this->merge(['phone' => '+'.$digits]);
    }

    public function rules(): array
    {
        return [
            'phone' => ['required', 'string', 'regex:/^\+62[1-9][0-9]{7,12}$/'],
        ];
    }

    public function messages(): array
    {
        return [
            'phone.regex' => 'Nomor WhatsApp tidak valid. Gunakan format Indonesia (contoh: 08xxxx atau +628xxxx).',
        ];
    }
}
