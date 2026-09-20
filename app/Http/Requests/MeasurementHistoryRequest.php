<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Encryption\DecryptException;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Crypt;

class MeasurementHistoryRequest extends FormRequest
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
            'customer_id'  => ['required'],
            'clothing_type_id'  => ['required'],
            'category'  => ['required'],
            'measured_at'  => ['required', 'date'],
            'measured_by'  => ['required', 'string', 'max:255'],
            'measurement_details'  => ['required'],
            'notes'  => ['nullable', 'string'],
        ];
    }
	
	public function messages(): array
	{
		return [
			'customer_id.required'         => 'Kolom pelanggan wajib diisi.',
			'clothing_type_id.required'    => 'Kolom jenis pakaian wajib diisi.',
			'category.required'             => 'Kolom katagory pakaian wajib dipilih.',
			'measured_at.required'         => 'Tanggal pengukuran wajib diisi.',
			
			'measured_by.required'         => 'Kolom diukur oleh wajib diisi.',
			'measured_by.string'           => 'Kolom diukur oleh harus berupa teks.',
			'measured_by.max'              => 'Kolom diukur oleh tidak boleh lebih dari :max karakter.',
			
			'measured_at.required'         => 'Tanggal pengukuran wajib diisi.',
			'measured_at.date'     			=> 'Format tanggal pengukuran tidak valid.',
			
			'measurement_details.required' 	=> 'Kolo Bagian yang perlu wajib diisi.',
			
			'notes.string'                 => 'Catatan harus berupa teks atau string.',
		];
	}

}
