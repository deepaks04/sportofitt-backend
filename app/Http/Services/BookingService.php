<?php

namespace App\Http\Services;

use App\SessionBooking;
use App\AvailableFacility;
use Carbon\Carbon;
use App\SessionPackage;
use App\Order;
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
     * @param string $bookingData
     * @return mixed boolean | App\BookedPackage
     * @throws Exception
     */
    public function makeBooking($bookingData)
    {
        try {
            $bookingInfo = json_decode($bookingData);
            if(!empty($bookingInfo)) {
                $this->makeOrder();
                foreach($bookingInfo as $bookingInformation) {
                    $this->processBooking();
                }
            }
        } catch (Exception $exception) {
            throw new Exception($exception->getMessage(), $exception->getCode(), $exception);
        }

        return false;
    }
    
    /**
     * Get opening hours acording to the facility id 
     * 
     * @param integer $facilityId
     * @return arary
     * @throws \Exception
     */
    public function getOpeningHoursOfFacility($facilityId)
    {
        try {
            $facility = new AvailableFacility();
            $facilityDetails = $facility->getFacilityDetails($facilityId);
            if (!empty($facilityDetails->id)) {
                $openingHours = $facilityDetails->getOpenigHoursOfFacility();
                $sessionDetails = $facilityDetails->getSession($facilityId);
                
                return $this->getFormattedOpeningHours($openingHours, $sessionDetails->duration);
            }
        } catch (\Exception $ex) {
            throw new \Exception($ex);
        }
    }
    
    /**
     * Get formatted opening hours that is the opening hour according to the days
     * peaks and offpeaks timings. Like for monday we can have entry as below:
     * 1 => array(
     *  'peaks' => array(
     *  ),
     *  'offpeaks' => array(
     *  ),
     * )
     * 
     * @param OpeningHour $openingHours
     * @param integer $duration
     * @return array
     */
    private function getFormattedOpeningHours($openingHours, $duration)
    {
        $array = array();
        foreach ($openingHours as $openingHour) {
            if ($openingHour->is_peak) {
                $array[$openingHour->day]['peaks'][] = $this->getTimings($openingHour->start, $openingHour->end, $duration);
            } else {
                $array[$openingHour->day]['offpeak'][] = $this->getTimings($openingHour->start, $openingHour->end, $duration);
            }
        }
        
        return $array;
    }

    /**
     * Get timings according to the start and end time selected by the user
     * 
     * @param string $start
     * @param string $end
     * @param integer $duration
     * @return array
     */
    private function getTimings($start, $end, $duration)
    {
        $minutes = array();
        $endMinutes = explode(":", $end);
        $carbonEndDate = Carbon::create(date('Y'), date('m'), date('d'), $endMinutes[0], $endMinutes[1], 0);

        $startMinutes = explode(":", $start);
        $carbonStartDate = Carbon::create(date('Y'), date('m'), date('d'), $startMinutes[0], $startMinutes[1], 0);
        while ($carbonStartDate->lt($carbonEndDate)) {
            $startMinutes = date("H:s", strtotime($carbonStartDate->__toString()));
            $newInstance = $carbonStartDate->addMinutes($duration);
            $minutes[] = $startMinutes . "-" . date("H:s", strtotime($newInstance->__toString()));
            $carbonStartDate = $newInstance;
        }
 
        return $minutes;
    }
    
    public function makeOrder()
    {
        $user = \Request::getUser();
        $order = new Order();
        $array = array('user_id' => $user->id, 
            'order_id' => $user->id.$this->getOrderId(), 
            'created_at' => date("Y-m-d H:i:s"),
            'order_status' => 2,
        );
        
        $orderObj = $order->createOrder($array);
        dd($orderObj);
    }
    
    private function getOrderId()
    {
        return time();
    }
}