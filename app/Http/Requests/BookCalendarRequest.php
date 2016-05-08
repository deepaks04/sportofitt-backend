<?php

namespace App\Http\Requests;

use App\AvailableFacility;
use App\Http\Requests\Request;
use App\SessionBooking;
use App\User;
use Illuminate\Support\Facades\Auth;

class BookCalendarRequest extends Request
{

    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        $data = $this->request->all();
        if ($this->route('uid') == null) {
            $user = Auth::user();
        } else {
            $user = User::find($this->route('uid'));
        }
        switch ($this->method()) {
            case 'PUT':
                $id = $this->route('id');
                $facility = AvailableFacility::find($id);
                if ($facility == null) {
                    return false;
                } else {
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

        return [
            'facility_id' => 'required',
            'date' => 'required',
            'is_peak' => 'required',
            'slot_timing' => 'required'
        ];
    }

    public function messages()
    {
        return [
            'facility_id.required' => 'Please select a category',
            'date.required' => 'Please select date for event',
            'is_peak.required' => 'Please select the is it peak or not',
            'slot_timing.required' => 'Please select a slot timing'
        ];
    }

}