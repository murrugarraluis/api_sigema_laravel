<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class EmployeeRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'document_number' => ['bail', 'required', 'string', 'min:8'],
            'name' => ['bail', 'required', 'string'],
            'lastname' => ['bail', 'required', 'string'],
            'personal_email' => ['bail', 'required', 'email'],
            'phone' => ['bail', 'required', 'string', 'min:9'],
            'address' => ['bail', 'required', 'string'],
            'position' => ['bail', 'required', 'array'],
            'position.id' => ['bail', 'required', 'uuid', 'exists:positions,id'],
            'document_type' => ['bail', 'required', 'array'],
            'document_type.id' => ['bail', 'required', 'uuid', 'exists:document_types,id'],
            'type' => ['bail', 'required', 'string', "in:permanent,relay"],
            'turn' => ['bail', 'required', 'string', "in:day,night"],
            'native_language' => ['bail', 'required', 'string',"in:spanish,english"],
        ];
    }
}
