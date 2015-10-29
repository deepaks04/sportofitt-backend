<?php
namespace App\Http\Requests;

use App\Http\Requests\Request;
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
        switch ($this->method()) {
            case 'GET':
                $id = $this->route('id');
                $facility = AvailableFacility::find($id);
                if ($facility == null) {
                    return false;
                    break;
                } else {
                    $user = Auth::user();
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
