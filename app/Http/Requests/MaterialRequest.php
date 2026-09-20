<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Encryption\DecryptException;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Validation\Rule;

class MaterialRequest extends FormRequest
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
            'code' => [
                'required',
                'string',
                'max:50',
                Rule::unique('materials', 'code')->ignore($this->material?->id ?? $this->id),
            ],
            'name'          => ['required', 'string', 'max:255'],
            'initial_stock' => ['nullable', 'numeric', 'min:0'], 
            'stock'         => ['nullable', 'numeric', 'min:0'], 
            'description'   => ['nullable', 'string'],  
            'unit'          => ['required', 'string', 'in:meter,yard,roll'],
            'photo' => [
                    'nullable',
                    'image',
                    'mimes:jpeg,jpg,png',
                    'max:1024',
                    ],
        ];
    }public function messages(): array
{
    return [
        'code.required'         => 'Kode material wajib diisi.',
        'code.string'           => 'Kode material harus berupa teks.',
        'code.max'              => 'Kode material tidak boleh lebih dari :max karakter.',
        'code.unique'           => 'Kode material sudah terdaftar di sistem.',

        'name.required'         => 'Nama material wajib diisi.',
        'name.string'           => 'Nama material harus berupa teks.',
        'name.max'              => 'Nama material tidak boleh lebih dari :max karakter.',

        'initial_stock.numeric' => 'Stok awal harus berupa angka.',
        'initial_stock.min'     => 'Stok awal tidak boleh kurang dari :min.',

        'stock.numeric'         => 'Stok harus berupa angka.',
        'stock.min'             => 'Stok tidak boleh kurang dari :min.',

        'description.string'    => 'Deskripsi harus berupa teks.',

        'unit.required'         => 'Satuan material wajib dipilih.',
        'unit.in'               => 'Satuan yang dipilih harus berupa: meter, yard atau roll.',

        'photo.image'           => 'Berkas yang diunggah harus berupa gambar.',
        'photo.mimes'           => 'Format gambar harus berupa: jpeg, jpg, atau png.',
        'photo.max'             => 'Ukuran gambar tidak boleh lebih dari 1 MB (1024 KB).',
    ];
}

}
