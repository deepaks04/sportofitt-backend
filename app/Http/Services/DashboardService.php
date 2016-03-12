<?php

namespace App\Http\Services;

use URL;
use File;
use App\City;
use App\User;
use App\Customer;
use App\Http\Services\BaseService;
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
//            $city = City::select('id', 'name')->where('name', '=', 'pune')->first();
//            if (!empty($city) && $city->id > 0) {
//                $areas = $city->areas()->select('id', 'name', 'city_id')->get();
//                $cityDetails = $city->toArray();
//                $areaDetails = $areas->toArray();
//            }

            if ($this->user->profile_picture != null) {
                $vendorUploadPath = URL::asset(env('CUSTOMER_FILE_UPLOAD'));
                $vendorOwnDirecory = $vendorUploadPath . "/" . sha1($this->user->id) . "/" . "profile_image/";
                $this->user['profile_picture'] = $vendorOwnDirecory . "thumb_267X267_" . $this->user->profile_picture;
            }

            $userMeta = $this->user->customer()->select('pincode', 'area_id', 'phone_no', 'gender', 'birthdate')->first();
            if (!empty($userMeta)) {
                $customer = $userMeta->toArray();
                if ($customer && $customer['birthdate']) {
                    $customer['birthdate'] = date("d-m-Y", strtotime($customer['birthdate']));
                }
            }
            return array_merge($this->user->toArray(), $customer);
        } catch (Exception $exception) {
            throw new Exception($exception->getMessage(), $exception->getCode(), $exception);
        }

        return null;
    }

    /**
     *  Update customer profile picture
     * 
     * @param array $data
     * @return string
     * @throws Exception
     */
    public function updateProfilePicture($data)
    {
        try {
            if (!empty($data['profile_picture'])) {
                $vendorUploadPath = public_path() . env('CUSTOMER_FILE_UPLOAD');
                $vendorOwnDirecory = $vendorUploadPath . sha1($this->user->id);
                $vendorImageUploadPath = $vendorOwnDirecory . "/" . "profile_image";

                /* Create Upload Directory If Not Exists */
                if (!file_exists($vendorImageUploadPath)) {
                    File::makeDirectory($vendorImageUploadPath, $mode = 0777, true, true);
                    chmod($vendorOwnDirecory, 0777);
                    chmod($vendorImageUploadPath, 0777);
                }

                $extension = $data['profile_picture']->getClientOriginalExtension();
                $fileName = sha1($this->user->id . time()) . ".{$extension}";
                $data['profile_picture']->move($vendorImageUploadPath, $fileName);
                chmod($vendorImageUploadPath, 0777);

                $fileHelper = new FileHelper($data['profile_picture']);
                $fileHelper->sourceFilename = $fileName;
                $fileHelper->sourceFilepath = $vendorImageUploadPath . "/";
                $fileHelper->destinationPath = $vendorImageUploadPath . "/";
                $fileHelper->resizeImage('user', true);

                $this->user->profile_picture = $fileName;
                $this->user->save();
                
                return $this->user->profile_picture;
            }
        } catch (Exception $exception) {
            throw new Exception($exception->getMessage(), $exception->getCode(), $exception);
        }
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
            $this->user->fname = !empty($data['fname']) ? $data['fname'] : $this->user->fname;
            $this->user->lname = !empty($data['lname']) ? $data['lname'] : $this->user->lname;

//            if (!empty($data['profile_picture'])) {
//                $vendorUploadPath = public_path() . env('CUSTOMER_FILE_UPLOAD');
//                $vendorOwnDirecory = $vendorUploadPath . sha1($this->user->id);
//                $vendorImageUploadPath = $vendorOwnDirecory . "/" . "profile_image";
//
//                /* Create Upload Directory If Not Exists */
//                if (!file_exists($vendorImageUploadPath)) {
//                    File::makeDirectory($vendorImageUploadPath, $mode = 0777, true, true);
//                    chmod($vendorOwnDirecory, 0777);
//                    chmod($vendorImageUploadPath, 0777);
//                }
//
//                $extension = $data['profile_picture']->getClientOriginalExtension();
//                $fileName = sha1($this->user->id . time()) . ".{$extension}";
//                $data['profile_picture']->move($vendorImageUploadPath, $fileName);
//                chmod($vendorImageUploadPath, 0777);
//
//                $fileHelper = new FileHelper($data['profile_picture']);
//                $fileHelper->sourceFilename = $fileName;
//                $fileHelper->sourceFilepath = $vendorImageUploadPath . "/";
//                $fileHelper->destinationPath = $vendorImageUploadPath . "/";
//                $fileHelper->resizeImage('user', true);
//
//                $this->user->profile_picture = $fileName;
//            }

            $this->user->save();
            $this->addOrUpdateCustomerInformation($this->user, $data);

            return $this->getUserProfile();
        } catch (Exception $exception) {
            throw new Exception($exception->getMessage(), $exception->getCode(), $exception);
        }
    }

    /**
     * If it is existing user update customer user information else add new 
     * entry for the user with the provided data as customer
     * 
     * @param User $this->user
     * @param array $data
     */
    private function addOrUpdateCustomerInformation(User $user, array $data)
    {
        $customer = $user->customer()->first();

        if (empty($customer)) {
            $customer = new Customer();
        }

        $customer->pincode = !empty($data['pincode']) ? $data['pincode'] : NULL;
        $customer->area_id = !empty($data['area_id']) ? $data['area_id'] : NULL;
        $customer->phone_no = !empty($data['phone_no']) ? $data['phone_no'] : NULL;
        $customer->gender = !empty($data['gender']) ? $data['gender'] : NULL;
        $customer->birthdate = !empty($data['birthdate']) ? date("Y-m-d", strtotime($data['birthdate'])) : NULL;
        $customer->user_id = $this->user->id;

        $customer->save();
    }
    
    /**
     * 
     * @param array $data
     * @return boolean
     * @throws Exception
     */
    public function changePassword($data)
    {
        try {
            $password = bcrypt($data['password']);
            $this->user->password = $password;
            return $this->user->save();
        } catch (Exception $exception) {
            throw new Exception($exception->getMessage(), $exception->getCode(), $exception);
        }
    }

}