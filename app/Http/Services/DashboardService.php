<?php

namespace App\Http\Services;

use URL;
use File;
use App\City;
use App\User;
use App\Customer;
use App\Http\Services\BaseService;
use App\Http\Helpers\APIResponse;
use App\Http\Helpers\FileHelper;

class DashboardService extends BaseService
{

    /**
     * Getting users profile inforamtion of authenticated user based on
     * the token
     * 
     * @return array
     */
    public function getUserProfile()
    {
        $areaDetails = $cityDetails = $customer = array();
        try {
            $user = $this->getAuthenticatedUser();
            $city = City::select('id', 'name')->where('name', '=', 'pune')->first();
            if (!empty($city) && $city->id > 0) {
                $areas = $city->areas()->select('id', 'name', 'city_id')->get();
                $cityDetails = $city->toArray();
                $areaDetails = $areas->toArray();
            }

            if ($user->profile_picture != null) {
                $vendorUploadPath = URL::asset(env('CUSTOMER_FILE_UPLOAD'));
                $vendorOwnDirecory = $vendorUploadPath . "/" . sha1($user->id) . "/" . "profile_image/";
                $user['profile_picture'] = $vendorOwnDirecory . "thumb_267X267_" . $user->profile_picture;
            }

            $userMeta = $user->customer()->select('pincode', 'area_id', 'phone_no', 'gender', 'birthdate')->get();
            if (!empty($userMeta)) {
                $customer = $userMeta->toArray();
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

    /**
     * updating user profile information of authenticated user based on token
     * 
     * @param array $data
     * @return array
     */
    public function updateProfileInformation(array $data)
    {
        try {
            $user = $this->getAuthenticatedUser();
            $user->fname = !empty($data['fname']) ? $data['fname'] : $user->fname;
            $user->lname = !empty($data['lname']) ? $data['lname'] : $user->lname;

            if (!empty($data['profile_picture'])) {
                $vendorUploadPath = public_path() . env('CUSTOMER_FILE_UPLOAD');
                $vendorOwnDirecory = $vendorUploadPath . sha1($user->id);
                $vendorImageUploadPath = $vendorOwnDirecory . "/" . "profile_image";

                /* Create Upload Directory If Not Exists */
                if (!file_exists($vendorImageUploadPath)) {
                    File::makeDirectory($vendorImageUploadPath, $mode = 0777, true, true);
                    chmod($vendorOwnDirecory, 0777);
                    chmod($vendorImageUploadPath, 0777);
                }

                $extension = $data['profile_picture']->getClientOriginalExtension();
                $fileName = sha1($user->id . time()) . ".{$extension}";
                $data['profile_picture']->move($vendorImageUploadPath, $fileName);
                chmod($vendorImageUploadPath, 0777);

                $fileHelper = new FileHelper($data['profile_picture']);
                $fileHelper->sourceFilename = $fileName;
                $fileHelper->sourceFilepath = $vendorImageUploadPath . "/";
                $fileHelper->destinationPath = $vendorImageUploadPath . "/";
                $fileHelper->resizeImage('user', true);

                $user->profile_picture = $fileName;
            }

            $user->save();
            $this->addOrUpdateCustomerInformation($user, $data);

            return $this->getUserProfile();
        } catch (Exception $exception) {
            APIResponse::handelException($exception);
        }
    }

    /**
     * If it is existing user update customer user information else add new 
     * entry for the user with the provided data as customer
     * 
     * @param User $user
     * @param array $data
     */
    private function addOrUpdateCustomerInformation(User $user, array $data)
    {
        $customer = $user->customer()->first();

        if (empty($customer)) {
            $customer = new Customer();
            $customer->birthdate = !empty($data['birthdate']) ? $data['birthdate'] : NULL;
            $customer->pincode = !empty($data['pincode']) ? $data['pincode'] : NULL;
            $customer->area_id = !empty($data['area_id']) ? $data['area_id'] : NULL;
        } else {
            $customer->birthdate = !empty($data['birthdate']) ? $data['birthdate'] : $customer->birthdate;
            $customer->pincode = !empty($data['pincode']) ? $data['pincode'] : $customer->pincode;
            $customer->area_id = !empty($data['area_id']) ? $data['area_id'] : $customer->area_id;
        }

        $customer->user_id = $user->id;
        $customer->save();
    }

}