<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class AttendancePDFRequest extends FormRequest
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
			"start_date" => ['bail', 'required', 'date_format:Y-m-d'],
			"end_date" => ['bail', 'required', 'date_format:Y-m-d'],
			'sort_by' => ['bail', 'required', 'string', "in:lastname,name,attendances,absences"],
//			'sort_by' => ['bail', 'required', 'string'],

			'order_by' => ['bail', 'required', 'string', "in:asc,desc"],
			"type" => ['bail', 'required', "in:attended,missed"],
		];
	}
}
