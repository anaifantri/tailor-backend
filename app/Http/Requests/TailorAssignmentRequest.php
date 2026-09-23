<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Encryption\DecryptException;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Crypt;

class TailorAssignmentRequest extends FormRequest
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
            'order_detail_id'   => ['required'],
            'tailor_id'         => ['nullable'],
            'quantity_assigned' => ['required', 'integer', 'min:1'],
            'assignment_date'   => ['required', 'date'],
            'labor_cost'        => ['required', 'numeric', 'min:0'],
            'total_labor_cost'  => ['required', 'numeric', 'min:0'],
            'status'            => ['nullable', 'string', 'in:assigned,in_progress,completed,cancelled'],
            'notes'             => ['nullable', 'string', 'max:500'],
        ];
    }

    public function messages(): array
    {
        return [
            'order_detail_id.required'   => 'Rincian order wajib dipilih.',
            'quantity_assigned.required' => 'Jumlah item yang ditugaskan wajib diisi.',
            'quantity_assigned.integer'  => 'Jumlah item harus berupa angka bulat.',
            'quantity_assigned.min'      => 'Jumlah item minimal :min.',
            'assignment_date.required'   => 'Tanggal penugasan wajib diisi.',
            'assignment_date.date'       => 'Format tanggal penugasan tidak valid.',
            'labor_cost.required'        => 'Biaya jahit per unit wajib diisi.',
            'labor_cost.numeric'         => 'Biaya jahit harus berupa angka.',
            'labor_cost.min'             => 'Biaya jahit tidak boleh bernilai negatif.',
            'total_labor_cost.required'  => 'Total biaya jahit wajib diisi.',
            'total_labor_cost.numeric'   => 'Total biaya jahit harus berupa angka.',
            'total_labor_cost.min'       => 'Total biaya jahit tidak boleh bernilai negatif.',
            'status.in'                  => 'Status penugasan tidak valid.',
            'notes.max'                  => 'Catatan maksimal :max karakter.',
        ];
    }
}
