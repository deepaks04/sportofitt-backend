<?php
namespace App;

use Illuminate\Database\Eloquent\Model;

class AvailableFacility extends Model
{

    /**
     *
     * @var string
     */
    protected $table = 'available_facilities';

    protected $fillable = [
        'name','description','sub_category_id','vendor_id','is_active','slots','cancellation_before_24hrs','cancellation_after_24hrs','created_at','updated_at'
    ];

    /**
     * Facilities owned by Vendor
     * 
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function vendor()
    {
        return $this->belongsTo('App\Vendor', 'id');
    }

    public function packageType()
    {
        return $this->hasMany('App\SessionPackage');
    }
}
