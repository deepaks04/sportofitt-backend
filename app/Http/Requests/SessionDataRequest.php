<?php
namespace App\Http\Requests;

use App\Http\Requests\Request;
use App\User;
use Illuminate\Support\Facades\Auth;
use App\AvailableFacility;

class SessionDataRequest extends Request
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
        switch ($this->method()) {
            case 'GET':
                $id = $this->route('id');
                $facility = AvailableFacility::find($id);
                if ($facility == null) {
                    return false;
                    break;
                } else {
                    $vendor = $user->vendor($user->id)->first();
                    $isOwner = AvailableFacility::where('id', '=', $id)->where('vendor_id', '=', $vendor->id)->count();
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
        return []
        //
        ;
    }
}
