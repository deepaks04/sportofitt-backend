<?php

namespace App\Http\Services;

use Input;
use App\SessionBooking;
use App\AvailableFacility;
use Carbon\Carbon;
use App\SessionPackage;
use App\Order;
use App\BookedPackage;
use App\BookedTiming;
use App\Http\Services\BaseService;

class BookingService extends BaseService
{

    /**
     *
     * @var mixed (NULL| App\SessionBooking)
     */
    private $sessionBooking = null;
    private $orderObj = null;
    private $bookingData = null;

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
            $this->bookingData = $bookingData;
            if (!empty($this->bookingData)) {
                $this->orderObj = $this->makeOrder();
                if (!empty($this->orderObj->id)) {
                    return $this->processBooking();
                }
            }
        } catch (\Exception $exception) {
            throw new \Exception($exception->getMessage(), $exception->getCode(), $exception);
        }

        return 0;
    }

    /**
     * Get order id
     * 
     * @return string
     */
    private function getOrderId()
    {
        $today = date("Ymd");
        $rand = strtoupper(substr(uniqid(sha1(time())), 0, 4));
        $unique = $today . $rand;
        return $unique;
    }

    /**
     * make order
     * 
     * @return App\Order
     * @throws \Exception
     */
    public function makeOrder()
    {
        try {
            $user = \Request::user();
            $order = new Order();
            $array = array('user_id' => $user->id,
                'order_id' => $this->getOrderId(),
                'created_at' => date("Y-m-d H:i:s"),
                'order_status' => 2,
                'order_total' => Input::get('order_total'),
                'payment_mode' => Input::get('payment_mode')
            );
            
            if('cash' == trim(Input::get('payment_mode'))) {
                $array['order_status'] = 1;
                $array['payment_status'] = 1;
            }
            
            return $order->createOrder($array);
        } catch (\Exception $exception) {
            throw new \Exception($exception->getMessage(), $exception->getCode(), $exception);
        }
    }

    /**
     * Processing booking for the user
     * 
     * @return int
     * @throws \Exception
     */
    public function processBooking()
    {
        try {
            
            if (!empty($this->orderObj->id) && !empty($this->bookingData)) {
                foreach ($this->bookingData as $bookingData) {
                    $bookingObj = new BookedPackage();
                    $bookingObj->package_type = $bookingData['package_type_id'];
                    $bookingObj->name = $bookingData['name'];
                    $bookingObj->description = $bookingData['description'];
                    $bookingObj->booking_amount = $bookingData['booking_amount'];
                    $bookingObj->discount = !empty($bookingData['discount'])?$bookingData['discount']:0;
                    $bookingObj->final_amount = $bookingObj->discounted_amount;
                    $bookingObj->order_id = $this->orderObj->id;
                    $bookingObj->no_of_peak = (isset($bookingData['no_of_peak'])) ? $bookingData['no_of_peak'] : 0;
                    $bookingObj->no_of_offpeak = (isset($bookingData['no_of_offpeak'])) ? $bookingData['no_of_offpeak'] : 0;
                    $bookingObj->booking_status = 2;
                    
                    if('cash' == trim(Input::get('payment_mode'))) {
                        $bookingObj->booking_status = 1;
                    }
            
                    $bookingObj->created_at = date('Y-m-d H:i:s');
                    if ($bookingObj->save()) {
                        $this->addBookingTimings($bookingObj, $bookingData);
                    }
                }
 
                return 1;
            }
        } catch (\Exception $exception) {
            throw new \Exception($exception->getMessage(), $exception->getCode(), $exception);
        }
    }

    /**
     * Add booking timig for the booking made by the user
     * 
     * @param BookedPackage $booking
     * @param object $bookingData
     * @throws \Exception
     */
    private function addBookingTimings(\App\BookedPackage $booking, $bookingData)
    {
        try {
            if (!empty($booking->id)) {
                $bookingTimming = new BookedTiming();
                $bookingTimming->booking_id = $booking->id;
                $bookingTimming->facility_id = $bookingData['facilityId'];
                $bookingTimming->is_peak = ($bookingData['is_peak']) ? 1 : 0;
                $bookingTimming->booking_date = date("Y-m-d H:i:s", strtotime($bookingData['selectedDate']));
                $bookingTimming->booking_day = date('N', strtotime($bookingTimming->booking_date));
                $slotTime = explode("-", $bookingData['selectedSlot']);
                if (!empty($slotTime)) {
                    $bookingTimming->start_time = $slotTime[0];
                    $bookingTimming->end_time = $slotTime[1];
                }

                $bookingTimming->created_at = date('Y-m-d H:i:s');
                $bookingTimming->save();
            }
        } catch (\Exception $exception) {
            throw new \Exception($exception->getMessage(), $exception->getCode(), $exception);
        }
    }

    /**
     * Get opening hours acording to the facility id 
     * 
     * @param integer $facilityId
     * @return arary
     * @throws \Exception
     */
    public function getOpeningHoursOfFacility($facilityId, $date, $isPeak)
    {
        $bookingHours = array();
        try {
            $facility = new AvailableFacility();
            $facilityDetails = $facility->getFacilityDetails($facilityId);
            if (!empty($facilityDetails->id)) {
                $date = $date / 1000;
                $day = date('N', $date);
                $openingHours = $facilityDetails->getOpenigHoursOfFacility($day,$isPeak);
                $bookingHours = $this->getBookingAvailableTimings($facilityDetails,$openingHours,$date,$isPeak);
                
            }
        } catch (\Exception $ex) {
            throw new \Exception($ex);
        }
        
        return $bookingHours;
    }
    
    public function getBookingAvailableTimings($facilityDetails,$openingHours,$date,$isPeak) 
    {
        $sessionDetails = $facilityDetails->getSession($facilityDetails->id);
        $bookingTiming = array();
        foreach ($openingHours as $openingHour) {
            if (isset($bookingTiming) && count($bookingTiming) > 0) {
                $bookingTiming = array_merge($bookingTiming, $this->getTimings($openingHour->start, $openingHour->end, $sessionDetails->duration));
            } else {
                $bookingTiming = $this->getTimings($openingHour->start, $openingHour->end, $sessionDetails->duration);
            }
        }
        
        // if not empty then check is booking made against the same date and time
        if(!empty($bookingTiming)) {
            $bookingDate = date('Y-m-d',$date);
            foreach($bookingTiming as $key => $timeSlot) {
                $bookingCount = $this->checkAvailability($facilityDetails->id, $timeSlot, $bookingDate, $isPeak);
                if($bookingCount == $facilityDetails->slots) {
                    unset($bookingTiming[$key]);
                }
            }
            
            $bookingTiming = array_values($bookingTiming);
        }
        
        return $bookingTiming;        
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
                if (isset($array[$openingHour->day]['peak']) && count($array[$openingHour->day]['peak']) > 0) {
                    $array[$openingHour->day]['peak'] = array_merge($array[$openingHour->day]['peak'], $this->getTimings($openingHour->start, $openingHour->end, $duration));
                } else {
                    $array [$openingHour->day]['peak'] = $this->getTimings($openingHour->start, $openingHour->end, $duration);
                }
            } else {
                if (isset($array[$openingHour->day]['offpeak']) && count($array[$openingHour->day]['offpeak']) > 0) {
                    $array[$openingHour->day]['offpeak'] = array_merge($array[$openingHour->day]['offpeak'], $this->getTimings($openingHour->start, $openingHour->end, $duration));
                } else {
                    $array[$openingHour->day]['offpeak'] = $this->getTimings($openingHour->start, $openingHour->end, $duration);
                }
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
            $newInstance = $carbonStartDate->
                    addMinutes($duration);
            $minutes[] = $startMinutes . "-" . date("H:s", strtotime($newInstance->__toString()));
            $carbonStartDate = $newInstance;
        }

        return $minutes;
    }

    /**
     * Check Availability for the timings 
     * 
     * @param integer $facilityId
     * @param string $timeSlot
     * @param string $date
     * @param integer $isPeak
     * @return boolean
     */
    public function checkAvailability($facilityId, $timeSlot, $date,$isPeak)
    {
        $result = false;
        try {
            if (!empty($facilityId) && !empty($timeSlot) && !empty($date) && isset($isPeak)) {
                $slot = explode("-", $timeSlot);
                $day = date('N', strtotime($date));
                $isBooked = BookedTiming::select('booked_timings.id')
                        ->join('booked_packages', function($join) {
                            $join->on('booked_packages.id', '=', 'booked_timings.booking_id');
                            $join->on('booked_packages.booking_status', '=', \DB::raw(1));
                        }) 
                        ->where('booked_timings.start_time', '=', $slot[0])
                        ->where('booked_timings.end_time', '=', $slot[1])
                        ->where('booked_timings.booking_day', '=', $day)
                        ->where('booked_timings.facility_id', '=', $facilityId)
                        ->where('booked_timings.is_peak', '=', $isPeak)
                        ->get();
                return $isBooked->count();
            }
        } catch (\Exception $exception) {
            throw new \Exception($exception->getMessage(), $exception->getCode(), $exception);
        }
        return $result;
    }
    
}