<?php

namespace App\Http\Requests\Api;

use Illuminate\Foundation\Http\FormRequest;

class DepartmentUpdateRequest extends FormRequest
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
            'name' => 'sometimes|string|max:255|unique:departments',
            'code' => 'sometimes|string|max:50|unique:departments',
            'description' => 'nullable|string|max:1000',
            'manager_id' => 'nullable|exists:users,id',
            'is_active' => 'boolean',
        ];
    }
}
