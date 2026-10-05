<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;

class ReportRequest extends BaseRequest
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
            'id' => ['required', 'integer'],
            'type' => ['required', 'string', 'in:status,comment,user'],
            'message' => ['required', 'string', 'min:10', 'max:1000'],
        ];
    }
}
