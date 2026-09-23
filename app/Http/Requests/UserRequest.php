<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UserRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        // Mengambil ULID dari parameter route 'users/{ulid}'
        $ulid = $this->route('ulid');

        return [
            'name'      => ['required', 'string', 'max:255'],
            'username'  => [
                'required',
                'string', 
                'min:6', 
                Rule::unique('users', 'username')->ignore($ulid, 'ulid'),
            ],
            'email'     => [
                'required',
                'email:rfc,dns',
                Rule::unique('users', 'email')->ignore($ulid, 'ulid'),
            ],
            'phone'     => [
                'required',
                'string',
                'regex:/^(\+62|62|0)[0-9]{9,12}$/',
                Rule::unique('users', 'phone')->ignore($ulid, 'ulid'),
            ],
            'photo'     => ['nullable', 'image', 'mimes:jpeg,jpg,png', 'max:1024'],
            'is_active' => ['required', 'boolean'],
            
            // Password wajib saat POST (create), opsional saat PUT/PATCH (update)
            'password'  => $this->isMethod('post') 
                ? ['required', 'string', 'min:6'] 
                : ['nullable', 'string', 'min:6'],
        ];
    }
    
    public function messages(): array
    {
        return [
            'name.required'     => 'Nama lengkap wajib diisi.',
            'name.string'       => 'Nama harus berupa teks.',
            'name.max'          => 'Nama tidak boleh lebih dari 255 karakter.',
            'username.required' => 'Username wajib diisi.',
            'username.string'   => 'Username harus berupa teks.',
            'username.min'      => 'Username minimal harus 6 karakter.',
            'username.unique'   => 'Username ini sudah digunakan.',
            'email.required'    => 'Alamat email wajib diisi.',
            'email.email'       => 'Format alamat email tidak valid.',
            'email.dns'         => 'Domain email tidak valid atau tidak terdaftar.',
            'email.unique'      => 'Alamat email ini sudah terdaftar.',
            'phone.required'    => 'Nomor telepon wajib diisi.',
            'phone.string'      => 'Nomor telepon harus berupa teks.',
            'phone.regex'       => 'Format nomor telepon tidak valid (gunakan format Indonesia yang benar).',
            'phone.unique'      => 'Nomor telepon ini sudah terdaftar.',
            'photo.image'       => 'File harus berupa gambar.',
            'photo.mimes'       => 'Format gambar harus jpeg, jpg, atau png.',
            'photo.max'         => 'Ukuran gambar tidak boleh lebih dari 1 MB (1024 KB).',
            'is_active.required'=> 'Status aktif wajib dipilih.',
            'is_active.boolean' => 'Status aktif harus bernilai benar atau salah.',
            'password.required' => 'Password wajib diisi.',
            'password.min'      => 'Password minimal harus 6 karakter.',
        ];
    }
}