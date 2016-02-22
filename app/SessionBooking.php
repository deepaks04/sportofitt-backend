<?php namespace App;

use Illuminate\Database\Eloquent\Model;

class SessionBooking extends Model {

    protected $table = 'session_bookings';
    protected $guarded = ['id'];

    /**
     * Get user booking by id 
     * 
     * @param integer $userId
     * @param integer $offset
     * @param integer $limit
     * @return App\SessionBooking
     */
    public function getUsersBooking($userId, $offset = 0, $limit = 10)
    {
        return self::select('session_bookings.*', 'available_facilities.name', 'available_facilities.description', 'available_facilities.image')
                        ->join('available_facilities', 'session_bookings.available_facility_id', '=', 'available_facilities.id')
                        ->where('user_id', '=', $userId)
                        ->take($limit)
                        ->skip($offset)
                        ->get();
    }

}