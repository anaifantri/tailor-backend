<?php

namespace App\Http\Requests;

use App\Models\ClothingType;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ClothingTypeRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    protected function prepareForValidation(): void
    {
        $ulid = $this->route('ulid');

        if ($ulid) {
            $clothingType = ClothingType::where('ulid', $ulid)->first();

            if (!$clothingType) {
                abort(response()->json([
                    'status' => 'error',
                    'message' => 'Format atau ID ULID jenis pakaian tidak valid.'
                ], 400));
            }

            $this->merge([
                'clothing_type_internal_id' => $clothingType->id,
            ]);
        }
    }

    public function rules(): array
    {
        $internalId = $this->clothing_type_internal_id ?? null;

        return [
            'code' => [
                'required',
                'string',
                'max:8',
                Rule::unique('clothing_types', 'code')->ignore($internalId),
            ],
            'type' => [
                'required',
                'string',
                'max:255',
                Rule::unique('clothing_types', 'type')->ignore($internalId),
            ],
            'category'   => ['required', 'string', 'in:baju,celana,rok'],
            'base_price' => ['nullable', 'numeric', 'min:0'],
        ];
    }

    public function messages(): array
    {
        return [
            'code.required'     => 'Kode jenis pakaian wajib diisi.',
            'code.string'       => 'Kode jenis pakaian harus berupa teks.',
            'code.max'          => 'Kode jenis pakaian tidak boleh lebih dari :max karakter.',
            'code.unique'       => 'Kode jenis pakaian sudah terdaftar di sistem.',

            'type.required'     => 'Nama jenis pakaian wajib diisi.',
            'type.string'       => 'Nama jenis pakaian harus berupa teks.',
            'type.max'          => 'Nama jenis pakaian tidak boleh lebih dari :max karakter.',
            'type.unique'       => 'Nama jenis pakaian sudah terdaftar di sistem.',

            'category.required' => 'Category pakaian wajib dipilih.',
            'category.in'       => 'Category yang dipilih harus berupa: baju, celana atau rok.',

            'base_price.numeric'=> 'Harga dasar harus berupa angka.',
            'base_price.min'    => 'Harga dasar tidak boleh kurang dari :min.',
        ];
    }
}