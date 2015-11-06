<?php

namespace App\Http\Requests;

use App\Http\Requests\Request;
use App\OpeningHour;
use App\AvailableFacility;
use App\SessionPackage;
use App\User;
use Auth;

class DeleteOpeningTimeRequest extends Request
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
        $id = $this->route('id');//OpeningHoursID
        $openingHour = OpeningHour::find($id);
        if($openingHour!=null){
            $sessionPackage = SessionPackage::find($openingHour->session_package_id);
            $facility = AvailableFacility::find($sessionPackage->available_facility_id);
            if($facility==null){
                return false;
            }else{
                $vendor = $user->vendor($user->id)->first();
                $isOwner = AvailableFacility::where('id','=',$sessionPackage->available_facility_id)->where('vendor_id','=',$vendor->id)->count();

                if($isOwner){
                    return true;
                }else{
                    return false;
                }
            }
        }
        return false;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            //
        ];
    }
}
