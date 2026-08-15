<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Encryption\DecryptException;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Validation\Rule;

class TailorRequest extends FormRequest
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
            // 'code' => [
            //     'required',
            //     'string',
            //     Rule::unique('tailors', 'code')->ignore($this->id), 
            // ],
            'name'  => ['required', 'string', 'max:255'],
            'is_active'  => ['required'],
            'email' => [
                'nullable',
                'email:rfc,dns',
                Rule::unique('tailors', 'email')->ignore($this->id),
                ],
            'phone'  => [
                    'required',
                    Rule::unique('tailors', 'phone')->ignore($this->id),
                ],
            'address' => [
                    'nullable',
                    ],
            'specialty' => [
                    'nullable'
                    ],
            'photo' => [
                    'nullable',
                    'image',
                    'mimes:jpeg,jpg,png',
                    'max:1024',
                    ],
        ];
    }
}
