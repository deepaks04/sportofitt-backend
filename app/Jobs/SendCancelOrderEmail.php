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

class SendCancelOrderEmail extends Job implements SelfHandling, ShouldQueue
{

    use InteractsWithQueue,
        SerializesModels;

    /**
     *
     * @var array
     */
    protected $order = array();

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
    public function __construct($order)
    {
        $this->log = new Logger('queue_log');
        $this->log->pushHandler(new StreamHandler(storage_path('logs/laravel.log'), Logger::ERROR));
        $this->order = $order;
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
            $params = array('fname' => ucfirst($this->order['fname']),
                'lname' => ucfirst($this->order['lname']),
                'orderId' => $this->order['orderNumber']);
            $subject = "Sprotofitt:" . $this->order['orderNumber'] . " : Order has been cancelled";
            $templateName = 'emails.ordercancel';
            $adminTemplate = 'emails.admin.ordercancelnotification';
            $orderRef = $this->order;
            
            $vendorDetails = Order::select('users.fname as vendorFname','users.lname as vendorLname','users.email as vendorEmail')
                    ->join('booked_packages','booked_packages.order_id','=','orders.id')
                    ->join('vendors','vendors.id','=','booked_packages.vendor_id')
                    ->join('users','users.id','=','vendors.user_id')
                    ->where('orders.order_id','=',$this->order['orderNumber'])
                    ->first();
            Mail::queue($templateName, $params, function($message) use($orderRef, $log, $subject) {
                $message->to($orderRef['email'], $orderRef['fname'])->subject($subject);
                $log->addError(PHP_EOL . ' Email Sent' . $orderRef['email'] . PHP_EOL);
            });
            
            $vendorParams = $params;
            $params['vendorName'] = ucfirst($vendorDetails->vendorFname) . " " . ucfirst($vendorDetails->vendorLname);
            $vendorParams['fname'] = ucfirst($vendorDetails->vendorFname);
            $vendorParams['email'] = $vendorDetails->vendorEmail;
            Mail::queue('emails.vendor.ordercancelnotification', $params, function($message) use($vendorParams, $log, $subject) {
                $message->to($vendorParams['email'], $vendorParams['fname'])->subject($subject);
                $log->addError(PHP_EOL . ' Email Sent ' . $vendorParams['email'] . PHP_EOL);
            });

            $adminEmailDetails = Config::get('mail.from');
            Mail::queue($adminTemplate, $params, function($message) use($log, $adminEmailDetails, $subject) {
                $message->to($adminEmailDetails['address'], $adminEmailDetails['name'])->subject($subject);
                $log->addError(PHP_EOL . ' Email Sent To ADMIN FOR CANCEL' . PHP_EOL);
            });
        } catch (Exception $exc) {
            throw new Exception($exc->getMessage(), $exc->getCode(), $exc);
        }
    }

}
