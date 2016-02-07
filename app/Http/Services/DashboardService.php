<?php

namespace App\Http\Services;

use App\City;
use App\Http\Services\BaseService;
use App\Http\Helpers\APIResponse;

class DashboardService extends BaseService
{

    public function getUserProfile()
    {
        $areaDetails = $cityDetails = $customer = array();
        try {
            $user = $this->getAuthenticatedUser();
            $city = City::select('id', 'name')->where('name', '=', 'pune')->first();
            if (!empty($city) && $city->id > 0) {
                $areas = $city->areas()->select('id','name','city_id')->get();
                $cityDetails = $city->toArray();
                $areaDetails = $areas->toArray();
            }
            
            if ($user->profile_picture != null) {
                $vendorUploadPath = URL::asset(env('CUSTOMER_FILE_UPLOAD'));
                $vendorOwnDirecory = $vendorUploadPath . "/" . sha1($user->id) . "/" . "profile_image/";
                $user['profile_picture'] = $vendorOwnDirecory . $user->profile_picture;
            }
            
            $userMeta = $user->customer()->select('pincode','area_id','phone_no','gender','birthdate')->get();
            if(!empty($userMeta)) {
                $customer =$userMeta->toArray();
            }
            
            return [
                'user' => $user->toArray(),
                'cities' => $cityDetails,
                'areas' => $areaDetails,
                'customer' => $customer
                
            ];
        } catch (Exception $exception) {
            APIResponse::handelException($exception);
        }
        
        return null;
    }
}