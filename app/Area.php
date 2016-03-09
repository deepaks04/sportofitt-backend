<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Area extends Model
{

    protected $table = 'areas';

    /**
     *  Get all areas by city id 
     * 
     * @return Area
     */
    public function cities()
    {
        return $this->belongsTo('App\City');
    }
    
    /**
     * 
     * @param integer $id
     * @return mixed App\Area| boolean
     */
    public static function getAreaById($id)
    {
        $area = self::find($id);
        if (!empty($area) && $area->id > 0) {
            return $area;
        }
        
        return false;
    }

}