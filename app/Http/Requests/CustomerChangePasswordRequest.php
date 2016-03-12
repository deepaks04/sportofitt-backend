<?php

namespace App\Http\Requests;

use App\Http\Requests\Request;

class CustomerChangePasswordRequest extends Request
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
            'current_password' => 'required|min:6|regex:[^\S*(?=\S{6,})(?=\S*[a-zA-Z\d\W])\S*$]',
            'password' => 'required|confirmed|min:6|regex:[^\S*(?=\S{6,})(?=\S*[a-zA-Z\d\W])\S*$]'
        ];
    }

}