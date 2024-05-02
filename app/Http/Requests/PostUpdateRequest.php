<?php

namespace App\Http\Requests;


class PostUpdateRequest extends BaseRequest
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
            'pre_title' => ['max:255'],
            'category_id' => ['required','integer','exists:categories,id'],
            'summary' => ['max:255'],
            'content' => ['required','string','min:5'],
            'tags' => ['max:255'],
            'image' => !empty($this->get('image')) ? ['required','string'] : ['sometimes'],
            'type' => ['integer','min:0','max:1'],
            'video' =>  $this->get('type') == 1 && !empty($this->get('video')) ? 'required|string' : 'sometimes',
            'status' => ['required','min:0','max:1'],
            'special' => ['required','min:0','max:1']
        ];
    }
}
