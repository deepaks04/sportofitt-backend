<?php

namespace App\Http\Requests;

use App\Http\Requests\Request;

class CheckAvailabilityRequest extends Request
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
            'facility_id' => 'required',
            'time_slot' => 'required',
            'booking_date' => 'required',
        ];
    }
}
