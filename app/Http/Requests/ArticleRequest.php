<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ArticleRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules(): array
    {
        return [
            'serie_number' => ['bail', 'required', 'string'],
            "name" => ['bail','required', 'string'],
            "brand" => ['bail','required', 'string'],
            "model" => ['bail','required', 'string'],
            "quantity" => ['bail','required', 'numeric', 'min:0'],
            'image' => ['bail','nullable','string'],
            'technical_sheet' => ['bail','nullable','string'],
            "article_type" => ['bail','required', 'array'],
            "article_type.id" => ['bail','required', 'uuid', 'exists:article_types,id'],
            'suppliers' => ['bail','nullable', 'array'],
            'suppliers.*.id' => ['bail','required', 'uuid', 'exists:suppliers,id'],
            'suppliers.*.price' => ['bail','required', 'numeric', 'min:0'],
        ];
    }
}
