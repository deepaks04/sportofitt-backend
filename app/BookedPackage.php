<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class BookedPackage extends Model
{

    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'booked_packages';


    public function getBookedPackageDetails($bookedId)
    {
        return self::find($bookedId);
    }
}