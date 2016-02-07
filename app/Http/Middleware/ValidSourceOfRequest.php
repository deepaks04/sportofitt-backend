<?php

namespace App\Http\Middleware;

use Closure;
use Config;
use App\Http\Helpers\APIResponse;

class ValidSourceOfRequest {

    /**
     * Handle an incoming request.and verify it is from the valid source that is from the server where the 
     * code has been deployed.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next) {
        $keyFromHeader = $request->header('api-key');
        $apiKey = Config::get('app.key');
        if ($keyFromHeader !== $apiKey) {
            APIResponse::$status = 401;
            APIResponse::$message['error'] = 'Unauthorized! This request is not valid. API Key is missing';
            return APIResponse::sendResponse();
        }

        return $next($request);
    }

}