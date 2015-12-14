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
            'fname' => 'required|alpha_spaces|min:3|max:25',
            'lname' => 'required||alpha_spaces|min:3|max:25',
            'email' => 'required|email|min:5|max:100|unique:users',
            'username' => 'required|min:5|max:25|unique:users',
            'password' => 'required|alpha_num|min:6|max:12',
            'business_name' => 'required|alpha_specialchars|min:3|max:160'
        ];
    }
}
