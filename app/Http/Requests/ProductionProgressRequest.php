<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Encryption\DecryptException;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Crypt;

class ProductionProgressRequest extends FormRequest
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
            'order_detail_id'  => ['required'],
            'progress_date'  => ['required'],
            'status'  => ['required', 'string'],
            'notes'  => ['string', 'nullable'],
        ];
    }
}
