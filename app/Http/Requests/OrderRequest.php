<?php

namespace App\Http\Requests;

use App\Models\Customer;
use App\Models\Order;
use App\Models\User;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class OrderRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    protected function prepareForValidation(): void
    {
        $internalId = null;
        if ($this->route('order')) {
            $order = Order::where('ulid', $this->route('order'))->first();
            if ($order) {
                $internalId = $order->id;
            }
        }

        // Resolusi ULID Customer ke ID
        if ($this->has('customer_ulid')) {
            $customer = Customer::where('ulid', $this->customer_ulid)->first();
            if ($customer) {
                $this->merge(['customer_id' => $customer->id]);
            }
        }

        // Resolusi ULID User ke ID
        if ($this->has('user_ulid')) {
            $user = User::where('ulid', $this->user_ulid)->first();
            if ($user) {
                $this->merge(['user_id' => $user->id]);
            }
        }

        if ($internalId) {
            $this->merge(['internal_id' => $internalId]);
        }
    }

    public function rules(): array
    {
        return [
            'user_ulid' => ['required', 'string', 'exists:users,ulid'],
            'user_id' => ['required', 'integer', 'exists:users,id'],
            'customer_ulid' => ['required', 'string', 'exists:customers,ulid'],
            'customer_id' => ['required', 'integer', 'exists:customers,id'],
            'number' => [
                'required',
                'string',
                Rule::unique('orders', 'number')->ignore($this->internal_id),
            ],
            'order_date' => ['required', 'date'],
            'fitting_date' => ['nullable', 'date', 'after_or_equal:order_date'],
            'due_date' => ['required', 'date', 'after_or_equal:order_date'],
            'amount_paid' => ['nullable', 'numeric', 'min:0'],
            'payment_method' => ['nullable', 'string', Rule::in(['Cash', 'Card', 'Transfer-BCA', 'Transfer-BNI', 'Transfer-BRI'])],
            'payment_date' => ['nullable', 'date', 'required_with:amount_paid'],
            'discount' => ['nullable', 'numeric', 'min:0'],
            'tax' => ['nullable', 'numeric', 'min:0'],
            'total' => ['required', 'numeric', 'min:0'],
            'notes' => ['nullable', 'string', 'max:5000'],
            
            'order_details' => ['required', 'array', 'min:1'],
            'order_details.*.clothing_type_ulid' => ['required', 'string', 'exists:clothing_types,ulid'],
            'order_details.*.material_ulid' => ['nullable', 'string', 'exists:materials,ulid'],
            'order_details.*.measurement_history_ulid' => ['nullable', 'string', 'exists:measurement_histories,ulid'],
            'order_details.*.quantity' => ['required', 'integer', 'min:1'],
            'order_details.*.price' => ['required', 'numeric', 'min:0'],
            'order_details.*.measurements' => ['required'],
            'order_details.*.fabric_consumed_meter' => ['nullable', 'numeric', 'min:0'],
            'order_details.*.notes' => ['nullable', 'string', 'max:5000'],
        ];
    }

    public function messages(): array
    {
        return [
            'user_ulid.required' => 'Pengguna wajib dipilih.',
            'user_ulid.exists' => 'Data pengguna tidak valid.',
            'customer_ulid.required' => 'Pelanggan wajib dipilih.',
            'customer_ulid.exists' => 'Data pelanggan tidak valid.',

            'number.required' => 'Nomor order wajib diisi.',
            'number.unique' => 'Nomor order sudah digunakan.',

            'order_date.required' => 'Tanggal order wajib diisi.',
            'fitting_date.after_or_equal' => 'Tanggal fitting tidak boleh sebelum tanggal order.',
            'due_date.required' => 'Tanggal jatuh tempo wajib diisi.',
            'due_date.after_or_equal' => 'Tanggal jatuh tempo tidak boleh sebelum tanggal order.',

            'total.required' => 'Total harga wajib diisi.',
            'order_details.required' => 'Detail pesanan wajib diisi.',
            'order_details.min' => 'Minimal harus ada 1 detail pesanan.',

            'order_details.*.clothing_type_ulid.required' => 'Jenis pakaian pada item ke-:index wajib dipilih.',
            'order_details.*.clothing_type_ulid.exists' => 'Jenis pakaian pada item ke-:index tidak valid.',
            'order_details.*.quantity.required' => 'Jumlah pesanan item ke-:index wajib diisi.',
            'order_details.*.price.required' => 'Harga pesanan item ke-:index wajib diisi.',
        ];
    }
}