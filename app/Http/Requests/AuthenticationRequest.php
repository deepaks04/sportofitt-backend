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
}