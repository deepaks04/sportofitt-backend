<?php

namespace App\Http\Controllers\Customer;

use Illuminate\Http\Request;
use App\Http\Helpers\APIResponse;
use App\Http\Controllers\Controller;
use App\Http\Services\BookingService;
use App\Http\Requests\PackageBookingRequest;
use App\Http\Requests\SessionBookingRequest;
use App\Http\Requests\CheckAvailabilityRequest;

class BookingController extends Controller
{

    public function __construct()
    {
        $this->service = new BookingService();
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        try {
            $userBookings = $this->service->getUsersBooking();
            if (0 == $userBookings->count()) {
                APIResponse::$message['success'] = 'Ooops ! I think you have not made your first booking yet!';
            }
            APIResponse::$data = $userBookings;
        } catch (Exception $exception) {
            APIResponse::handleException($exception);
        }

        return APIResponse::sendResponse();
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  integer  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        try {
            $booking = $this->service->getBookigDetails($id);
            APIResponse::$data = $booking;
        } catch (Exception $exception) {
            APIResponse::handleException($exception);
        }

        return APIResponse::sendResponse();
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

    /**
     * Make booking for user 
     * 
     * @param SessionBookingRequest $request
     * @return \Illuminate\Http\Response
     */
    public function makeBooking(SessionBookingRequest $request)
    {
        try {
            $isBooked = $this->service->makeBooking($request->get('booking_data'));
            if ($isBooked) {
                APIResponse::$message['success'] = 'Booking has been done succesfully';
            } else {
                APIResponse::$message['success'] = 'Something went wrong';
            }
        } catch (\Exception $exception) {
            APIResponse::handleException($exception);
        }

        return APIResponse::sendResponse();
    }

    /**
     * Get opening hours for the facility
     * 
     * @param $request Request
     * @return Response
     */
    public function getOpeningHours(Request $request)
    {
        try {
            $facilityId = $request->get('facility_id');
            $date = $request->get('date');
            $isPeak = $request->get('is_peak');
            if (!empty($facilityId)) {
                $openigHours = $this->service->getOpeningHoursOfFacility($facilityId,$date,$isPeak);
                if(empty($openigHours)) {
                    APIResponse::$message['success'] = 'Slots not available';
                }
                APIResponse::$data = $openigHours;
            } else {
                APIResponse::$message['success'] = 'Facility missing. Select Facility first';
            }
        } catch (Exception $exception) {
            APIResponse::handleException($exception);
        }

        return APIResponse::sendResponse();
    }
    
    /**
     * 
     * @param CheckAvailabilityRequest $request
     * @return \Illuminate\Http\Response
     */
    public function checkAvailability(CheckAvailabilityRequest $request)
    {
        try {
            $facilityId = $request->get('facility_id');
            $timeSlots = $request->get('time_slot');
            $bookingDate = $request->get('booking_date');
            $isPeak = $request->get('is_peak');
            $isAvailable = $this->service->checkAvailability($facilityId, $timeSlots,$bookingDate, $isPeak);
            if(!$isAvailable) {
                APIResponse::$message['error'] = 'Session Not Available';
            } else {
                APIResponse::$message['success'] = 'Session Available';
            }
            
            APIResponse::$data = $isAvailable;
        } catch (Exception $exception) {
            APIResponse::handleException($exception);
        }

        return APIResponse::sendResponse();
    }

}