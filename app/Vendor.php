<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Vendor extends Model
{
    /**
     * @var string
     */
    protected $table = 'vendors';
    /**
     * @var array
     */
    protected $guarded = ['id','is_processed','user_id','area_id','billing_info_id','bank_detail_id'];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function user(){
        return $this->belongsTo('App\User');
    }

    public function billingInfo(){
        return $this->hasOne('App\Billing','vendor_id');
    }
}
