<?php

namespace App\Http\Requests;

use App\Http\Requests\Request;

class CreateVendorRequest extends Request
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
            'email' => 'required|min:5|max:255|unique:users',
            'username' => 'required|min:5|max:50|unique:users',
            'password' => 'required|min:5|max:60',
            'business_name' => 'required|min:3|max:255',
        ];
    }
}
