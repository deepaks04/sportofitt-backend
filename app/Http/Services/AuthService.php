<?php

namespace App\Http\Services;

use App\User;
use App\Status;
use App\Role;
use JWTAuth;
use Tymon\JWTAuth\Exceptions\JWTException;
use Illuminate\Http\Request;
use App\Http\Helpers\APIResponse;
use Illuminate\Foundation\Auth\ThrottlesLogins;
use Illuminate\Foundation\Auth\AuthenticatesAndRegistersUsers;
use App\Jobs\SendWelcomeEmail;
use Illuminate\Foundation\Bus\DispatchesJobs;

class AuthService
{
    /*
      |--------------------------------------------------------------------------
      | Registration & Login Controller
      |--------------------------------------------------------------------------
      |
      | This controller handles the registration of new users, as well as the
      | authentication of existing users. By default, this controller uses
      | a simple trait to add these behaviors. Why don't you explore it?
      |
     */

use AuthenticatesAndRegistersUsers,
    ThrottlesLogins,
    DispatchesJobs;

    /**
     * Where to redirect users after login / registration.
     *
     * @var string
     */
    protected $redirectTo = '/';

    /**
     * Handle a registration request for the application.
     * 
     * @param array $data
     * @return mixed (null | App\User)
     */
    public function register($data)
    {
        try {
            return $this->create($data);
        } catch (Exception $exception) {
            APIResponse::handleException($exception);
        }

        return null;
    }

    /**
     * Handle a login request to the application.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return mixed (null | App\User)
     */
    public function login(Request $request)
    {
        try {
            $credentials = $this->getCredentials($request);
            try {
                // attempt to verify the credentials and create a token for the user
                if (!$token = JWTAuth::attempt($credentials)) {
                    APIResponse::$status = 401;
                    APIResponse::$message['error'] = 'invalid credentials';
                }
            } catch (JWTException $e) {
                APIResponse::$status = 500;
                APIResponse::$message['error'] = 'could not create token';
            }

            return compact('token');
        } catch (Exception $exception) {
            APIResponse::handleException($exception);
        }

        return null;
    }

    /**
     * Create a new user instance after a valid registration.
     *
     * @param  array  $data
     * @return User
     */
    protected function create(array $data)
    {

        try {
            $remember_token = csrf_token();
            $activeStatus = Status::where('slug', '=', 'Active')->first();
            $role = Role::where("slug", "=", "customer")->first();
            $user = new User();
            $user->fname = $data['first_name'];
            $user->lname = $data['last_name'];
            $user->email = $data['email'];
            $user->password = bcrypt($data['password']);
            $user->status_id = $activeStatus->id;
            $user->role_id = $role->id;
            $user->remember_token = $remember_token;
            $user->save();


            // Adding job to queue for processing to the mail will be send via the queue
            $job = (new SendWelcomeEmail($user))->delay(60);
            $this->dispatch($job);

            return $user;
        } catch (Exception $exception) {
            APIResponse::handleException($exception);
        }
    }

}