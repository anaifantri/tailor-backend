<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UserRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'name' => 'required|string|max:255',
            'username' => 'required|string|min:6|unique:users,username,' . $this->route('id'),
            'email' => 'required|email:dns|unique:users,email,' . $this->route('id'),
            'phone' => 'required|unique:users,phone,' . $this->route('id'),
            'photo' => 'image|mimes:jpeg,jpg,png|max:1024|nullable',
            'is_active' => 'required|boolean',
        ];
    }
}
