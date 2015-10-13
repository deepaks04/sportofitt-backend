<?php

namespace App\Http\Requests;

use App\Http\Requests\Request;
use App\AvailableFacility;
use Illuminate\Support\Facades\Auth;

class SessionRequest extends Request
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        switch($this->method())
        {
            case 'PUT':
                return true;
                break;
            case 'GET':
                //echo 1;exit;
                return true;
                break;
            case 'POST':
                if(!empty($this->available_facility_id)){
                    $id = $this->available_facility_id;
                    $facility = AvailableFacility::find($id);
                    if($facility==null){
                        return false;
                    }else{
                        $user = Auth::user();
                        $vendor = $user->vendor($user->id)->first();
                        $isOwner = AvailableFacility::where('id','=',$id)->where('vendor_id','=',$vendor->id)->count();
                        if($isOwner){
                            return true;
                        }else{
                            return false;
                        }
                    }
                }
                return true;
                break;
            default:
                return false;
                break;
        }
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        switch($this->method())
        {
            case 'GET':
                break;
            case 'PUT':
                break;
            case 'POST':
                return [
                    'available_facility_id' => 'required|integer',
                    'is_peak' => 'required|digits_between:0,1',
                    'actual_price' => 'required',
                    'discount' => 'required|integer',
                    'session_id' => 'required|integer',
                    //'day' => 'required|integer',
                    'start' => 'required|date_format:H:i',
                    'end' => 'required|date_format:H:i',
                    //'duration' => 'required|date_format:H:i',
                ];
                break;
            default:break;
        }
    }
}
