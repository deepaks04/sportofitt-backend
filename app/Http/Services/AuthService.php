<?php

namespace App\Http\Services;

use Illuminate\Support\Facades\Auth;
use App\User;
use App\Status;
use App\Role;
use Illuminate\Http\Request;
use App\Http\Helpers\APIResponse;
use App\Http\Services\BaseService;
use Illuminate\Foundation\Auth\ThrottlesLogins;
use Illuminate\Foundation\Auth\AuthenticatesAndRegistersUsers;
use Mail;

class AuthService extends BaseService {
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
    ThrottlesLogins;

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
    public function register($data) {
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
    public function login(Request $request) {
        $user = null;
        try {
            $credentials = $this->getCredentials($request);
            if (Auth::attempt($credentials)) {
                $user = Auth::user();
            } else {
                APIResponse::$message['error'] = 'Username and Password does not match';
            }
        } catch (Exception $exception) {
            APIResponse::handleException($exception);
        }

        return $user;
    }

    /**
     * Create a new user instance after a valid registration.
     *
     * @param  array  $data
     * @return User
     */
    protected function create(array $data) {

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

            /* if (!empty($user) && $user->id > 0) {
              $params = array('fname' => $user->fname,'lname' => $user->fname,'email' => $user->fname,'remember_token' => $remember_token);
              Mail::send('emails.activation', $params, function($message) use($user){
              $message->to($user->email, $user->fname)->subject('Welcome!');
              });
              } */
            return $user;
        } catch (Exception $exception) {
            APIResponse::handleException($exception);
        }
    }

}