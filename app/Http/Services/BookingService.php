<?php

namespace App\Http\Services;

use Input;
use App\SessionBooking;
use App\AvailableFacility;
use Carbon\Carbon;
use App\Order;
use App\BookedPackage;
use App\BookedTiming;
use App\Jobs\SendCancelOrderEmail;
use Illuminate\Foundation\Bus\DispatchesJobs;
use URL;
use App\Http\Services\BaseService;
use App\Jobs\SendNewOrderEmail;

class BookingService extends BaseService
{
    
    use DispatchesJobs;

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
            $fields = array('orders.id as oid', 'orders.order_id', 'order_total', 'payment_status', 'payment_mode', 'orders.created_at',
                'booked_packages.id as bookingId', 'booked_packages.package_type', 'booked_packages.name', 'booked_packages.description',
                'booked_packages.no_of_month', 'booked_packages.booking_amount', 'booked_packages.discount', 'booked_packages.final_amount',
                'booked_packages.no_of_peak', 'booked_packages.no_of_offpeak', 'booked_packages.booking_status');

            $orders = Order::select($fields)
                    ->join('booked_packages', 'orders.id', '=', 'booked_packages.order_id')
                    ->where('user_id', '=', $this->user->id)
                    ->where('order_status', '!=', \DB::raw(0))
                    ->orderBy('orders.created_at', 'ASC')
                    ->get();
            if (isset($orders) && $orders->count() > 0) {
                $vendorUploadPath = env('VENDOR_FILE_UPLOAD');
                foreach ($orders as $order) {
                    $bookingTimings = BookedTiming::select('is_peak', 'booking_date', 'booking_day', 'start_time', 'end_time', 'facility_id')
                            ->where('booked_timings.booking_id', '=', $order->bookingId)
                            ->get();
                    if (isset($bookingTimings) && $bookingTimings->count() > 0) {
                        foreach ($bookingTimings as $bookedTiming) {
                            $bookingTime = Carbon::createFromTimestamp(strtotime($bookedTiming->booking_date . " " . $bookedTiming->start_time));
                            $currentDate = Carbon::now();
                            $bookedTiming['is_editable'] = (3 == $order->order_status) ? false : $this->checkIsItPastBooking($bookingTime, $currentDate);
                            $facility = AvailableFacility::select('vendors.user_id','users.profile_picture', 'areas.name AS AreaName','sub_categories.name as SubCategory', 'root_categories.name as RootCategory','vendors.business_name','vendors.address','vendors.postcode')
                                    ->leftJoin('areas', 'areas.id', '=', 'available_facilities.area_id')
                                    ->leftJoin('vendors', 'vendors.id', '=', 'available_facilities.vendor_id')
                                    ->leftJoin('users', 'vendors.user_id', '=', 'users.id')
                                    ->leftJoin('sub_categories', 'sub_categories.id', '=', 'available_facilities.sub_category_id')
                                    ->leftJoin('root_categories', 'root_categories.id', '=', 'available_facilities.root_category_id')
                                    ->where('available_facilities.id', '=', $bookedTiming['facility_id'])
                                    ->take(1)
                                    ->first();
                            $bookedTiming['image'] = URL::asset($vendorUploadPath . "noProfilePic.png");
                            $bookedTiming['start_time'] = date('h:i A', strtotime($bookedTiming['booking_date'] . " " . $bookedTiming['start_time']));
                            $bookedTiming['end_time'] = date('h:i A', strtotime($bookedTiming['booking_date'] . " " . $bookedTiming['end_time']));
                            if (isset($facility) && $facility->count() > 0) {
                                $imagePath = $vendorUploadPath . sha1($facility->user_id) . "/" . "profile_image/" . $facility->profile_picture;
                                if (file_exists(public_path() . $imagePath)) {
                                    $bookedTiming['image'] = URL::asset($imagePath);
                                }
                                $bookedTiming['subcategory'] = $facility->SubCategory;
                                $bookedTiming['category'] = $facility->RootCategory;
                                $bookedTiming['vendor_name'] = $facility->business_name;
                                $bookedTiming['vendor_address'] = $facility->address;
                                $bookedTiming['vendor_pincode'] = $facility->postcode;
                                $bookedTiming['area_name'] = $facility->AreaName;
                            }
                        }
                        
                        $order->bookingDetails = $bookingTimings->toArray();
                    }
                }
            }

            return $orders;
        } catch (\Exception $exception) {
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
                    if($this->processBooking()) {
                        $job = (new SendNewOrderEmail($this->orderObj->id))->delay(10);
                        $this->dispatch($job);
                    }
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
            
            if ('cash' == trim(Input::get('payment_mode'))) {
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
                    $bookingObj->discount = !empty($bookingData['discount']) ? $bookingData['discount'] : 0;
                    $bookingObj->final_amount = !empty($bookingObj['discounted_amount']) ? $bookingObj['discounted_amount'] : $bookingObj->booking_amount;
                    $bookingObj->order_id = $this->orderObj->id;
                    if ($bookingData['is_peak']) {
                        $bookingObj->no_of_peak = (isset($bookingData['qty'])) ? $bookingData['qty'] : 0;
                    } else {
                        $bookingObj->no_of_offpeak = (isset($bookingData['qty'])) ? $bookingData['qty'] : 0;
                    }

                    $bookingObj->booking_status = 2;
                    if ('cash' == trim(Input::get('payment_mode'))) {
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
                $openingHours = $facilityDetails->getOpenigHoursOfFacility($day, $isPeak);
                $bookingHours = $this->getBookingAvailableTimings($facilityDetails, $openingHours, $date, $isPeak);
            }
        } catch (\Exception $ex) {
            throw new \Exception($ex);
        }

        return $bookingHours;
    }

    /**
     * Get booking available timings.
     * 
     * @param App\AvailableFacilityDetails $facilityDetails
     * @param array $openingHours
     * @param string $date
     * @param integer $isPeak
     * @return array
     */
    public function getBookingAvailableTimings($facilityDetails, $openingHours, $date, $isPeak)
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
        if (!empty($bookingTiming)) {
            $bookingDate = date('Y-m-d', $date);
            $currentDate = date('Y-m-d');
            $currentHour = date('H');
            foreach ($bookingTiming as $key => $timeSlot) {
                $startHour = (int)$key;
                if($bookingDate == $currentDate && $currentHour >= $startHour) {
                    unset($bookingTiming[$key]);
                    continue;
                }
                
                $bookingCount = $this->checkAvailability($facilityDetails->id, $key, $bookingDate, $isPeak);
                if ($bookingCount == $facilityDetails->slots) {
                    unset($bookingTiming[$key]);
                }
            }
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
            $newInstance = $carbonStartDate->addMinutes($duration);
            $openingTime = $startMinutes . "-" . date("H:s", strtotime($newInstance->__toString()));
            $minutes[$openingTime] = date("h:s A", strtotime("1970-01-01 ".$startMinutes.":00"))."-".date("h:s A", strtotime($newInstance->__toString()));
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
    public function checkAvailability($facilityId, $timeSlot, $date, $isPeak)
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
                        ->where('booked_timings.booking_date', '=', date('Y-m-d',strtotime($date)))
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

    /**
     * Cancelling order according to the order id 
     * 
     * @param integer $orderId
     * @return array
     * @throws \Exception
     */
    public function cancelUserOrder($orderId)
    {
        $response = array('error' => null, 'refund' => null);
        $bookingDetails = array();
        try {
            if (!empty($orderId)) {
                $orderDetails = BookedPackage::select('users.fname','users.lname','users.email','booked_packages.*', 'orders.order_id As orderNumber', 'orders.order_total', 'orders.payment_mode', 'orders.order_status')
                        ->join('orders', 'orders.id', '=', 'booked_packages.order_id')
                        ->leftJoin('users', 'orders.user_id', '=', 'users.id')
                        ->where('booked_packages.order_id', '=', $orderId)
                        ->first();
                if (!empty($orderDetails)) {
                    $bookingDetails['fname'] = $orderDetails->fname;
                    $bookingDetails['lname'] = $orderDetails->lname;
                    $bookingDetails['orderNumber'] = $orderDetails->orderNumber;
                    $bookingDetails['email'] = $orderDetails->email;
                    if (3 == $orderDetails->order_status) {
                        $response['error'] = 'Order has been already cancelled';
                        return $response;
                    }
                    
                    $bookedTiming = $orderDetails->bookedTimings()->orderBy('booking_day', 'ASC')->first();
                    $bookingTime = Carbon::createFromTimestamp(strtotime($bookedTiming->booking_date . " " . $bookedTiming->start_time));
                    $currentDate = Carbon::now();
                    if (!empty($bookedTiming)) {
                        if ($this->checkIsItPastBooking($bookingTime, $currentDate)) {
                            $response['refund'] = 'The refund for “Pay at Venue” order will be processed by venue provider, you are requested to follow up with venue provider';
                            if('cash' != $orderDetails->payment_mode) {
                                $response['refund'] = $this->calculateRefund($orderDetails, $bookedTiming->facility, $bookingTime, $currentDate);
                            }
                            
                            $cancellationDate = date('Y-m-d H:i:s');
                            Order::where('id', '=', $orderId)->update(['order_status' => 3, 'cancellation_date' => $cancellationDate]);
                            BookedPackage::where('order_id', '=', $orderId)->update(['booking_status' => 3, 'cancellation_date' => $cancellationDate]);
                            
                            // Adding job to queue for processing to the mail will be send via the queue
                            $job = (new SendCancelOrderEmail($bookingDetails))->delay(10);
                            $this->dispatch($job);                            
                            
                        } else {
                            $response['error'] = 'Booking time has been delapsed you can not cancel this order';
                        }
                    } else {
                        $response['error'] = 'Opps! something went wrong try again later';
                    }
                } else {
                    $response['error'] = 'Opps! something went wrong try again later';
                }
            }
        } catch (\Exception $exception) {
            throw new \Exception($exception->getMessage(), $exception->getCode(), $exception);
        }

        return $response;
    }

    /**
     * calculating the refund money when order is get cancelled
     * 
     * @param BookedPackage $orderDetails
     * @param AvailableFacility $facility
     * @param Carbon $bookingTime
     * @param Carbon $currentDate
     * @return integer
     * @throws \Exception
     */
    protected function calculateRefund(\App\BookedPackage $orderDetails, \App\AvailableFacility $facility, $bookingTime, $currentDate)
    {
        $refund = 0;
        try {
            if (!empty($facility)) {
                $cancellationBefore24Hrs = $facility->cancellation_before_24hrs;
                $cancellationWithIn24Hrs = $facility->cancellation_after_24hrs;
                $hoursBeforeCancel = $currentDate->diffInHours($bookingTime);
                if ($hoursBeforeCancel > 24) {
                    $refund = ($cancellationBefore24Hrs) ? ($orderDetails->order_total * ($cancellationBefore24Hrs / 100)) : $cancellationBefore24Hrs;
                } elseif ($hoursBeforeCancel <= 24) {
                    $refund = ($cancellationWithIn24Hrs) ? ($orderDetails->order_total * ($cancellationWithIn24Hrs / 100)) : $cancellationWithIn24Hrs;
                }
            }
        } catch (\Exception $ex) {
            throw new \Exception($ex->getMessage(), $ex->getCode());
        }

        return $refund;
    }

    /**
     * Checking is the cancellation is done for the past dates
     * 
     * @param Carbon $bookingTime
     * @param Carbon $currentDate
     * @return boolean
     */
    protected function checkIsItPastBooking($bookingTime, $currentDate)
    {
        return ($bookingTime->gt($currentDate));
    }

}
