<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Helpers\APIResponse;
use App\Http\Services\IndexService;

class IndexController extends Controller
{

    /**
     *
     * @var mixed null | App\Http\Services\IndexService
     */
    protected $service = null;

    public function __construct()
    {
        $this->service = new IndexService();
    }

    public function index(Request $request)
    {
        try {
            $data = $request->all();
            $vendors = $this->service->getVendors($data);
            APIResponse::$data = $vendors;
        } catch (Exception $exception) {
            APIResponse::handleException($exception);
        }

        return APIResponse::sendResponse();
    }

    /**
     * Get featured facilities.
     * 
     * @return App\AvailableFacilities
     */
    public function featuredListing()
    {
        try {
            APIResponse::$data = $this->service->getFacilities(array('is_featured', '=', 1));
        } catch (Exception $exception) {
            APIResponse::handleException($exception);
        }

        return APIResponse::sendResponse();
    }

    /**
     * Get all lates available facilities those are added recently.
     * 
     * @return App\AvailableFacilities
     */
    public function latestFacilities()
    {
        try {
            APIResponse::$data = $this->service->getFacilities(array('is_featured', '!=', 1));
        } catch (Exception $exception) {
            APIResponse::handleException($exception);
        }

        return APIResponse::sendResponse();
    }

}