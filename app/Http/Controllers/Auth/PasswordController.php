<?php
namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\ChangePasswordRequest;
use App\User;
use Illuminate\Foundation\Auth\ResetsPasswords;
use Auth;
use Illuminate\Support\Facades\Hash;
use App\PasswordReset;
use Illuminate\Http\Request;
use Illuminate\Mail\Message;
use Illuminate\Support\Facades\Password;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

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
    /**
     * Display the password reset view for the given token.
     *
     * @param  string  $token
     * @return \Illuminate\Http\Response
     */
    public function getReset($token = null)
    {
        $status = 500;
        $message = "Token not found";
        $data = '';
        if (is_null($token)) {
            $status = 500;
            $message = "Token not found";
        }else{
            $resetPassword = PasswordReset::where('token',$token)->first();
            if($resetPassword!=null){
                $status = 200;
                $message = "success";
                $data = array(
                    'email' => $resetPassword->email,
                    'token' => $resetPassword->token,
                );
            }
        }
        $response = [
            'message' => $message,
            'data' => $data,
        ];
        return response($response,$status);
    }

    /**
     * Send a reset link to the given user.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function postEmail(Request $request)
    {
        $this->validate($request, ['email' => 'required|email']);

        $response = Password::sendResetLink($request->only('email'), function (Message $message) {
            $message->subject($this->getEmailSubject());
        });

        switch ($response) {
            case Password::RESET_LINK_SENT:
                $status = 200;
                $message = 'Password reset link sent successfully!! Please check your email';
                break;
            //return redirect()->back()->with('status', trans($response));

            case Password::INVALID_USER:
                $status = 401;
                $message = 'Invalid User';
                break;
            //return redirect()->back()->withErrors(['email' => trans($response)]);
        }
        $res = [
            'message' => $message,
        ];
        return response($res,$status);
    }

    /**
     * Reset the given user's password.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function postReset(Request $request)
    {
        $this->validate($request, [
            'token' => 'required',
            'email' => 'required|email',
            'password' => 'required|confirmed|min:6',
        ]);

        $credentials = $request->only(
            'email', 'password', 'password_confirmation', 'token'
        );

        $response = Password::reset($credentials, function ($user, $password) {
            $this->resetPassword($user, $password);
        });

        switch ($response) {
            case Password::PASSWORD_RESET:
                //return redirect($this->redirectPath());
                $status = 200;
                $message = 'Password reset successfully';
                break;

            default:
                $status = 401;
                $message = 'Something went wrong, password reset unsuccessful';
                break;
            /*return redirect()->back()
                        ->withInput($request->only('email'))
                        ->withErrors(['email' => trans($response)]);*/
        }
        $response = [
            'message' => $message,
        ];
        return response($response,$status);
    }
}
