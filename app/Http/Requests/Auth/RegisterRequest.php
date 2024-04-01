<?php

namespace App\Http\Requests\Auth;

use Illuminate\Foundation\Http\FormRequest;

class RegisterRequest extends FormRequest
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
     * @return array<string, \Illuminate\Contracts\Validation\Rule|array|string>
     */
    public function rules(): array
    {
        return [
            'first_name' => ['required', 'string', 'min:4'],
            'last_name' => ['required', 'string', 'min:4'],
            'nickname' => ['required', 'string', 'min:3', 'max:255', 'unique:users,nickname', 'regex:/^[a-zA-Z_]+$/'],
            'email' => ['required', 'string', 'email', 'unique:users,email'],
            'password' => ['required', 'string', 'min:8', 'regex:/^[a-zA-Z0-9_!@#$%^&*()=+.]+$/'],
        ];
    }
}
