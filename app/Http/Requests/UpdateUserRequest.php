<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Encryption\DecryptException;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Validation\Rule;

class UpdateUserRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    protected function prepareForValidation(): void
    {
        // 1. Ambil hashed_id dari parameter URL (misal: /api/users/{user_id})
        // Jika React mengirimkannya lewat body, ganti menjadi: $this->input('hashed_id')
        $hashedId = $this->route('id'); 

        if ($hashedId) {
            try {
                // 2. Dekripsi menjadi ID asli angka
                $decryptedId = Crypt::decryptString($hashedId);

                // 3. Masukkan ID asli ke dalam request payload agar bisa dibaca oleh Rule validasi
                $this->merge([
                    'id' => (int) $decryptedId,
                ]);
            } catch (DecryptException $e) {
                // Jika ID manipulasi / rusak, batalkan proses dengan melemparkan error
                abort(response()->json([
                    'status' => 'error',
                    'message' => 'Format parameter ID tidak valid.'
                ], 400));
            }
        }
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
        'username' => [
            'required',
            'string',
            'min:6',
            Rule::unique('users', 'username')->ignore($this->id), 
        ],
        'name'  => ['required', 'string', 'max:255'],
        'is_active'  => ['required', 'boolean'],
        'email' => [
            'required',
            'email:rfc,dns',
            Rule::unique('users', 'email')->ignore($this->id),
            ],
        'phone'  => [
                'required',
                Rule::unique('users', 'phone')->ignore($this->id),
            ],
        'photo' => [
                'image',
                'mimes:jpeg,jpg,png',
                'max:1024',
                'nullable'
                ],
        ];
    }
}
