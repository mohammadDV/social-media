<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;

class SearchClubRequest extends BaseRequest
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
     * @return array<string, ValidationRule|array|string>
     */
    public function rules(): array
    {
        return [
            'sport_id' => 'required|integer|exists:sports,id',
            'country_id' => ! empty($this->get('country_id')) ? 'required|integer|exists:countries,id' : 'sometimes',
            'search' => ! empty($this->get('search')) ? 'required|string' : 'sometimes',
        ];
    }
}
