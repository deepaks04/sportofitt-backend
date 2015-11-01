<?php
namespace App;

use Illuminate\Database\Eloquent\Model;

class Bank extends Model
{

    protected $table = 'bank_details';

    /**
     *
     * @var array
     */
    protected $guarded = [
        'id',
        'vendor_id'
    ];

    /**
     * Belongs to one vendor only
     * 
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function vendor()
    {
        return $this->belongsTo('App\Vendor', 'id');
    }
}
