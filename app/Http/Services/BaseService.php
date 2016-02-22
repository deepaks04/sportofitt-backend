<?php namespace App\Http\Services;

use JWTAuth;
use Illuminate\Foundation\Bus\DispatchesJobs;

abstract class BaseService {

    use DispatchesJobs;

    public $user = null;
    protected $limit = 10;
    protected $offset = 0;
    
    public function __construct()
    {
        try {
            $this->getAuthenticatedUser();
        } catch (Exception $exception) {
            throw new Exception($exception->getMessage(), $exception->getStatusCode(), $exception);
        }
    }

    /**
     * Getting authenticated user from access token
     * 
     * @return App\User
     * @throws Exception
     */
    public function getAuthenticatedUser()
    {
        try {
            $this->user = JWTAuth::parseToken()->authenticate();
            if (!$this->user) {
                return response()->json(['user_not_found'], 404);
            }
        } catch (Tymon\JWTAuth\Exceptions\TokenExpiredException $e) {
            throw new Exception('token_expired', $e->getStatusCode(), $e);
        } catch (Tymon\JWTAuth\Exceptions\TokenInvalidException $e) {
            throw new Exception('token_invalid', $e->getStatusCode(), $e);
        } catch (Tymon\JWTAuth\Exceptions\JWTException $e) {
            throw new Exception('token_absent', $e->getStatusCode(), $e);
        }
    }

}
