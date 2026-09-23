<?php

namespace App\Http\Requests;

use App\Models\Tailor;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class TailorRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    protected function prepareForValidation(): void
    {
        $ulid = $this->route('ulid');

        if ($ulid) {
            $tailor = Tailor::where('ulid', $ulid)->first();

            if (!$tailor) {
                abort(response()->json([
                    'status' => 'error',
                    'message' => 'Format atau ID ULID tukang jahit tidak valid.'
                ], 400));
            }

            $this->merge([
                'tailor_internal_id' => $tailor->id,
            ]);
        }
    }

    public function rules(): array
    {
        $internalId = $this->tailor_internal_id ?? null;

        return [
            'name' => ['required', 'string', 'max:255'],
            'is_active' => ['required', 'boolean'],
            'email' => [
                'nullable',
                'email:rfc,dns',
                Rule::unique('tailors', 'email')->ignore($internalId),
            ],
            'phone' => [
                'required',
                'string',
                'regex:/^(\+62|62|0)[0-9]{9,12}$/',
                Rule::unique('tailors', 'phone')->ignore($internalId),
            ],
            'address' => [
                'nullable',
                'string',
            ],
            'specialty' => [
                'nullable',
                'array',
            ],
            'specialty.*' => [
                'string',
            ],
            'photo' => [
                'nullable',
                'image',
                'mimes:jpeg,jpg,png',
                'max:1024',
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
            'phone.required' => 'Nomor telepon wajib diisi.',
            'phone.string' => 'Nomor telepon harus berupa teks.',
            'phone.regex' => 'Format nomor telepon tidak valid (gunakan format Indonesia yang benar).',
            'phone.unique' => 'Nomor telepon ini sudah terdaftar.',
            'address.string' => 'Alamat harus berupa teks.',
            'specialty.array' => 'Keahlian harus berupa array.',
            'photo.image' => 'File harus berupa gambar.',
            'photo.mimes' => 'Format gambar harus jpeg, jpg, atau png.',
            'photo.max' => 'Ukuran gambar tidak boleh lebih dari 1 MB (1024 KB).',
            'is_active.required' => 'Status aktif wajib dipilih.',
            'is_active.boolean' => 'Status aktif harus bernilai benar atau salah.',
        ];
    }
}