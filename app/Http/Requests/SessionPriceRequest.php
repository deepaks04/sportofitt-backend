<?php

namespace App\Http\Requests;

use App\AvailableFacility;
use App\Http\Requests\Request;
use App\User;
use Illuminate\Support\Facades\Auth;

class SessionPriceRequest extends Request
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
                return true;
                break;
            case 'GET':
                $facility_id = $this->route('id');
                $session = AvailableFacility::find($facility_id);
                if ($session != null) {
                    $vendor = $user->vendor($user->id)->first();
                    $isOwner = AvailableFacility::where('id', '=', $session->id)->where('vendor_id', '=', $vendor->id)->count();
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
        return [
            //
        ];
    }
}
