<?php namespace App;

use Illuminate\Database\Eloquent\Model;

class AvailableFacility extends Model {

    /**
     *
     * @var string
     */
    protected $table = 'available_facilities';
    protected $guarded = [
        'id'
    ];

    /**
     * Facilities owned by Vendor
     * 
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function vendor()
    {
        return $this->belongsTo('App\Vendor', 'vendor_id');
    }

    /**
     * Package Types
     * 
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function packageType()
    {
        return $this->hasMany('App\SessionPackage');
    }

    /**
     * Getting sub category
     * 
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function subCategory()
    {
        return $this->belongsTo('App\SubCategory','sub_category_id');
    }

}
