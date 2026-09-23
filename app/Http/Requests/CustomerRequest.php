<?php

namespace App\Http\Requests;

use App\Models\Customer;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class CustomerRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    protected function prepareForValidation(): void
    {
        $ulid = $this->route('ulid');

        if ($ulid) {
            $customer = Customer::where('ulid', $ulid)->first();

            if (!$customer) {
                abort(response()->json([
                    'status' => 'error',
                    'message' => 'Format atau ID ULID pelanggan tidak valid.'
                ], 400));
            }

            $this->merge([
                'customer_internal_id' => $customer->id,
            ]);
        }
    }

    public function rules(): array
    {
        $internalId = $this->customer_internal_id ?? null;

        return [
            'name' => ['required', 'string', 'max:255'],
            'email' => [
                'nullable',
                'email:rfc,dns',
                Rule::unique('customers', 'email')->ignore($internalId),
            ],
            'phone' => [
                'nullable',
                'string',
                'regex:/^(\+62|62|0)[0-9]{9,12}$/',
                Rule::unique('customers', 'phone')->ignore($internalId),
            ],
            'address' => [
                'nullable',
                'string',
            ],
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => 'Nama lengkap wajib diisi.',
            'name.string' => 'Nama harus berupa teks.',
            'name.max' => 'Nama tidak boleh lebih dari 255 karakter.',
            'email.email' => 'Format alamat email tidak valid.',
            'email.dns' => 'Domain email tidak valid atau tidak terdaftar.',
            'email.unique' => 'Alamat email ini sudah terdaftar.',
            'phone.string' => 'Nomor telepon harus berupa teks.',
            'phone.regex' => 'Format nomor telepon tidak valid (gunakan format Indonesia yang benar).',
            'phone.unique' => 'Nomor telepon ini sudah terdaftar.',
            'address.string' => 'Alamat harus berupa teks.',
        ];
    }
}