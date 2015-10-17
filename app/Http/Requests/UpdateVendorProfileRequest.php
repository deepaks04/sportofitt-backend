<?php

namespace App\Http\Requests;

use App\Http\Requests\Request;

class UpdateVendorProfileRequest extends Request
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
            'fname' => 'required|min:3|max:20',
            'lname' => 'required|min:3|max:20',
            'business_name' => 'required|min:3|max:255',
            'address' => 'required|min:10|max:255',
            'longitude' => 'required|min:3|max:20',
            'latitude' => 'required|min:3|max:20',
            'description' => 'required|min:3|max:500',
            'area_id' => 'required|integer',
            'profile_picture' => 'mimes:jpeg,png,jpg',
        ];
    }
}
