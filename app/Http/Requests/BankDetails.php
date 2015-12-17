<?php
namespace App\Http\Requests;

use App\Http\Requests\Request;

class BankDetails extends Request
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
            'bank_name' => 'required|alpha_spaces|min:3|max:80',
            'ifsc' => 'required|alpha_num|min:5|max:30',
            'account_type' => 'required|min:5|max:255',
            'branch_name' => 'required|alpha_spaces|min:5|max:50',
            'beneficiary' => 'required|alpha_spaces|min:5|max:50',
            'account_number' => 'required|numeric|min:20'
        ];
    }
    public function messages()
    {
        return [
            'bank_name.required' => 'Bank Name is required',
            'account_type.required' => 'Account Number is required',
            'ifsc.required' => 'IFSC Code is required',
            'account_type.required' => 'Please select the Account Type',
            'beneficiary.required' => 'Beneficiary Name is required',
        ];
    }
}
