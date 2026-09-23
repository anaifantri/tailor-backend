<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Encryption\DecryptException;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Crypt;

class OrderCancellationRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }
    
    /**
     * Prepare data for validation.
     */
    protected function prepareForValidation(): void
    {
        $merges = [];

        // 1. Dekripsi ID dari Route Parameter jika ada
        $hashedId = $this->route('id'); 
        if ($hashedId && !is_numeric($hashedId)) {
            try {
                $decryptedId = (int) Crypt::decryptString($hashedId);
                // Menimpa parameter route agar $request->route('id') berisi ID asli
                $this->route()->setParameter('id', $decryptedId);
                $merges['id'] = $decryptedId;
            } catch (DecryptException $e) {
                // Biarkan bernilai invalid agar gagal di aturan validasi
                $merges['id'] = null;
            }
        }

        // 2. Helper dekripsi untuk input payload
        $this->decryptField('order_detail_id', $merges);
        $this->decryptField('user_id', $merges);
        $this->decryptField('order_id', $merges);

        if (!empty($merges)) {
            $this->merge($merges);
        }
    }

    /**
     * Helper privat untuk dekripsi field agar DRY (Don't Repeat Yourself)
     */
    private function decryptField(string $field, array &$merges): void
    {
        $value = $this->input($field);

        if (!empty($value) && !is_numeric($value)) {
            try {
                $merges[$field] = (int) Crypt::decryptString($value);
            } catch (DecryptException $e) {
                $merges[$field] = null; // Di-set null agar memicu error 'required' / 'integer'
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
            'id'               => ['nullable', 'integer'], // Jika 'id' berasal dari route
            'order_id'         => ['required', 'integer', 'exists:orders,id'],
            'order_detail_id'  => ['required', 'integer', 'exists:order_details,id'],
            'user_id'          => ['required', 'integer', 'exists:users,id'],
            'cancellation_type' => ['required', 'string', 'max:50'],
            'refund_amount'    => ['required', 'numeric', 'min:0'],
            'reason'           => ['required', 'string', 'max:500'],
            'cancelled_at'     => ['required', 'date_format:Y-m-d H:i:s'],
        ];
    }

    /**
     * Custom validation messages.
     */
    public function messages(): array
    {
        return [
            'order_id.required'         => 'Order wajib dipilih.',
            'order_id.exists'           => 'Order tidak ditemukan.',
            'order_detail_id.required'  => 'Rincian order wajib dipilih.',
            'order_detail_id.exists'    => 'Rincian order tidak ditemukan.',
            'user_id.required'          => 'Petugas pengirim wajib dipilih.',
            'user_id.exists'            => 'Petugas pengirim tidak ditemukan.',
            'cancellation_type.required' => 'Tipe pembatalan wajib diisi.',
            'refund_amount.required'    => 'Pengembalian wajib diisi.',
            'refund_amount.numeric'     => 'Jumlah pengembalian harus berupa angka.',
            'reason.required'           => 'Alasan pembatalan wajib diisi.',
            'cancelled_at.required'     => 'Tanggal pembatalan wajib diisi.',
            'cancelled_at.date_format'  => 'Format tanggal pembatalan tidak valid (harus Y-m-d H:i:s).',
        ];
    }
}