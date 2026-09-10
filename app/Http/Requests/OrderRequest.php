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
                    Rule::unique('orders', 'number')->ignore($this->id),
                ],
            'customer_id'  => ['required'],
            'order_date'  => ['required'],
            'fitting_date'  => ['nullable'],
            'due_date'  => ['required'],
            'amount_paid'  => ['nullable'],
            'payment_method'  => ['nullable'],
            'payment_date'  => ['nullable'],
            'notes'  => ['nullable'],
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
            'order_details'  => ['required', 'array', 'min:1'],
        ];
    }
}
