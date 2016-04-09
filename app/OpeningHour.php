<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class OpeningHour extends Model
{

    protected $table = 'opening_hours';
    protected $guarded = ['id'];

    public function type()
    {
        return $this->belongsTo('App\SessionPackage');
    }

    public function getOpeningHoursByFacilityId($facilityId)
    {
        try {
            $openingHours = self::select('id', 'is_peak', 'day', 'start', 'end')
                    ->where('opening_hours.is_active', '=', \DB::raw(1))
                    ->where('opening_hours.available_facility_id', '=', $facilityId)
                    ->orderBy('opening_hours.day', 'ASC')
                    ->get();

            return $openingHours;
        } catch (\Exception $ex) {
            throw new \Exception($ex);
        }
    }

}