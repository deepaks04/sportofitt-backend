<?php
namespace App;

use Illuminate\Database\Eloquent\Model;

class VendorImages extends Model
{

    protected $table = 'vendor_images';

    /**
     * images owned by vendor
     * 
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function vendor()
    {
        return $this->belongsTo('App\Bank', 'id');
    }
}
