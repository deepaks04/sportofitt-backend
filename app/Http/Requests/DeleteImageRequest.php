<?php

namespace App\Http\Requests;

use App\Http\Requests\Request;
use App\VendorImages;
use Illuminate\Support\Facades\Auth;
use App\User;

class DeleteImageRequest extends Request
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        $id = $this->route('id');
        $image = VendorImages::find($id);
        if($image==null){
            return false;
        }else{
            $user = Auth::user();
            $vendor = $user->vendor($user->id)->first();
            $isOwner = VendorImages::where('id','=',$id)->where('vendor_id','=',$vendor->id)->count();
            if($isOwner){
                return true;
            }else{
                return false;
            }
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
