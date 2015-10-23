<?php
namespace App\Http\Requests;

use App\Http\Requests\Request;
use App\AvailableFacility;
use Auth;

class FacilityInfoRequest extends Request
{

    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        $user = Auth::user();
        $vendor = $user->vendor->first();
        $isOwner = AvailableFacility::where(array(
            'id' => $this->id,
            'vendor_id' => $vendor->id
        ))->count();
        if ($isOwner) {
            return true;
        } else {
            return false;
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
