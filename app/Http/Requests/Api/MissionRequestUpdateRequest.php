<?php

namespace App\Http\Requests\Api;

use Illuminate\Foundation\Http\FormRequest;

class MissionRequestUpdateRequest extends FormRequest
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
            'destination' => 'sometimes|string|max:255',
            'purpose' => 'sometimes|string|max:1000',
            'estimated_cost' => 'nullable|numeric|min:0',
            'transportation_mode' => 'nullable|string|max:255',
            'accommodation_details' => 'nullable|string|max:1000',
            'start_date' => 'sometimes|date',
            'end_date' => 'sometimes|date|after_or_equal:start_date',
            'reason' => 'sometimes|string|max:1000',
        ];
    }
}
