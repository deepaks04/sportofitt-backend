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
            'registration_no' => 'required|min:3|max:30',
            'service_tax_no' => 'required|min:5|max:30',
            'pan_no' => 'required|min:5|max:20',
            'contact_person_name' => 'required|min:5|max:30',
            'contact_person_email' => 'required|min:5|max:80|email',
            'contact_person_phone' => 'required|min:5|max:20',
            'vat' => 'required|min:1|max:2'
        ];
    }
}
