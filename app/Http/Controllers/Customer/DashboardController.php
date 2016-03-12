<?php

namespace App\Http\Controllers\Customer;

use App\Http\Helpers\APIResponse;
use App\Http\Controllers\Controller;
use App\Http\Services\DashboardService;
use App\Http\Requests\CustomerProfileUpdateRequest;
use App\Http\Requests\CustomerProfilePictureRequest;
use Hash;

class DashboardController extends Controller
{

    public function __construct()
    {
        $this->service = new DashboardService();
    }

    /**
     *  Showing dashboard for logged in user with his basic information
     * 
     * @return json
     */
    public function index()
    {
        try {
            $profileDetails = $this->service->getUserProfile();
            APIResponse::$data = $profileDetails;
        } catch (Exception $exception) {
            APIResponse::handleException($exception);
        }

        return APIResponse::sendResponse();
    }

    /**
     * Updating user's profile information
     * 
     * @param CustomerProfileUpdateRequest $request
     * @return APIResponse
     */
    public function updateProfile(CustomerProfileUpdateRequest $request)
    {
        try {

            $data = $request->all();
            $profileDetails = $this->service->updateProfileInformation($data);
            if (empty(APIResponse::$message['error'])) {
                APIResponse::$data = $profileDetails;
                APIResponse::$message['success'] = 'User profile has been updated successfully';
            }
        } catch (Exception $exception) {
            APIResponse::handleException($exception);
        }

        return APIResponse::sendResponse();
    }

    /**
     *  Change profile image of user. 
     * 
     * @param CustomerProfilePictureRequest $request
     * @return  Illuminate\Support\Facades\Response
     */
    public function changeProfilePicture(CustomerProfilePictureRequest $request)
    {
        try {
            $data = $request->all();
            $profileImage = $this->service->updateProfilePicture($data);
            $userProfileImage = ($profileImage) ? asset(env('CUSTOMER_FILE_UPLOAD') . sha1($this->service->getUser()->id) . '/profile_image/thumb_267X267_' . $profileImage) : null;
            APIResponse::$data = $userProfileImage;
            APIResponse::$message['success'] = 'Profile picture changed successfully';
        } catch (Exception $exception) {
            APIResponse::handleException($exception);
        }
        return APIResponse::sendResponse();
    }

    /**
     *  Changing customer password  
     * 
     * @param \App\Http\Controllers\Customer\CustomerChangePasswordRequest $request
     * @return \Illuminate\Http\Response
     */
    public function changePassword(\App\Http\Requests\CustomerChangePasswordRequest $request)
    {
        try {
            $data = $request->all();
            if (Hash::check($data['current_password'], $this->service->getUser()->password)) {
                APIResponse::$message['error'] = 'New Password must not be same as the old password';
            } else if ($this->service->changePassword($data)) {
                APIResponse::$message['success'] = 'Password has been changed successfully';
            }
        } catch (Exception $exception) {
            APIResponse::handleException($exception);
        }

        return APIResponse::sendResponse();
    }

}