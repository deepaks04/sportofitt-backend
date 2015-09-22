<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Billing extends Model
{
    protected $table = 'billing_info';
    /**
     * @var array
     */
    protected $guarded = ['id','vendor_id'];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function vendor(){
        return $this->belongsTo('App\Vendor','id');
    }
}
