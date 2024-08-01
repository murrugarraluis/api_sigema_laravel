<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class MachineUpdateRequest extends FormRequest
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
            'name' => ['bail', 'required', 'string'],
            'brand' => ['bail', 'required', 'string'],
            'model' => ['bail', 'required', 'string'],
            'image' => ['bail', 'nullable', 'string'],
            'maximum_working_time' => ['bail', 'required', 'integer'],
            'maximum_working_time_per_day' => ['bail', 'required', 'integer'],
						'recommendation' => ['bail','nullable','string'],
            'articles' => ['bail', 'nullable', 'array'],
            'articles.*.id' => ['bail', 'required', 'uuid', 'exists:articles,id'],
            'status' => ['bail', 'nullable', 'string'],
        ];
    }
}
