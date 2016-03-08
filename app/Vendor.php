<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Vendor extends Model
{

    /**
     *
     * @var string
     */
    protected $table = 'vendors';

    /**
     *
     * @var array
     */
    protected $guarded = [
        'id',
        'is_processed',
        'user_id',
        'area_id',
        'billing_info_id',
        'bank_detail_id'
    ];
    protected $hidden = ['created_at', 'updated_at'];

    /**
     * vendors belongs to user
     * 
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function user()
    {
        return $this->belongsTo('App\User');
    }

    /**
     * vendor has billing details
     * 
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function billingInfo()
    {
        return $this->hasOne('App\Billing', 'vendor_id');
    }

    /**
     * vendor has bank details
     * 
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function bankInfo()
    {
        return $this->hasOne('App\Bank', 'vendor_id');
    }

    /**
     * vendor has multiple images
     * 
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function images()
    {
        return $this->hasMany('App\VendorImages', 'vendor_id');
    }

    /**
     * vendor have many facilities
     * 
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function facility()
    {
        return $this->hasMany('App\AvailableFacility', 'vendor_id');
    }

    public function getVendorsAccordingToLatLong($squareLatLong, $latitude, $longitude)
    {
        $query = self::select('id')
                ->where('latitude', '<=', $squareLatLong['latNorth'])
                ->where('latitude', '>=', $squareLatLong['latSouth'])
                ->where('longitude', '<=', $squareLatLong['lonEast'])
                ->where('longitude', '>=', $squareLatLong['lonWest'])
                ->where('latitude', '!=', $latitude)
                ->where('longitude', '!=', $longitude)
                ->where('is_active', '=', \DB::raw(1));
                //->get();
        echo $query->toSql();die;
//SELECT * FROM zipcodedistance 
//WHERE (latitude <= $latN AND latitude >= $latS AND longitude <= $lonE AND longitude >= $lonW) AND (latitude != $lat1 AND longitude != $lon1) AND city != '' ORDER BY state, city, latitude, longitude        
    }

}