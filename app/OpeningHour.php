<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class OpeningHour extends Model
{
    protected $table = 'opening_hours';

    protected $guarded = ['id'];

    public function type(){
        return $this->belongsTo('App\SessionPackage');
    }
}
