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
     * vendors belongs to user
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function user(){
        return $this->belongsTo('App\User');
    }

    /**
     * vendor has billing details
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function billingInfo(){
        return $this->hasOne('App\Billing','vendor_id');
    }

    /**
     * vendor has bank details
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function bankInfo(){
        return $this->hasOne('App\Bank','vendor_id');
    }

    /**
     * vendor has multiple images
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function images(){
        return $this->hasMany('App\VendorImages','vendor_id');
    }

    /**
     * vendor have many facilities
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function facility(){
        return $this->hasMany('App\AvailableFacility','vendor_id');
    }
}
