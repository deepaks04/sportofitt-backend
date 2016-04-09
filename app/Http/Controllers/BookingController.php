<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Helpers\APIResponse;
use App\Http\Controllers\Controller;
use App\Http\Services\BookingService;
use App\Http\Requests\PackageBookingRequest;
use App\Http\Requests\SessionBookingRequest;

class BookingController extends Controller
{

    /**
     *
     * @var mixed null | App\Http\Services\IndexService
     */
    protected $service = null;

    public function __construct()
    {
        $this->service = new BookingService();
    }

    /**
     * Booking a package for user
     * 
     * @param PackageBookingRequest $request
     * @return \Illuminate\Http\Response
     */
    public function bookAPackage(PackageBookingRequest $request)
    {
        try {
            $bookedPackage = $this->service->bookPacakge($request->get('package_id'));
            if ($bookedPackage instanceof \App\BookedPackage) {
                $bookedPackageDetails = $bookedPackage->getBookedPackageDetails($bookedPackage->id);
                APIResponse::$message['success'] = 'Package has been booked successfully';
                APIResponse::$data = $bookedPackageDetails->toArray();
            }
        } catch (Exception $exception) {
            APIResponse::handleException($exception);
        }

        return APIResponse::sendResponse();
    }

    public function makeBooking(SessionBookingRequest $request)
    {
        try {
            $bookedPackage = $this->service->makeBooking($request->get('booking_data'));
            if ($bookedPackage instanceof \App\BookedPackage) {
                $bookedPackageDetails = $bookedPackage->getBookedPackageDetails($bookedPackage->id);
                APIResponse::$message['success'] = 'Package has been booked successfully';
                APIResponse::$data = $bookedPackageDetails->toArray();
            }
        } catch (Exception $exception) {
            APIResponse::handleException($exception);
        }

        return APIResponse::sendResponse();
    }

    /**
     * Get opening hours for the facility
     * 
     * @param integer $facilityId
     * @return Response
     */
    public function getOpeningHours($facilityId)
    {
        try {
            if(!empty($facilityId)) {
                $openigHours = $this->service->getOpeningHoursOfFacility($facilityId);
                APIResponse::$data = $openigHours;
            } else {
                APIResponse::$message['success'] = 'Facility missing. Select Facility first';
            }
        } catch (Exception $exception) {
            APIResponse::handleException($exception);
        }
        
         return APIResponse::sendResponse();
    }

}