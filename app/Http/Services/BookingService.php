<?php

namespace App\Http\Services;

use App\SessionBooking;
use App\SessionPackage;
use App\Http\Services\BaseService;

class BookingService extends BaseService
{

    /**
     *
     * @var mixed (NULL| App\SessionBooking)
     */
    private $sessionBooking = null;

    public function __construct()
    {
        try {
            parent::__construct();
            $this->sessionBooking = new SessionBooking();
        } catch (Exception $exception) {
            throw new Exception($exception->getMessage(), $exception->getStatusCode(), $exception);
        }
    }

    /**
     * Get user bookings according to user 
     * 
     * @return mixed (NULL | App\SessionBooking)
     * @throws Exception
     */
    public function getUsersBooking()
    {
        try {
            return $this->sessionBooking->getUsersBooking($this->user->id, $this->offset, $this->limit);
        } catch (Exception $exception) {
            throw new Exception($exception->getMessage(), $exception->getCode(), $exception);
        }
    }

    /**
     * Get booking details by id 
     * 
     * @param integer $id
     * @return array
     * @throws Exception
     */
    public function getBookigDetails($id)
    {
        try {
            $response = array();
            $booking = $this->sessionBooking->findBookingDetails($id);
            if (!empty($booking)) {
                $response['booking'] = $booking;
                $response['facility'] = $booking->facility()->first();
                $response['facility']['vendor'] = $booking->facility()->first()->vendor;
                $response['facility']['subCategory'] = $booking->facility()->first()->subCategory;
                $response['facility']['subCategory']['rootCategory'] = $booking->facility()->first()->subCategory()->first()->rootCategory;
            }

            return $response;
        } catch (Exception $exception) {
            throw new Exception($exception->getMessage(), $exception->getCode(), $exception);
        }
    }

    /**
     * Book a package for user with the user id
     * 
     * @param App\SessionPackage $packageId
     * @return mixed boolean | App\BookedPackage
     * @throws Exception
     */
    public function bookPacakge($packageId)
    {
        try {
            $sessionPackage = new SessionPackage();
            $package = $sessionPackage->getPackageDetails($packageId);
            if (!empty($package)) {
                return $package->bookPackageForUser($this->user->id);
            }
        } catch (Exception $exception) {
            throw new Exception($exception->getMessage(), $exception->getCode(), $exception);
        }

        return false;
    }

}