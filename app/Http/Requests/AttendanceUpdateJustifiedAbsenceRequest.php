<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class AttendanceUpdateJustifiedAbsenceRequest extends FormRequest
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
			'employees.*.missed_reason' => ['bail', 'required', 'string'],
			'employees.*.missed_description' => ['bail', 'required', 'string'],
		];
	}
}
