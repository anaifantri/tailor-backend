<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Encryption\DecryptException;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Validation\Rule;

class UserRequest extends FormRequest
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
        $hashedId = $this->route('id'); 

        if ($hashedId) {
            try {
                $decryptedId = Crypt::decryptString($hashedId);

                $this->merge([
                    'id' => (int) $decryptedId,
                ]);
            } catch (DecryptException $e) {
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
			'name'      => ['required', 'string', 'max:255'],
			'username'  => [
				'required',
				'string', 
				'min:6', 
				Rule::unique('users', 'username')->ignore($this->id),
			],
			'email'     => [
				'required',
				'email:rfc,dns',
				Rule::unique('users', 'email')->ignore($this->id),
			],
			'phone'     => [
				'required',
				'string',
				'regex:/^(\+62|62|0)[0-9]{9,12}$/',
				Rule::unique('users', 'phone')->ignore($this->id),
			],
			'photo'     => ['nullable', 'image', 'mimes:jpeg,jpg,png', 'max:1024'],
			'is_active' => ['required', 'boolean'],
		];
    }
	
	public function messages(): array
	{
		return [
			// Name
			'name.required'     => 'Nama lengkap wajib diisi.',
			'name.string'       => 'Nama harus berupa teks.',
			'name.max'          => 'Nama tidak boleh lebih dari 255 karakter.',

			// Username
			'username.required' => 'Username wajib diisi.',
			'username.string'   => 'Username harus berupa teks.',
			'username.min'      => 'Username minimal harus 6 karakter.',
			'username.unique'   => 'Username ini sudah digunakan.',

			// Email
			'email.required'    => 'Alamat email wajib diisi.',
			'email.email'       => 'Format alamat email tidak valid.',
			'email.dns'         => 'Domain email tidak valid atau tidak terdaftar.',
			'email.unique'      => 'Alamat email ini sudah terdaftar.',

			// Phone
			'phone.required'    => 'Nomor telepon wajib diisi.',
			'phone.string'      => 'Nomor telepon harus berupa teks.',
			'phone.regex'       => 'Format nomor telepon tidak valid (gunakan format Indonesia yang benar).',
			'phone.unique'      => 'Nomor telepon ini sudah terdaftar.',

			// Photo
			'photo.image'       => 'File harus berupa gambar.',
			'photo.mimes'       => 'Format gambar harus jpeg, jpg, atau png.',
			'photo.max'         => 'Ukuran gambar tidak boleh lebih dari 1 MB (1024 KB).',

			// Is Active
			'is_active.required'=> 'Status aktif wajib dipilih.',
			'is_active.boolean' => 'Status aktif harus bernilai benar atau salah.',
		];
	}
}
