<?php

namespace App\Jobs;

use App\User;
use App\Jobs\Job;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Bus\SelfHandling;
use Illuminate\Contracts\Queue\ShouldQueue;
use Monolog\Logger;
use Monolog\Handler\StreamHandler;
use Mail;
use Config;

class SendOrderEmail extends Job implements SelfHandling, ShouldQueue
{

    use InteractsWithQueue,
        SerializesModels;

    CONST NEW_ORDER = 1;
    CONST CANCEL_ORDER = 2;
    
    protected $order;
    protected $purpose = NEW_ORDER;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($order,$purpose)
    {
        $this->order = $order;
        $this->purpose = $purpose;
    }


    /**
     *  Execute the job
     * 
     * @throws Exception
     */
    public function handle()
    {
        try {
            $params = array('fname' => ucfirst($this->order->fname),
                'lname' => ucfirst($this->order->lname),
                'orderId' => $this->order->order_id);
            $order = $this->order;
            $log = new Logger('queue_log');
            $log->pushHandler(new StreamHandler(storage_path('logs/laravel.log'), Logger::ERROR));
            $subject = "New Order has been placed";
            if($this->purpose == 2) {
                $subject = "Order has been cancelled";
            }
            
            Mail::queue('emails.ordercancel', $params, function($message) use($order, $log, $subject) {
                $message->to($order->email, $order->fname)->subject($subject);
                $log->addError(PHP_EOL . ' Email Sent' . PHP_EOL);
            });

            $adminEmailDetails = Config::get('mail.from');
            Mail::queue('emails.admin.ordercancelnotification', $params, function($message) use($log, $adminEmailDetails,$subject) {
                $message->to($adminEmailDetails['address'], $adminEmailDetails['name'])->subject($subject);
                $log->addError(PHP_EOL . ' Email Sent To ADMIN' . PHP_EOL);
            });
        } catch (Exception $exc) {
            throw new Exception($exc->getMessage(), $exc->getCode(), $exc);
        }
    }

}