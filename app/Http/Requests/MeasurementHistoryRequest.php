<?php

namespace App\Http\Requests;

use App\Models\Customer;
use App\Models\ClothingType;
use Illuminate\Foundation\Http\FormRequest;

class MeasurementHistoryRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    protected function prepareForValidation(): void
    {
        // Resolusi ULID Customer ke internal integer ID untuk relational integrity
        if ($this->has('customer_ulid')) {
            $customer = Customer::where('ulid', $this->customer_ulid)->first();
            if ($customer) {
                $this->merge([
                    'customer_id' => $customer->id,
                ]);
            }
        }

        // Resolusi ULID ClothingType ke internal integer ID
        if ($this->has('clothing_type_ulid')) {
            $clothingType = ClothingType::where('ulid', $this->clothing_type_ulid)->first();
            if ($clothingType) {
                $this->merge([
                    'clothing_type_id' => $clothingType->id,
                ]);
            }
        }
    }

    public function rules(): array
    {
        return [
            'customer_ulid' => ['required', 'string', 'exists:customers,ulid'],
            'clothing_type_ulid' => ['required', 'string', 'exists:clothing_types,ulid'],
            'customer_id' => ['required', 'integer', 'exists:customers,id'],
            'clothing_type_id' => ['required', 'integer', 'exists:clothing_types,id'],
            'category' => ['required', 'string', 'max:100'],
            'measured_at' => ['required', 'date'],
            'measured_by' => ['required', 'string', 'max:255'],
            'measurement_details' => ['required', 'array'],
            'notes' => ['nullable', 'string'],
        ];
    }

    public function messages(): array
    {
        return [
            'customer_ulid.required' => 'Pelanggan wajib dipilih.',
            'customer_ulid.exists' => 'Data pelanggan tidak valid.',
            'clothing_type_ulid.required' => 'Jenis pakaian wajib dipilih.',
            'clothing_type_ulid.exists' => 'Data jenis pakaian tidak valid.',
            'category.required' => 'Kategori pakaian wajib dipilih.',
            'measured_at.required' => 'Tanggal pengukuran wajib diisi.',
            'measured_at.date' => 'Format tanggal pengukuran tidak valid.',
            'measured_by.required' => 'Kolom pengukur wajib diisi.',
            'measured_by.string' => 'Kolom pengukur harus berupa teks.',
            'measured_by.max' => 'Kolom pengukur tidak boleh lebih dari :max karakter.',
            'measurement_details.required' => 'Rincian ukuran wajib diisi.',
            'measurement_details.array' => 'Format rincian ukuran harus berupa array JSON.',
            'notes.string' => 'Catatan harus berupa teks.',
        ];
    }
}