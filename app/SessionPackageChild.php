<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class SessionPackageChild extends Model
{
    protected $table = 'session_package_child';

    protected $guarded = ['id'];

    public function type(){
        return $this->belongsTo('App\SessionPackage');
    }
}
