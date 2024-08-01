<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class MaintenanceSheetStoreRequest extends FormRequest
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
            "date" => ['bail', 'required', 'date_format:Y-m-d H:i:s'],
            "responsible" => ['bail', 'required', 'string'],
            "technical" => ['bail', 'required', 'string'],
            "description" => ['bail', 'nullable', 'string'],
            'maintenance_type' => ['bail', 'nullable', 'array'],
            'maintenance_type.id' => ['bail', 'required', 'uuid', 'exists:maintenance_types,id'],
            'supplier' => ['bail', 'nullable', 'array'],
            'supplier.id' => ['bail', 'required', 'uuid', 'exists:suppliers,id'],
            'machine' => ['bail', 'nullable', 'array'],
            'machine.id' => ['bail', 'required', 'uuid', 'exists:machines,id'],
            'maximum_working_time' => ['bail', 'required', 'integer'],
            "ref_invoice_number" => ['bail', 'required', 'string'],

            "detail" => ['bail', 'required', 'array'],
            "detail.*.article" => ['bail', 'nullable', 'array'],
            "detail.*.article.id" => ['bail', 'nullable', 'uuid', 'exists:articles,id'],
            "detail.*.description" => ['bail', 'nullable', 'string'],
            "detail.*.price" => ['bail', 'required', 'numeric'],
            "detail.*.quantity" => ['bail', 'required', 'numeric'],
						'recommendation' => ['bail','nullable','string'],

				];
    }
}
