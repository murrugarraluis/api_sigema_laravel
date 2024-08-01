<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class AttendanceUpdateCheckOutRequest extends FormRequest
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
				'employees' => ['bail', 'required', 'array'],
				'employees.*.id' => ['bail', 'required', 'uuid', 'exists:employees,id'],
				'employees.*.check_out' => ['bail', 'required', 'date_format:Y-m-d H:i:s'],
			];
    }
}
