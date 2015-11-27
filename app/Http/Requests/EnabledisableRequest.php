<?php

namespace App\Http\Requests;

use App\Http\Requests\Request;
use App\AvailableFacility;
use Auth;

class EnabledisableRequest extends Request
{


    public function authorize()
    {

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


    public function rules()
    {
        switch($this->method())
        {
            case 'GET':
                return [
                ];
                break;
            case 'PUT':
               return[];
                break;
            case 'POST':
                return [];

                break;
            default:break;
        }
    }

}
