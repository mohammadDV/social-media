<?php

namespace App\Http\Requests;

class UserRequest extends BaseRequest
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
            'first_name' => ['required','string','min:2','max:255'],
            'last_name' => ['required','string','max:255'],
            'email' => ['required','max:255','email','string'],
            'password' => ['required','min:6','max:255','string'],
            'nickname' => !empty($this->get('nickname')) ? ['required', 'string', 'min:3', 'max:25'] : 'sometimes',
            'biography' => !empty($this->get('biography')) ? ['required', 'string', 'min:5', 'max:255'] : 'sometimes',
            'profile_photo_path' => !empty($this->get('profile_photo_path')) ? ['required', 'image','mimes:jpg,jpeg,png,gif,svg','max:2048'] : 'sometimes',
            'bg_photo_path' => !empty($this->get('bg_photo_path')) ? ['image','mimes:jpg,jpeg,png,gif,svg','max:2048'] : 'sometimes',
            'mobile' => !empty($this->get('mobile')) ? ['required', 'string', 'min:11', 'max:12'] : 'sometimes',
            'status' => ['required','in:0,1']
        ];
    }
}
