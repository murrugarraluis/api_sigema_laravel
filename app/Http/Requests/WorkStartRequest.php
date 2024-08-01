<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class WorkStartRequest extends FormRequest
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
            "machine" => ['bail', 'required', 'array'],
            "machine.id" => ['bail', 'required', 'uuid', 'exists:machines,id'],
            "description" => ['bail', 'nullable', 'string'],
            "date" => ['bail', 'required', 'date_format:Y-m-d H:i:s'],
        ];
    }
}
