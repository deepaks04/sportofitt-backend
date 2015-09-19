<?php

namespace App\Http\Controllers\Auth;

use App\User;
use Validator;
use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\ThrottlesLogins;
use Illuminate\Foundation\Auth\AuthenticatesAndRegistersUsers;
use Auth;
use App\Http\Requests\LoginUserRequest;
use App\Role;
use App\Status;

class AuthController extends Controller
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

    use AuthenticatesAndRegistersUsers, ThrottlesLogins;

    /**
     * Create a new authentication controller instance.
     *
     * @return void
     */
    public function __construct()
    {
      //  $this->middleware('guest', ['except' => 'logout']);
    }

    /**
     * Get a validator for an incoming registration request.
     *
     * @param  array  $data
     * @return \Illuminate\Contracts\Validation\Validator
     */
    protected function validator(array $data)
    {
        return Validator::make($data, [
            'name' => 'required|max:255',
            'email' => 'required|email|max:255|unique:users',
            'password' => 'required|confirmed|min:6',
        ]);
    }

    /**
     * Create a new user instance after a valid registration.
     *
     * @param  array  $data
     * @return User
     */
    protected function create(array $data)
    {
        return User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => bcrypt($data['password']),
        ]);
    }

    protected function authenticate(LoginUserRequest $request){
        $user = User::where('email', $request->email)->first();
        if($user == NULL){
            $status =404;
            $response = [
                "message" => "Sorry!! Incorrect email or password",
            ];
        }elseif($user->is_active == 0){
            $status =401;
            $response = [
                "message" => "Please confirm your email id first",
            ];
        }elseif (Auth::attempt(['email' => $request->email, 'password' => $request->password])) {
            // Authentication passed...
            $status =200;
            $user = Auth::User();
            $role = Role::find($user->role_id);
            $currentStatus = Status::find($user->status_id);
            $user['role'] = $role->slug;
            $user['status'] = $currentStatus->slug;
            $response = [
                "message" => "Login Successful",
                "user" =>$user
            ];
        }else{
            $status =404;
            $response = [
                "message" => "Sorry!! Incorrect email or password",
            ];
        }
        return response($response,$status);
    }

    protected function logout(){
        Auth::logout();
        $status =200;
        $response = [
            "message" => "Logout Successful",
        ];
        return response($response,$status);
    }
}
