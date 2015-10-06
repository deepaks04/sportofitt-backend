<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class PackageType extends Model
{
    protected $table = 'package_types';

    public function package(){
        return $this->belongsTo('App\SessionPackage');
    }
}
