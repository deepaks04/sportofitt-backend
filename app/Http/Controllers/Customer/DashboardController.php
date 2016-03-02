<?php namespace App\Http\Controllers\Customer;

use App\Http\Helpers\APIResponse;
use App\Http\Controllers\Controller;
use App\Http\Services\DashboardService;
use App\Http\Requests\CustomerProfileUpdateRequest;

class DashboardController extends Controller {

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

}
