<?php

namespace App\Http\Requests;

class StatusRequest extends BaseRequest
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
            'content' => ['required','string','min:5'],
            'file' => !empty($this->get('file')) ? ['image','mimes:jpg,jpeg,png,gif,svg,mov,webp','max:2048'] : ['sometimes'],
            'status' => ['required','min:0','max:1']
        ];
    }
}
