<?php

namespace App\Http\Requests;

use App\Http\Requests\Request;
use App\AvailableFacility;
use Illuminate\Support\Facades\Auth;
use App\SessionPackage;

class PackageRequest extends Request
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
            case 'GET':
                $id = $this->route('id');
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
                return false;
                break;
            case 'PUT':
                $id = $this->route('id');
                $sessionPackage = SessionPackage::find($id);
                if($sessionPackage!=null){
                    $facility = AvailableFacility::find($sessionPackage->available_facility_id);
                    if($facility==null){
                        return false;
                    }else{
                        $user = Auth::user();
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
                return [
                ];
                break;
            case 'PUT':
                return [
                    'name' => 'required|min:5|max:50',
                    'description' => 'required|min:5|max:200',
                    'is_peak' => 'required|digits_between:0,1',
                    'actual_price' => 'required',
                    'discount' => 'required|integer',
                    //'package_id' => 'required|integer',
                    'month' => 'required|integer',
                ];
                break;
            case 'POST':
                return [
                    'available_facility_id' => 'required|integer',
                    'name' => 'required|min:5|max:50',
                    'description' => 'required|min:5|max:200',
                    'is_peak' => 'required|digits_between:0,1',
                    'actual_price' => 'required',
                    'discount' => 'required|integer',
                    //'package_id' => 'required|integer',
                    'month' => 'required|integer',
                ];
                break;
            default:break;
        }
    }
}
