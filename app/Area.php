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
    public  function cities()
    {
        return belongsTo('App\City');
    }
}
