<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class AvailableFacility extends Model
{
    /**
     * @var string
     */
    protected $table = 'available_facilities';

    protected $guarded = ['id'];

    /**
     * Facilities owned by Vendor
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function vendor(){
        return $this->belongsTo('App\Vendor','id');
    }
}
