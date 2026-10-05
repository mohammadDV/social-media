<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;

class AdvertiseRequest extends BaseRequest
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
            'title' => 'required|string:min:5|max:255',
            'place_id' => 'required|integer|min:0|max:100',
            // 'link' => 'required|string|max:255',
            'status' => 'required|integer|in:0,1',
            'image' => ['required', 'string'],
        ];
    }
}
