<?php

namespace App\Http\Middleware;

use App\Http\Helpers\APIResponse;
use Tymon\JWTAuth\Middleware\GetUserFromToken;
use Tymon\JWTAuth\Exceptions\TokenExpiredException;
use Tymon\JWTAuth\Exceptions\JWTException;

class UserFromToken extends GetUserFromToken
{

    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, \Closure $next)
    {           
        
        if (!$token = $this->auth->setRequest($request)->getToken()) {
            APIResponse::$message['error'] = 'token not provided';
            APIResponse::$status = 400;
            APIResponse::$isError = true;
            return APIResponse::sendResponse();
        }

        try {
            $user = $this->auth->authenticate($token);
        } catch (TokenExpiredException $e) {
            APIResponse::$message['error'] = 'Token expired kindly log in once again';
            APIResponse::$status = $e->getStatusCode();
            APIResponse::$isError = true;
            return APIResponse::sendResponse();
        } catch (JWTException $e) {
            APIResponse::$message['error'] = 'Token Invalid';
            APIResponse::$status = $e->getStatusCode();
            APIResponse::$isError = true;
            return APIResponse::sendResponse();
        }

        if (!$user) {
            APIResponse::$message['error'] = 'user not  found';
            APIResponse::$status = 404;
            APIResponse::$isError = true;
            return APIResponse::sendResponse();
        }

        $this->events->fire('tymon.jwt.valid', $user);

        return $next($request);
    }

}