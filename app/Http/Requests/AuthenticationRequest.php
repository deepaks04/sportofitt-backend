<?php

namespace App\Http\Requests;

use App\Http\Requests\Request;

class AuthenticationRequest extends Request
{

    protected $rules = [
        'first_name' => 'required|alpha|max:255',
        'last_name' => 'required|alpha|max:255',
        'email' => 'required|email|max:255|unique:users',
        'password' => 'required|confirmed|min:6|regex:[^\S*(?=\S{6,})(?=\S*[a-zA-Z\d\W])\S*$]'
    ];

    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return $this->rules;
    }

    public function setRules($rules = array())
    {
        $this->rules = $rules;
    }

    public function messages()
    {
        return [
            'first_name.required' => 'First name must not be blank',
            'first_name.alpha' => 'First name must include only letters',
            'last_name.alpha' => 'Last name must include only letters',
            'last_name.required' => 'Last name must not be blank',
            'email.required' => 'Email  must not be blank',
            'email.email' => 'Enter valid email address',
            'email.unique' => 'Email address has been already taken',
            'password.required' => 'Password must not be blank',
            'password.min' => 'Password must be atleast 6 charcter long',
            'confirmation_password' => 'Password does not match',
            'password.regex' => 'Password format not matching',
        ];
    }

}