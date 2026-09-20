<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Encryption\DecryptException;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Validation\Rule;

class OrderRequest extends FormRequest
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
            'user_id'  => ['required'],
            'number'  => [
                    'required',
					'string',
                    Rule::unique('orders', 'number')->ignore($this->id),
                ],
            'customer_id'  => ['required'],
            'order_date'  => ['required', 'date'],
            'fitting_date'  => ['nullable', 'date', 'after_or_equal:order_date'],
            'due_date'  => ['required', 'date', 'after_or_equal:order_date'],
            'amount_paid'  => ['nullable', 'numeric', 'min:0'],
            'payment_method'  => ['nullable', 'string', Rule::in(['Cash', 'Card', 'Transfer-BCA', 'Transfer-BNI', 'Transfer-BRI'])],
            'payment_date'  => ['nullable', 'date', 'required_with:amount_paid'],
            'discount' => [
                'nullable',
                'numeric',
                'decimal:0,2',
                'min:0',
                ],
            'tax' => [
                'nullable',
                'numeric',
                'decimal:0,2',
                'min:0',
                ],
            'total' => [
                'required',
                'numeric',
                'decimal:0,2',
                'min:0',
                ],
            'notes'  => ['nullable', 'string', 'max:5000'],
            'order_details'  => ['required', 'array', 'min:1'],
			'order_details.*.clothing_type_id' => ['required'],
			'order_details.*.material_id'   => ['nullable'],
			'order_details.*.quantity'      => ['required'],
			'order_details.*.price'      => ['required', 'numeric', 'min:0'],
			'order_details.*.measurements'      => ['required'],
			'order_details.*.fabric_cosumed_meter'      => ['nullable', 'numeric', 'min:0'],
			'order_details.*.notes'      => ['nullable', 'string', 'max:5000'],
        ];
    }
	
	public function messages(): array
	{
		return [
			// Validasi User & Customer
			'user_id.required'    => 'Kolom user wajib diisi.',
			'user_id.exists'      => 'User yang dipilih tidak valid.',
			'customer_id.required'=> 'Kolom customer wajib diisi.',
			'customer_id.exists'  => 'Customer yang dipilih tidak valid.',

			// Validasi Nomor Order
			'number.required'     => 'Nomor order wajib diisi.',
			'number.string'       => 'Nomor order harus berupa teks.',
			'number.unique'       => 'Nomor order sudah digunakan.',

			// Validasi Tanggal
			'order_date.required' => 'Tanggal order wajib diisi.',
			'order_date.date'     => 'Format tanggal order tidak valid.',
			'fitting_date.date'   => 'Format tanggal fitting tidak valid.',
			'fitting_date.after_or_equal' => 'Tanggal fitting tidak boleh sebelum tanggal order.',
			'due_date.required'   => 'Tanggal jatuh tempo wajib diisi.',
			'due_date.date'       => 'Format tanggal jatuh tempo tidak valid.',
			'due_date.after_or_equal' => 'Tanggal jatuh tempo tidak boleh sebelum tanggal order.',

			// Validasi Pembayaran & Keuangan
			'amount_paid.numeric' => 'Jumlah bayar harus berupa angka.',
			'amount_paid.min'     => 'Jumlah bayar tidak boleh kurang dari 0.',
			'payment_method.string' => 'Metode pembayaran harus berupa teks.',
			'payment_method.in'   => 'Metode pembayaran yang dipilih tidak valid.',
			'payment_date.date'   => 'Format tanggal pembayaran tidak valid.',
			'payment_date.required_with' => 'Tanggal pembayaran wajib diisi jika ada jumlah bayar.',

			// Validasi Diskon, Pajak, Total
			'discount.numeric'    => 'Diskon harus berupa angka.',
			'discount.decimal'    => 'Diskon maksimal memiliki 2 angka di belakang koma.',
			'discount.min'        => 'Diskon tidak boleh kurang dari 0.',
			'tax.numeric'         => 'Pajak harus berupa angka.',
			'tax.decimal'         => 'Pajak maksimal memiliki 2 angka di belakang koma.',
			'tax.min'             => 'Pajak tidak boleh kurang dari 0.',
			'total.required'      => 'Total harga wajib diisi.',
			'total.numeric'       => 'Total harga harus berupa angka.',
			'total.decimal'       => 'Total harga maksimal memiliki 2 angka di belakang koma.',
			'total.min'           => 'Total harga tidak boleh kurang dari 0.',
			'notes.string'        => 'Catatan harus berupa teks atau string.',

			// Validasi Array Order Details
			'order_details.required' => 'Detail order wajib diisi.',
			'order_details.array'    => 'Format detail order harus berupa list/array.',
			'order_details.min'      => 'Minimal harus ada 1 detail order.',

			// Validasi Item di Dalam Order Details (Menggunakan *)
			'order_details.*.clothing_type_id.required' => 'Jenis pakaian pada item ke-:index wajib diisi.',
			'order_details.*.clothing_type_id.exists'   => 'Jenis pakaian pada item ke-:index tidak valid.',
			'order_details.*.material_id.exists'        => 'Bahan/material pada item ke-:index tidak valid.',
			
			'order_details.*.quantity.required' => 'Jumlah (quantity) pada item ke-:index wajib diisi.',
			'order_details.*.quantity.integer'  => 'Jumlah (quantity) pada item ke-:index harus berupa bilangan bulat.',
			'order_details.*.quantity.min'      => 'Jumlah (quantity) pada item ke-:index tidak boleh kurang dari 0.',
			
			'order_details.*.price.required'    => 'Harga pada item ke-:index wajib diisi.',
			'order_details.*.price.numeric'     => 'Harga pada item ke-:index harus berupa angka.',
			'order_details.*.price.min'         => 'Harga pada item ke-:index tidak boleh kurang dari 0.',
			
			'order_details.*.measurements.required' => 'Ukuran pada item ke-:index wajib diisi.',
			'order_details.*.measurements.numeric'  => 'Ukuran pada item ke-:index harus berupa angka.',
			'order_details.*.measurements.min'      => 'Ukuran pada item ke-:index tidak boleh kurang dari 0.',
			
			'order_details.*.fabric_cosumed_meter.numeric' => 'Kain yang digunakan pada item ke-:index harus berupa angka.',
			'order_details.*.fabric_cosumed_meter.min'     => 'Kain yang digunakan pada item ke-:index tidak boleh kurang dari 0.',
			
			'order_details.*.notes.string'      => 'Catatan pada item ke-:index harus berupa teks.',
			'order_details.*.notes.max'         => 'Catatan pada item ke-:index maksimal 5000 karakter.',
		];
	}


}
