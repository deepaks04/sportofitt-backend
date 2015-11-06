<?php
namespace App\Http\Requests;

use App\Http\Requests\Request;
use App\User;
use Illuminate\Support\Facades\Auth;
use App\MultipleSession;
use App\AvailableFacility;

class MultipleSessionRequest extends Request
{

    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        if($this->route('uid')==null){
            $user = Auth::user();
        }else{
            $user = User::find($this->route('uid'));
        }
        $data = $this->request->all();
        switch ($this->method()) {
            case 'PUT':
                $id = $this->route('id');
                $session = MultipleSession::find($id);
                if ($session != null) {
                    $vendor = $user->vendor($user->id)->first();
                    $isOwner = AvailableFacility::where('id', '=', $session->available_facility_id)->where('vendor_id', '=', $vendor->id)->count();
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
            case 'GET':
                $id = $this->route('id');
                $session = MultipleSession::find($id);
                if ($session != null) {
                    $vendor = $user->vendor($user->id)->first();
                    $isOwner = AvailableFacility::where('id', '=', $session->available_facility_id)->where('vendor_id', '=', $vendor->id)->count();
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
                if (!empty($data['available_facility_id'])) {
                    $id = $data['available_facility_id'];
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
                }
                // return false;
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
        switch ($this->method()) {
            case 'GET':
                return [];
                break;
            case 'PUT':
                return [
                    'peak' => 'required|integer',
                    'off_peak' => 'required|integer',
                    'price' => 'required',
                    'discount' => 'required|integer'
                ];
                break;
            case 'POST':
                return [
                    'available_facility_id' => 'required|integer',
                    'peak' => 'required|integer',
                    'off_peak' => 'required|integer',
                    'price' => 'required',
                    'discount' => 'required|integer'
                ];
                break;
            default:
                break;
        }
    }
}
