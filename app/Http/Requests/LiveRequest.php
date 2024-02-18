<?php

namespace App\Http\Requests;


class LiveRequest extends BaseRequest
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
            'title' => ['required','string','min:5','max:255'],
            'teams' => ['required','string','min:5','max:255'],
            'date' => ['required','string','min:5','max:255'],
            'info' => !empty($this->get('info')) ? ['required','string','min:5','max:255'] : ['sometimes'],
            'link' => !empty($this->get('link')) ? ['required','string','min:5','max:255'] : ['sometimes'],
            'status' => ['required','min:0','max:1'],
            'priority' => ['required', 'integer', 'min:0', 'max:100'],
        ];
    }
}
