<?php

namespace App\Http\Requests;


class StatusUpdateRequest extends BaseRequest
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
            'text' => ['required','string','min:5'],
            'file' => !empty($this->get('file')) ? ['string'] : ['sometimes'],
            'status' => ['required','min:0','max:1']
        ];
    }
}
