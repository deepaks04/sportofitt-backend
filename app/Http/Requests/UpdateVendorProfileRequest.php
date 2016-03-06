<?php
namespace App\Http\Requests;

use App\Http\Requests\Request;
use Auth;
class UpdateVendorProfileRequest extends Request
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
        $rules =  [
            'fname' => 'required|alpha|min:3|max:25',
            'lname' => 'required||alpha|min:3|max:25',
            'business_name' => 'required|alpha_specialchars|min:3|max:160',
            'address' => 'required|min:10|max:255',
            'longitude' => 'required|min:3|max:20',
            'latitude' => 'required|min:3|max:20',
            'description' => 'required|min:3|max:500',
            'area_id' => 'required|integer',
            'profile_picture' => 'mimes:jpeg,png,jpg',
            'postcode' => 'required|numeric|zip',
            'commission' => 'required|integer',
            'contact' => 'required|numeric|mobile|unique:users'
        ];
        
        if(Auth::check()) {
            $userId = Auth::user()->id;
            $rules['contact'] = 'required|numeric|mobile|unique:vendors,contact,' . $userId . ',user_id';
        }        
        
        return $rules;
    }
    public function messages()
    {
        return [
            'postcode.integer' => 'Please enter valid post code',
            'area_id.integer' => 'Please select Area',
        ];
    }
}
