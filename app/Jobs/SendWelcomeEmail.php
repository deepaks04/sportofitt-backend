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

class SendWelcomeEmail extends Job implements SelfHandling, ShouldQueue
{

    use InteractsWithQueue,
        SerializesModels;

    protected $user;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(User $user)
    {
        $this->user = $user;
    }


    /**
     *  Execute the job
     * 
     * @throws Exception
     */
    public function handle()
    {
        try {
            $params = array('fname' => ucfirst($this->user->fname),
                'lname' => ucfirst($this->user->lname),
                'email' => $this->user->email,
                'remember_token' => $this->user->remember_token);
            $user = $this->user;
            $log = new Logger('queue_log');
            $log->pushHandler(new StreamHandler(storage_path('logs/laravel.log'), Logger::ERROR));

            Mail::queue('emails.activation', $params, function($message) use($user, $log) {
                $message->to($user->email, $user->fname)->subject('Welcome!');
                $log->addError(PHP_EOL . ' Email Sent' . PHP_EOL);
            });

            $adminEmailDetails = Config::get('mail.from');
            Mail::queue('emails.admin.newnotification', $params, function($message) use($log, $adminEmailDetails) {
                $message->to($adminEmailDetails['address'], $adminEmailDetails['name'])->subject('New User Registration!');
                $log->addError(PHP_EOL . ' Email Sent To ADMIN' . PHP_EOL);
            });
        } catch (Exception $exc) {
            throw new Exception($exc->getMessage(), $exc->getCode(), $exc);
        }
    }

}