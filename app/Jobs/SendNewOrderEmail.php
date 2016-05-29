<?php

namespace App\Jobs;

use App\Jobs\Job;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Bus\SelfHandling;
use Illuminate\Contracts\Queue\ShouldQueue;
use Monolog\Logger;
use Monolog\Handler\StreamHandler;
use Mail;
use Config;
use App\Order;

class SendNewOrderEmail extends Job implements SelfHandling, ShouldQueue
{

    use InteractsWithQueue,
        SerializesModels;

    /**
     *
     * @var array
     */
    protected $orderNumber = 0;

    /**
     *
     * @var mixed NULL|Logger
     */
    protected $log = null;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($orderNumber)
    {
        $this->log = new Logger('queue_log');
        $this->log->pushHandler(new StreamHandler(storage_path('logs/laravel.log'), Logger::ERROR));
        $this->orderNumber = $orderNumber;
    }

    /**
     *  Execute the job
     * 
     * @throws Exception
     */
    public function handle()
    {
        try {
            $log = $this->log;

            $params = array();
            $order = Order::select('orders.order_id As orderNumber', 'orders.order_total', 'orders.payment_mode', 'users.fname', 'users.lname',
                    'users.email', 'booked_packages.booking_amount','booked_packages.discount', 'booked_packages.final_amount', 'booked_packages.name as bookingTitle', 
                    'booked_packages.package_type','booked_packages.id AS bookingId', 'booked_timings.booking_date', 'booked_timings.start_time', 
                    'booked_timings.end_time', 'booked_timings.facility_id')
                    ->join('booked_packages', 'booked_packages.order_id', '=', 'orders.id')
                    ->join('booked_timings', 'booked_timings.booking_id', '=', 'booked_packages.id')
                    ->join('users', 'orders.user_id', '=', 'users.id')
                    ->where('orders.id', '=', \DB::raw($this->orderNumber))
                    ->first();
            if ($order) {
                $subject = "Sprotofitt:" . $order->orderNumber . " : Order Confirmation";
                $facilityDetails = \App\AvailableFacility::select('available_facilities.is_venue', 'available_facilities.is_venue','vendors.user_id',
                        'vendors.business_name', 'vendors.contact', 'areas.name AS AreaName', 'sub_categories.name AS subCategoryName',
                        'vendors.postcode','vendors.address','users.fname as vendorFname','users.lname as vendorLname','users.email as vendorEmail')
                        ->join('vendors', 'available_facilities.vendor_id', '=', 'vendors.id')
                        ->join('users', 'vendors.user_id', '=', 'users.id')
                        ->join('areas', 'available_facilities.area_id', '=', 'areas.id')
                        ->join('sub_categories', 'available_facilities.sub_category_id', '=', 'sub_categories.id')
                        ->where('available_facilities.id', '=', $order->facility_id)
                        ->first();

                $params['fname'] = ucfirst($order->fname);
                $params['lname'] = ucfirst($order->lname);
                $params['email'] = $order->email;
                $params['orderNumber'] = $order->orderNumber;
                $params['packageType'] = $order->package_type;
                $params['bookingDate'] = date('d M,Y',strtotime($order->booking_date));
                $params['bookingTime'] = date('h:s A', strtotime($order->start_time)) . "-" . date('h:s A', strtotime($order->end_time));
                $params['venueName'] = $order->bookingTitle;
                $params['address'] = $facilityDetails->address;
                $params['pincode'] = $facilityDetails->postcode;
                $params['paymentMode'] = $order->payment_mode;
                $params['subCategoryName'] = $facilityDetails->subCategoryName;
                $params['bookingAmount'] = $order->booking_amount;
                $params['convenienceCharges'] = 00;
                $params['discountAmount'] = $order->booking_amount * ($order->discount/100);
                $params['totalAmount'] = ($params['discountAmount']) ? (($params['bookingAmount'] - $params['discountAmount']) + $params['convenienceCharges']) : ($params['bookingAmount'] + $params['convenienceCharges']);

                $usersDetails = array('fname' => $params['fname'],'email' => $params['email']);
                Mail::queue('emails.neworder', $params, function($message) use($usersDetails, $log, $subject) {
                    $message->to($usersDetails['email'], $usersDetails['fname'])->subject($subject);
                    $log->addError(PHP_EOL . ' Email Sent ' . $usersDetails['email'] . PHP_EOL);
                });
                
                $log->addError(PHP_EOL . ' vname' .ucfirst($facilityDetails->vendorFname)." ".ucfirst($facilityDetails->vendorLname ).  PHP_EOL);
                
                $vendorParams = $params;
                $params['vendorName'] = ucfirst($facilityDetails->vendorFname)." ".ucfirst($facilityDetails->vendorLname);
                $vendorParams['fname'] = ucfirst($facilityDetails->vendorFname);
                $vendorParams['email'] = $facilityDetails->vendorEmail;
                Mail::queue('emails.vendor.newordernotification', $params, function($message) use($vendorParams, $log, $subject) {
                    $message->to($vendorParams['email'], $vendorParams['fname'])->subject($subject);
                    $log->addError(PHP_EOL . ' Email Sent ' . $vendorParams['email'] . PHP_EOL);
                });                
                
                $adminEmailDetails = Config::get('mail.from');
                Mail::queue('emails.admin.newordernotification', $params, function($message) use($log, $adminEmailDetails, $subject) {
                    $message->to($adminEmailDetails['address'], $adminEmailDetails['name'])->subject($subject);
                    $log->addError(PHP_EOL . ' Email Sent To ADMIN FOR NEW ORDER' . PHP_EOL);
                });
            }
        } catch (Exception $exc) {
            throw new Exception($exc->getMessage(), $exc->getCode(), $exc);
        }
    }

}
