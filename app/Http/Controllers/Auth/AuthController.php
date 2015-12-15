<?php
namespace App\Http\Controllers\Auth;

use App\Area;
use App\User;
use Validator;
use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\ThrottlesLogins;
use Illuminate\Foundation\Auth\AuthenticatesAndRegistersUsers;
use Auth;
use App\Http\Requests\LoginUserRequest;
use App\Role;
use App\Status;
use Illuminate\Support\Facades\URL;

class AuthController extends Controller
{
    /*
     * |--------------------------------------------------------------------------
     * | Registration & Login Controller
     * |--------------------------------------------------------------------------
     * |
     * | This controller handles the registration of new users, as well as the
     * | authentication of existing users. By default, this controller uses
     * | a simple trait to add these behaviors. Why don't you explore it?
     * |
     */
    
    use AuthenticatesAndRegistersUsers, ThrottlesLogins;

    /**
     * Create a new authentication controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        //$this->middleware('guest', ['except' => 'logout','authenticate']);
    }

    /**
     * Get a validator for an incoming registration request.
     *
     * @param array $data            
     * @return \Illuminate\Contracts\Validation\Validator
     */
    protected function validator(array $data)
    {
        return Validator::make($data, [
            'name' => 'required|max:255',
            'email' => 'required|email|max:255|unique:users',
            'password' => 'required|confirmed|min:6'
        ]);
    }

    /**
     * Create a new user instance after a valid registration.
     *
     * @param array $data            
     * @return User
     */
    protected function create(array $data)
    {
        return User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => bcrypt($data['password'])
        ]);
    }

    /**
     * @param LoginUserRequest $request
     *
     * @return \Illuminate\Contracts\Routing\ResponseFactory|\Symfony\Component\HttpFoundation\Response
     */
    protected function authenticate(LoginUserRequest $request)
    {
        $user = User::where('email', $request->email)->first();
        if ($user == NULL) {
            $status = 404;
            $message="Sorry!! Incorrect email or password";
            $user="";
        } elseif ($user->is_active == 0) {
            $status = 401;
            $message="Please confirm your email id first";
            $user="";
        } elseif (Auth::attempt([
            'email' => $request->email,
            'password' => $request->password
        ])) {
            // Authentication passed...
            $status = 200;
            $user = Auth::User();
            $role = Role::find($user->role_id);
            $currentStatus = Status::find($user->status_id);
            $user['role'] = $role->slug;
            
            if ($user['profile_picture'] == null) {
                $user['profile_picture'] = $user['profile_picture'];
            } else {
                if ($role->slug == "vendor") {
                    $uploadPath = URL::asset(env('VENDOR_FILE_UPLOAD'));
                }
                if ($role->slug == "customer") {
                    $uploadPath = URL::asset(env('CUSTOMER_FILE_UPLOAD'));
                }
                $userOwnDirecory = $uploadPath . "/" . sha1($user['id']) . "/" . "profile_image/";
                $user['profile_picture'] = $userOwnDirecory . $user['profile_picture'];
            }
            
            if ($role->slug == "vendor") {
                $vendor = $user->vendor()->first();
                $user['is_processed'] = (int) $vendor->is_processed;
                $area = Area::select('name')->find($vendor['area_id']);
                $vendor['area'] = $area['name'];
                $user['extra'] = $vendor;
            }
            if ($role->slug == "customer") {
                $customer = $user->customer()->first();
                $area = Area::select('name')->find($customer['area_id']);
                $customer['area'] = $area['name'];
                $user['extra'] = $customer;
            }
            $user['status'] = $currentStatus->slug;
            $message="Login Successful";

        } else {
            $status = 404;
                $message="Sorry!! Incorrect email or password";
                $user="";
        }
        $response = [
            "message" => $message,
            "user" => $user,
        ];
        return response($response, $status);
    }

    /**
     * @return \Illuminate\Contracts\Routing\ResponseFactory|\Symfony\Component\HttpFoundation\Response
     */
    protected function logout()
    {
        Auth::logout();
        $status = 200;
        $response = [
            "message" => "Logout Successful"
        ];
        return response($response, $status);
    }
}
