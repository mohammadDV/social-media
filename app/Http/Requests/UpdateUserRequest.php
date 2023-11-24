<?php

namespace App\Http\Requests;

class UpdateUserRequest extends BaseRequest
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
            'nickname' => !empty($this->get('nickname')) ? ['required', 'string', 'min:3', 'max:25'] : 'sometimes',
            'biography' => !empty($this->get('biography')) ? ['required', 'string', 'min:5', 'max:255'] : 'sometimes',
            'profile_photo_path' => !empty($this->get('profile_photo_path')) ? ['required', 'string'] : 'sometimes',
            'bg_photo_path' => !empty($this->get('bg_photo_path')) ? ['required', 'string'] : 'sometimes',
            'mobile' => !empty($this->get('mobile')) ? ['required', 'string', 'min:11', 'max:15'] : 'sometimes',
            'status' => ['required','in:0,1']
        ];
    }
}
