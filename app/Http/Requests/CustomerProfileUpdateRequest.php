<?php

namespace App\Http\Requests;

use App\Http\Requests\Request;

class CustomerProfileUpdateRequest extends Request
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
            'fname' => 'required|max:255|alpha',
            'lname' => 'required|max:255|alpha',
            'profile_picture' => 'mimes:jpeg,png,jpg',
            'birthdate' => 'date_format:d-m-Y',
            'area_id' => 'required',
            'pincode' => 'required|min:6|max:6'
        ];
    }
    
    /**
     * Getting error message for the error according to the rules.
     * 
     * @return array
     */
    public function messages()
    {
        return [
            'fname.required' => 'First name must not be blank',
            'fname.max' => 'First name must not be greater than 255 characters',
            'fname.aplha' => 'Only letters are allowed',
            'lname.required' => 'Last name must not be blank',
            'lname.max' => 'Last name must not be greater than 255 characters',
            'lname.aplha' => 'Only letters are allowed',
            'profile_picture.mimes' => 'Images with type jpeg,jpg,png are allowed',
            'birthdate.date_format' => 'Enter brith date in format of d-m-Y',
            'area_id.required' => 'Please select your area',
            'pincode.required' => 'Pincode must not be blank',
            'pincode.min' => 'Pincode must be minimum 6 character long',
            'pincode.max' => 'Pincode must not be greater than 6 character'
        ];
    }

}
