<?php

namespace App\Http\Requests;


class MatchRequest extends BaseRequest
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
            'home_id' => 'required|integer|exists:clubs,id',
            'away_id' => 'required|integer|exists:clubs,id',
            'hsc' => 'required|string',
            'asc' => 'required|string',
            'link' => !empty($this->get('link')) ?  'required|string|max:255' : 'sometimes',
            'date' => 'required|string|max:100',
            'hour' => 'required|string|max:50',
            'priority' => 'required|integer|min:0|max:100'
        ];
    }
}
