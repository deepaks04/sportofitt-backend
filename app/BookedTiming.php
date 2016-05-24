<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class BookedTiming extends Model
{

    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'booked_timings';
    
    public function facility()
    {
        return $this->belongsTo('App\AvailableFacility','facility_id');
    }
}