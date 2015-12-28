<?php
namespace App\Http\Requests;

use App\Http\Requests\Request;

class ChangePasswordRequest extends Request
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
            'old' => 'required|min:6|max:12',
            'new' => 'required|min:6|max:12|password_custom',
            'confirm' => 'required|min:6|max:12|same:new'
        ];
    }
}
