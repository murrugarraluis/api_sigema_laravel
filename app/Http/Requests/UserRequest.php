<?php

namespace App\Http\Requests;

use App\Models\Employee;
use App\Rules\EmployeeHasUserRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UserRequest extends FormRequest
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
            'email' => ['bail', 'required', 'email', 'unique:users,email'],
            'password' => ['bail', 'required', 'string'],
            'employee' => ['bail', 'required', 'array'],
            'employee.id' => ['bail', 'required', 'uuid', 'exists:employees,id',
                new EmployeeHasUserRule()
            ],
            'roles' => ['bail', 'required', 'array'],
            'roles.*.id' => ['bail', 'required', 'numeric', 'exists:roles,id'],
        ];
    }
}
