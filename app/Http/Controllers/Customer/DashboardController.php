<?php

namespace App\Http\Controllers\Customer;

use App\City;
use Request;
use App\Http\Helpers\APIResponse;
use App\Http\Controllers\Controller;
use App\Http\Services\DashboardService;

class DashboardController extends Controller
{

    private $service = null;

    public function __construct()
    {
        $this->service = new DashboardService();
    }

    /**
     *  Showing dashboard for logged in user with his basic information
     * 
     * @param Request $request
     * @return json
     */
    public function index(Request $request)
    {
        try {
            $profileDetails =  $this->service->getUserProfile();
            APIResponse::$data = $profileDetails;
            APIResponse::sendResponse();
        } catch (Exception $e) {
            APIResponse::handleException($e);
        }
        return APIResponse::sendResponse();
    }

}