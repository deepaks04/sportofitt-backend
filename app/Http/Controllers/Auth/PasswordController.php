<?php
namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\ChangePasswordRequest;
use App\User;
use Illuminate\Foundation\Auth\ResetsPasswords;
use Auth;
use Illuminate\Support\Facades\Hash;

class PasswordController extends Controller
{
    /*
     * |--------------------------------------------------------------------------
     * | Password Reset Controller
     * |--------------------------------------------------------------------------
     * |
     * | This controller is responsible for handling password reset requests
     * | and uses a simple trait to include this behavior. You're free to
     * | explore this trait and override any methods you wish to tweak.
     * |
     */
    
    use ResetsPasswords;

    /**
     * Create a new password controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest', [
            'except' => [
                'change'
            ]
        ]);
        $this->middleware('auth', [
            'only' => [
                'change'
            ]
        ]);
    }

    /**
     * @param ChangePasswordRequest $request
     *
     * @return \Illuminate\Contracts\Routing\ResponseFactory|\Symfony\Component\HttpFoundation\Response
     */
    public function change(ChangePasswordRequest $request)
    {
        $status = 200;
        $password = $request->all();
        unset($password['_method']);
        $user = Auth::user();
        if (Hash::check($password['old'], $user->password)) {
            $message = "Password Updated Successfully";
            $user->update(array(
                'password' => bcrypt($password['new'])
            ));
        } else {
            $message = "Old password is not matched, update not allowed";
        }
        $response = [
            "message" => $message
        ];
        return response($response, $status);
    }
}
