<?php

namespace App\Http\Requests;

class SendNotificationRequest extends BaseRequest
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
            'users' => ['max:255'],
            'roles' => ['max:255'],
            'message' => ['required' ,'max:255'],
            'link' => ['max:255'],
            'has_email' => ['required' ,'in:0,1'],
            'has_modal' => ['required' ,'in:0,1'],
        ];
    }
}
