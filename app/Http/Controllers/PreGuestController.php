<?php
namespace App\Http\Controllers;
use Input;
use App\PreGuestUser;
use Carbon\Carbon;
class PreGuestController extends Controller
{
    public function saveGuestUser()
    {
        $data = Input::all();
        $preUser = new PreGuestUser();
        $preUser->full_name = $data['name'];
        $preUser->email = $data['email'];
        $preUser->phone = $data['phone'];
        $preUser->created_at = Carbon::now();
        $preUser->updated_at = Carbon::now();
        $response = [
            "success" => $preUser->save()
        ];
        return $response;
    }
}

