<?php

namespace App\Http\Requests;

use App\AvailableFacility;
use App\Http\Requests\Request;
use App\SessionBooking;
use App\User;
use Illuminate\Support\Facades\Auth;

class BlockCalendarRequest extends Request
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        $data = $this->request->all();
        if($this->route('uid')==null){
            $user = Auth::user();
        }else{
            $user = User::find($this->route('uid'));
        }
        switch($this->method())
        {
            case 'PUT':
                $id = $this->route('id');
                $facility = AvailableFacility::find($id);
                if($facility==null){
                    return false;
                }else{
                    $vendor = $user->vendor($user->id)->first();
                    $isOwner = AvailableFacility::where('id','=',$id)->where('vendor_id','=',$vendor->id)->count();
                    if($isOwner){
                        return true;
                    }else{
                        return false;
                    }
                }
                break;
            case 'GET':
                $id = $this->route('id');
                $session = SessionBooking::find($id);
                if ($session != null) {
                    $vendor = $user->vendor($user->id)->first();
                    $isOwner = SessionBooking::where('id', '=', $session->id)->where('user_id', '=', $vendor->user_id)->count();
                    if ($isOwner) {
                        return true;
                        break;
                    } else {
                        return false;
                        break;
                    }
                }
                return false;
                break;
            case 'POST':
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
                return [
                ];
                break;
            case 'PUT':
                return [
                    'name' => 'required|min:5|max:50',
                    //'image' => 'mimes:jpeg,png,jpg',
                    'is_active' => 'required|integer',
                    'slots' => 'required|integer',
                    'sub_category_id' => 'required|integer',
                    'description' => 'min:5|max:500',
                    'duration' => 'required|integer',
                    'cancellation_before_24hrs' => 'required|integer',
                    'cancellation_after_24hrs' => 'required|integer',
                ];
                break;
            case 'POST':
                return [
                    'available_facility_id' => 'required|integer',
                    //'date' => 'required|date_format:Y-m-d H:i:s',
                    'startsAt' => 'required|date',
                    //'end' => 'required|date_format:H:i',
                ];
                break;
            default:break;
        }
    }
}
