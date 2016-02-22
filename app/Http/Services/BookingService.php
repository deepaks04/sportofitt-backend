<?php namespace App\Http\Services;

use App\SessionBooking;
use App\Http\Services\BaseService;

class BookingService extends BaseService {

    /**
     *
     * @var mixed (NULL| App\SessionBooking)
     */
    private $sessionBooking = null;

    public function __construct()
    {
        $this->sessionBooking = new SessionBooking();
    }

    /**
     * Get user bookings according to user 
     * 
     * @return mixed (NULL | App\SessionBooking)
     * @throws Exception
     */
    public function getUsersBooking()
    {
        try {
            $user = $this->getAuthenticatedUser();
            return $this->sessionBooking->getUsersBooking($user->id, $this->offset, $this->limit);
        } catch (Exception $exception) {
            throw new Exception($exception->getMessage(), $exception->getCode(), $exception);
        }
    }

}
