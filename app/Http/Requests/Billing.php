<?php
namespace App\Http\Requests;

use App\Http\Requests\Request;

class Billing extends Request
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
            'company_title' => 'required|min:3|max:50',
            'contact_person_name' => 'required|min:5|max:30',
            'contact_person_email' => 'required|min:5|max:80|email',
            'contact_person_phone' => 'required|min:5|max:20'

        ];
    }
}
