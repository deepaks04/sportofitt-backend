<?php

namespace App;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

class SessionPackage extends Model
{

    protected $table = 'session_package';
    protected $guarded = [
        'id'
    ];

    public function type()
    {
        return $this->hasOne('App\PackageType');
    }

    public function ChildPackage()
    {
        return $this->hasOne('App\SessionPackageChild');
    }

    public function ChildOpeningHours()
    {
        return $this->hasOne('App\OpeningHour');
    }

    public function facility()
    {
        return $this->belongsTo('App\AvailableFacility');
    }

    /**
     * Get package details according to the package id 
     * 
     * @param integer $packageId
     * @return App\SessionPackage
     * @throws Exception
     */
    public function getPackageDetails($packageId)
    {
        try {
            $package = self::find($packageId);
            if (!empty($package) && $package->id > 0) {
                $package->ChildPackage;
            }

            return $package;
        } catch (Exception $exception) {
            throw new Exception($exception->getMessage(), $exception->getCode(), $exception);
        }
    }

    /**
     * Booking a backage for user according to the user id 
     * 
     * @param type $userId
     * @return \App\BookedPackage|boolean
     */
    public function bookPackageForUser($userId)
    {
        $bookPackage = new BookedPackage();

        $bookPackage->session_package_id = $this->id;
        $bookPackage->name = $this->name;
        $bookPackage->description = $this->description;
        $bookPackage->month = $this->ChildPackage->month;
        $bookPackage->booking_amount = $this->ChildPackage->actual_price;
        $bookPackage->discount = $this->ChildPackage->discount;
        $bookPackage->final_amount = $this->ChildPackage->booking_amount;
        if (!empty($this->ChildPackage->discount) && $this->ChildPackage->discount > 0) {
            $discountAmount = $this->ChildPackage->actual_price * ($this->ChildPackage->discount / 100);
            $bookPackage->final_amount = $this->ChildPackage->actual_price - $discountAmount;
        }

        $bookPackage->user_id = $userId;
        $bookPackage->available_facility_id = $this->available_facility_id;

        $today = Carbon::now();
        $bookPackage->start_at = $today->__toString();
        $bookPackage->end_at = $today->addMonths($bookPackage->month)->__toString();

        $booked = $bookPackage->save();
        if ($booked) {
            return $bookPackage;
        }

        return false;
    }

}