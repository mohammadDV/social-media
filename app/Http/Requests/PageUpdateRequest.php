<?php

namespace App\Http\Requests;


class PageUpdateRequest extends BaseRequest
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
            'title' => 'required|string:min:5|max:255',
            'content' => 'required|string',
            'status' => 'required|integer|in:0,1',
            'priority' => 'required|integer|min:0|max:100',
            'image' => !empty($this->get('image')) ? ['required','image','mimes:jpg,jpeg,png,gif,svg','max:2048'] : 'sometimes',
        ];
    }
}
