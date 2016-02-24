<?php namespace App;

use Illuminate\Database\Eloquent\Model;

class SessionBooking extends Model {

    protected $table = 'session_bookings';
    protected $guarded = ['id'];

    /**
     * Getting facility details 
     * 
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function facility()
    {
        return $this->belongsTo('App\AvailableFacility', 'available_facility_id');
    }

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
        return self::select('session_bookings.*', 'name', 'description', 'image')
                        ->join('available_facilities', 'session_bookings.available_facility_id', '=', 'available_facilities.id')
                        ->where('user_id', '=', $userId)
                        ->take($limit)
                        ->skip($offset)
                        ->get();
    }

    /**
     * Finding session booking details by id
     * 
     * @param integer $id
     * @return App\SessionBooking
     */
    public function findBookingDetails($id)
    {
        $fields = array('id', 'title', 'booked_or_blocked', 'day', 'startsAt',
            'endsAt', 'day', 'peak', 'off_peak', 'price', 'discount',
            'final_price', 'multiple_session_id', 'opening_hour_id',
            'user_id', 'available_facility_id', 'is_active');
        return self::select($fields)
                        ->where('session_bookings.id', '=', $id)
                        ->first();
    }

}
