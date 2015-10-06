<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class SessionPackage extends Model
{
    protected $table = 'session_package';

    protected $guarded = ['id'];

    public function type(){
        return $this->hasOne('App\PackageType');
    }

    public function Child(){
        return $this->hasOne('App\SessionPackageChild')->orderBy('created_at','DESC')->first();
    }

    public function facility(){
        return $this->belongsTo('App\AvailableFacility');
    }
}
