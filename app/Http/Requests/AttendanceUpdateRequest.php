<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class AttendanceUpdateRequest extends FormRequest
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
			'employees' => ['bail', 'nullable', 'array'],
			'employees.*.id' => ['bail', 'required', 'uuid', 'exists:employees,id'],
			'employees.*.check_in' => ['bail', 'nullable', 'date_format:H:i:s'],
			'employees.*.check_out' => ['bail', 'nullable', 'date_format:H:i:s'],
			'employees.*.attendance' => ['bail', 'required', 'boolean'],
			'employees.*.missed_reason' => ['bail', 'nullable', 'string'],
			'employees.*.missed_description' => ['bail', 'nullable', 'string'],
			'is_open' => ['bail', 'nullable', 'boolean'],
		];
	}
}
