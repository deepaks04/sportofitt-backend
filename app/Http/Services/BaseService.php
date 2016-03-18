<?php

namespace App\Http\Services;

use Illuminate\Foundation\Bus\DispatchesJobs;

abstract class BaseService
{

    use DispatchesJobs;

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

    /**
     *
     * @var mixed NULL|\App\User
     */
    protected $user = null;

    public function __construct()
    {
        $user = \Request::user();
        if (!empty($user) && $user->id > 0) {
            $this->user = $user;
        }
    }

    /**
     * Get current user.
     * 
     * @return App\User
     */
    public function getUser()
    {
        return $this->user;
    }

}