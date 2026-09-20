<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Encryption\DecryptException;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Validation\Rule;

class ClothingTypeRequest extends FormRequest
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
            'code'  => ['required', 'string', 'max:8',
                Rule::unique('clothing_types', 'code')->ignore($this->id) ],
            'type'  => ['required', 'string', 'max:255',
                Rule::unique('clothing_types', 'type')->ignore($this->id) ],
            'category'  => ['required', 'string', 'in:baju,celana,rok'],
            'base_price'  => ['nullable','numeric', 'min:0'],
        ];
    }
		public function messages(): array
		{
			return [
				'code.required'         => 'Kode jenis pakaian wajib diisi.',
				'code.string'           => 'Kode jenis pakaian harus berupa teks.',
				'code.max'              => 'Kode jenis pakaian tidak boleh lebih dari :max karakter.',
				'code.unique'           => 'Kode jenis pakaian sudah terdaftar di sistem.',

				'type.required'         => 'Nama jenis pakaian wajib diisi.',
				'type.string'           => 'Nama jenis pakaian harus berupa teks.',
				'type.max'              => 'Nama jenis pakaian tidak boleh lebih dari :max karakter.',
				'type.unique'           => 'Nama jenis pakaian sudah terdaftar di sistem.',

                'category.required'         => 'Category pakaian wajib dipilih.',
                'category.in'               => 'Category yang dipilih harus berupa: baju, celana atau rok.',

				'base_price.numeric'    => 'Harga dasar harus berupa angka.',
				'base_price.min'        => 'Harga dasar tidak boleh kurang dari :min.',
			];
		}
}
