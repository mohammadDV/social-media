<?php

namespace App\Http\Requests;

class FileRequest extends BaseRequest
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
            // 'image' => !empty($this->get('image')) ? ['required','image','mimes:jpg,jpeg,png,gif,svg','max:2048'] : ['sometimes'],
            'image' => !empty($this->get('image')) ? ['required','image','mimes:jpg,jpeg,png,gif,svg','max:2048'] : ['sometimes'],
            'video' => !empty($this->get('video')) ? 'required|mimes:mp4,ogx,oga,ogv,ogg,webm|max:102400' : 'sometimes',
        ];
    }
}
