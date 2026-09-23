<?php

namespace App\Http\Requests;

use App\Models\OrderDetail;
use Illuminate\Foundation\Http\FormRequest;

class ProductionProgressRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    protected function prepareForValidation(): void
    {
        if ($this->has('order_detail_ulid')) {
            $orderDetail = OrderDetail::where('ulid', $this->order_detail_ulid)->first();
            if ($orderDetail) {
                $this->merge([
                    'order_detail_id' => $orderDetail->id,
                ]);
            }
        }
    }

    public function rules(): array
    {
        return [
            'order_detail_ulid' => ['required', 'string', 'exists:order_details,ulid'],
            'order_detail_id'   => ['required', 'integer', 'exists:order_details,id'],
            'progress_date'     => ['required', 'date'],
            'status'            => ['required', 'string', 'max:100'],
            'notes'             => ['nullable', 'string', 'max:5000'],
        ];
    }

    public function messages(): array
    {
        return [
            'order_detail_ulid.required' => 'Detail pesanan wajib dipilih.',
            'order_detail_ulid.exists'   => 'Data detail pesanan tidak valid.',
            'progress_date.required'     => 'Tanggal progress pengerjaan wajib diisi.',
            'progress_date.date'         => 'Format tanggal progress tidak valid.',
            'status.required'            => 'Status pengerjaan wajib diisi.',
        ];
    }
}