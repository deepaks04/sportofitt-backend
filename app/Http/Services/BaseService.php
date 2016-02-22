<?php namespace App\Http\Services;

use JWTAuth;
use Illuminate\Foundation\Bus\DispatchesJobs;

abstract class BaseService {

    use DispatchesJobs;
    
    protected $limit = 10;
    protected $offset = 0;

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
            throw new Exception('token_expired', $e->getStatusCode(), $e);
        } catch (Tymon\JWTAuth\Exceptions\TokenInvalidException $e) {
            throw new Exception('token_invalid', $e->getStatusCode(), $e);
        } catch (Tymon\JWTAuth\Exceptions\JWTException $e) {
            throw new Exception('token_absent', $e->getStatusCode(), $e);
        }

        return $user;
    }

}
