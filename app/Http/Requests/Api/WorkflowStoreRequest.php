<?php

namespace App\Http\Requests\Api;

use Illuminate\Foundation\Http\FormRequest;

class WorkflowStoreRequest extends FormRequest
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
            'name' => 'required|string|max:255',
            'type' => 'required|string|in:leave,mission,other',
            'department_id' => 'required|exists:departments,id',
            'approval_steps' => 'required|json',
            'conditions' => 'nullable|json',
            'is_active' => 'boolean',
        ];
    }
}
