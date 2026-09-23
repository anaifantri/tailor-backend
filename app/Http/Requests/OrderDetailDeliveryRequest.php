<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Encryption\DecryptException;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Crypt;

class OrderDetailDeliveryRequest extends FormRequest
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
        $merges = [];

        // 1. Dekripsi ID dari route parameter jika ada
        $hashedId = $this->route('id'); 
        if ($hashedId && !is_numeric($hashedId)) {
            try {
                $merges['id'] = (int) Crypt::decryptString($hashedId);
            } catch (DecryptException $e) {
                abort(response()->json([
                    'status'  => 'error',
                    'message' => 'Format parameter ID tidak valid.'
                ], 400));
            }
        }

        // 2. Dekripsi order_detail_id jika dikirim sebagai hashed_id
        if ($this->has('order_detail_id') && !empty($this->order_detail_id) && !is_numeric($this->order_detail_id)) {
            try {
                $merges['order_detail_id'] = (int) Crypt::decryptString($this->order_detail_id);
            } catch (DecryptException $e) {
                $merges['order_detail_id'] = null; // Biarkan gagal di validasi 'exists'
            }
        }

        // 3. Dekripsi user_id jika dikirim sebagai hashed_id
        if ($this->has('user_id') && !empty($this->user_id) && !is_numeric($this->user_id)) {
            try {
                $merges['user_id'] = (int) Crypt::decryptString($this->user_id);
            } catch (DecryptException $e) {
                $merges['user_id'] = null; // Biarkan gagal di validasi 'exists'
            }
        }

        if (!empty($merges)) {
            $this->merge($merges);
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
            'order_detail_id'    => ['required', 'integer', 'exists:order_details,id'],
            'user_id'            => ['required', 'integer', 'exists:users,id'],
            'quantity_delivered' => ['required', 'integer', 'min:1'],
            'delivery_date'      => ['required', 'date'],
            'recipient_name'     => ['required', 'string', 'max:255'],
            'recipient_relation' => ['required', 'string', 'max:255'],
            'notes'              => ['nullable', 'string', 'max:500'],
        ];
    }

    public function messages(): array
    {
        return [
            'order_detail_id.required'    => 'Rincian order wajib dipilih.',
            'order_detail_id.exists'      => 'Rincian order tidak ditemukan.',
            'user_id.required'            => 'Petugas pengirim wajib dipilih.',
            'user_id.exists'              => 'Petugas pengirim tidak ditemukan.',
            'quantity_delivered.required' => 'Jumlah barang yang diserahkan wajib diisi.',
            'quantity_delivered.integer'  => 'Jumlah barang harus berupa angka bulat.',
            'quantity_delivered.min'      => 'Jumlah barang minimal :min.',
            'delivery_date.required'      => 'Tanggal pengiriman wajib diisi.',
            'delivery_date.date'          => 'Format tanggal pengiriman tidak valid.',
            'recipient_name.required'     => 'Nama penerima wajib diisi.',
            'recipient_name.string'       => 'Nama penerima harus berupa teks.',
            'recipient_relation.required' => 'Hubungan penerima wajib diisi.',
            'recipient_relation.string'   => 'Hubungan penerima harus berupa teks.',
            'notes.max'                   => 'Catatan maksimal :max karakter.',
        ];
    }
}