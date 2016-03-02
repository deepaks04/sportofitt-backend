<?php namespace App\Http\Services;

use JWTAuth;
use App\Http\Traits\AuthenticatedUserTrait;
use Illuminate\Foundation\Bus\DispatchesJobs;

abstract class BaseService {

    use DispatchesJobs,
        AuthenticatedUserTrait;

    /**
     *
     * @var integer
     */
    protected $limit = 10;

    /**
     *
     * @var integer
     */
    protected $offset = 0;

    public function __construct()
    {
        try {
            $this->getAuthenticatedUser();
        } catch (Exception $exception) {
            throw new Exception($exception->getMessage(), $exception->getStatusCode(), $exception);
        }
    }

}
