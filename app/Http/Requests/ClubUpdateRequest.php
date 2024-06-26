<?php

namespace App\Http\Requests;

class ClubUpdateRequest extends BaseRequest
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
            'alias_id' => !empty($this->get('alias_id')) ? ['required'] : 'sometimes',
            'title' => ['required','string','max:255'],
            'alias_title' => !empty($this->get('alias_title')) ? ['required','string','min:3','max:255'] : 'sometimes',
            'country_id' => ['required','integer','exists:countries,id'],
            'sport_id' => ['required','integer','exists:sports,id'],
            'image' => !empty($this->get('image')) ? ['required','string'] : 'sometimes',
            'status' => ['required', 'integer','in:0,1']
        ];
    }
}