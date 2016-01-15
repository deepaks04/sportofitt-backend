<?php
namespace App\Http\Requests;

use App\Http\Requests\Request;
use App\AvailableFacility;
use Auth;

class AddFacilityRequest extends Request
{

    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        if(!isset($this->uid) && $this->uid==null){
            switch ($this->method()) {
                case 'PUT':
                    $id = $this->route('id');
                    $facility = AvailableFacility::find($id);
                    if ($facility == null) {
                        return false;
                    } else {
                        $user = Auth::user();
                        $vendor = $user->vendor($user->id)->first();
                        $isOwner = AvailableFacility::where('id', '=', $id)->where('vendor_id', '=', $vendor->id)->count();
                        if ($isOwner) {
                            return true;
                        } else {
                            return false;
                        }
                    }
                    break;
                case 'GET':
                    return true;
                    break;
                case 'POST':
                    return true;
                    break;
                default:
                    return false;
                    break;
            }
        }
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        switch ($this->method()) {
            case 'PUT':
                return [
                    'name' => 'required|alpha_spaces|min:5|max:50',
                    'is_active' => 'required|integer',
                    'slots' => 'required|integer',
                    'sub_category_id' => 'required|integer',
                    'description' => 'min:5|max:500',
                    'duration' => 'required|integer',
                    'cancellation_before_24hrs' => 'required|numeric|between:0,100',
                    'cancellation_after_24hrs' => 'required|numeric|between:0,100',
                    'off_peak_hour_price' => 'required|numeric|digits_between:0,5',
                    'peak_hour_price' => 'required|numeric|digits_between:0,5'
                ];
                break;
            case 'POST':
                return [
                    'name' => 'required|alpha_spaces|min:5|max:50',
                    'sub_category_id' => 'required|integer',
                    'slots' => 'required|integer',
                    'description' => 'min:5|max:500',
                    'duration' => 'required|integer',
                    'cancellation_before_24hrs' => 'required|numeric|between:0,100',
                    'cancellation_after_24hrs' => 'required|numeric|between:0,100',
                    'off_peak_hour_price' => 'required|numeric|digits_between:0,5',
                    'peak_hour_price' => 'required|numeric|digits_between:0,5'
                ];
                break;
            default:
                break;
        }
    }
}
