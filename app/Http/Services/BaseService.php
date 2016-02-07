<?php

namespace App\Http\Services;

use JWTAuth;

abstract class BaseService
{

    public static $token = null;
    
    /**
     *  Getting autheticated user from access token
     * 
     * @return App\user
     */
    public function getAuthenticatedUser()
    {
        try {
            $user = JWTAuth::parseToken()->authenticate();
            if (!$user) {
                return response()->json(['user_not_found'], 404);
            }
        } catch (Tymon\JWTAuth\Exceptions\TokenExpiredException $e) {
            APIResponse::$message['error'] = 'token_expired';
            APIResponse::$status = $e->getStatusCode();
        } catch (Tymon\JWTAuth\Exceptions\TokenInvalidException $e) {
            APIResponse::$message['error'] = 'token_invalid';
            APIResponse::$status = $e->getStatusCode();
        } catch (Tymon\JWTAuth\Exceptions\JWTException $e) {
            APIResponse::$message['error'] = 'token_absent';
            APIResponse::$status = $e->getStatusCode();
        }
        
        return $user;
    }

}