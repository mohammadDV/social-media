<?php

namespace App\Http\Requests;

use App\Rules\Recaptcha;

class AdvertiseFormRequest extends BaseRequest
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
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array|string>
     */
    public function rules(): array
    {
        return [
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'phone' => 'required|string|max:11|min:11',
            'content' => 'max:1000',
            'token' => [new Recaptcha],
        ];
    }
}