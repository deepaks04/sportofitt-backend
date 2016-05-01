<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class FacilityImages extends Model
{

    protected $table = 'facility_images';
    
    public function getImagesById($facilityId)
    {
        $images = array();
        try {
            $images = self::where('available_facility_id','=',$facilityId)->get();
        } catch (\Exception $ex) {
            throw new \Exception($ex);
        }
        
        return $images;
    }
}