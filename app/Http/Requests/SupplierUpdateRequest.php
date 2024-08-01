<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class SupplierUpdateRequest extends FormRequest
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
            'phone' => ['bail', 'required', 'string', 'min:9'],
            'email' => ['bail', 'required', 'email'],
            'address' => ['bail', 'required', 'string'],
            'supplier_type' => ['bail', 'required', 'array'],
            'supplier_type.id' => ['bail', 'required', 'uuid', 'exists:supplier_types,id'],
            'document_type' => ['bail', 'required', 'array'],
            'document_type.id' => ['bail', 'required', 'uuid', 'exists:document_types,id'],
            'banks' => ['bail', 'nullable', 'array'],
            'banks.*.id' => ['bail', 'required', 'uuid', 'exists:banks,id'],
            'banks.*.account_number' => ['bail', 'required', 'string'],
            'banks.*.interbank_account_number' => ['bail', 'required', 'string'],

        ];
    }
}
