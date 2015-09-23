<?php

namespace App\Http\Requests;

use App\Http\Requests\Request;

class CreateCustomerRequest extends Request
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
            'email' => 'required|email|min:5|max:255|unique:users',
            'username' => 'required|min:5|max:50|unique:users',
            'password' => 'required|min:5|max:60',
            'gender' => 'required|integer',
            'area_id' => 'required|integer',
        ];
    }
}
