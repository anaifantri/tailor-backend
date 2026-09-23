<?php

namespace App\Http\Requests;

use App\Models\Order;
use App\Models\User;
use Illuminate\Foundation\Http\FormRequest;

class PaymentRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    protected function prepareForValidation(): void
    {
        $merges = [];

        if ($this->has('user_ulid')) {
            $user = User::where('ulid', $this->user_ulid)->first();
            if ($user) {
                $merges['user_id'] = $user->id;
            }
        }

        if ($this->has('order_ulid')) {
            $order = Order::where('ulid', $this->order_ulid)->first();
            if ($order) {
                $merges['order_id'] = $order->id;
            }
        }

        if (!empty($merges)) {
            $this->merge($merges);
        }
    }

    public function rules(): array
    {
        return [
            'user_ulid'      => ['required', 'string', 'exists:users,ulid'],
            'user_id'        => ['required', 'integer', 'exists:users,id'],
            'order_ulid'     => ['required', 'string', 'exists:orders,ulid'],
            'order_id'       => ['required', 'integer', 'exists:orders,id'],
            'payment_date'   => ['required', 'date'],
            'payment_method' => ['required', 'string', 'max:100'],
            'amount_paid'    => ['required', 'numeric', 'min:0'],
            'payment_status' => ['required', 'string', 'max:50'],
            'notes'          => ['nullable', 'string', 'max:5000'],
        ];
    }

    public function messages(): array
    {
        return [
            'user_ulid.required'      => 'Pengguna wajib dipilih.',
            'user_ulid.exists'        => 'Data pengguna tidak ditemukan.',
            'order_ulid.required'     => 'Pesanan wajib dipilih.',
            'order_ulid.exists'       => 'Data pesanan tidak ditemukan.',
            'payment_date.required'   => 'Tanggal pembayaran wajib diisi.',
            'payment_date.date'       => 'Format tanggal pembayaran tidak valid.',
            'payment_method.required' => 'Metode pembayaran wajib diisi.',
            'amount_paid.required'    => 'Jumlah pembayaran wajib diisi.',
            'amount_paid.numeric'     => 'Jumlah pembayaran harus berupa angka.',
            'payment_status.required' => 'Status pembayaran wajib diisi.',
        ];
    }
}