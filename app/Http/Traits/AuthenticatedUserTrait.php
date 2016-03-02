<?php namespace App\Http\Traits;

use JWTAuth;
use App\Http\Helpers\APIResponse;

trait AuthenticatedUserTrait {

    /**
     *
     * @var mixed null | App\User 
     */
    public $user = null;

    /**
     *  Getting authenticated user from the token provided in the headers
     * 
     * @return json
     */
    public function getAuthenticatedUser()
    {
        try {
            $this->user = JWTAuth::parseToken()->authenticate();
            if (!$this->user) {
                return response()->json(['user_not_found'], 404);
            }
            if (0 == $this->user->is_active) {
                APIResponse::$message['error'] = 'You are account is inactive kindly contact with the administrator';
                APIResponse::$status = 401;
            } else {
                $token = JWTAuth::getToken();
                APIResponse::$data = [
                    'first_name' => $this->user->fname,
                    'last_name' => $this->user->lname,
                    'email' => $this->user->email,
                    'access_token' => $token->__toString()
                ];
            }
        } catch (Tymon\JWTAuth\Exceptions\TokenExpiredException $e) {
            APIResponse::handleException($e);
        } catch (Tymon\JWTAuth\Exceptions\TokenInvalidException $e) {
            APIResponse::handleException($e);
        } catch (Tymon\JWTAuth\Exceptions\JWTException $e) {
            APIResponse::handleException($e);
        }
        // the token is valid and we have found the user via the sub claim
        return APIResponse::sendResponse();
    }

}
