<?php

namespace App\Http\Requests;

use App\Http\Requests\Request;
use App\AvailableFacility;
use App\SessionBooking;
use App\User;
use Illuminate\Support\Facades\Auth;
class UpdateBlockCalender extends Request
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
                $bookedBlock = SessionBooking::find($id);
                if($bookedBlock==null){
                    return false;
                }else{
                     $isOwner = SessionBooking::where('id','=',$id)->where('user_id','=',$user['id'])->count();
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
                    'startsAt' => 'required|date'
                ];
                break;
            case 'POST':
                return [
                    ];
                break;
            default:break;
        }
    }
}
