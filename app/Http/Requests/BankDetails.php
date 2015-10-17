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
            'bank_name' => 'required|min:3|max:80',
            'ifsc' => 'required|min:5|max:30',
            'account_type' => 'required|min:5|max:255',
            'branch_name' => 'required|min:5|max:50',
            'beneficiary' => 'required|min:5|max:50',
            'account_number' => 'required|integer|min:10',
        ];
    }
}
