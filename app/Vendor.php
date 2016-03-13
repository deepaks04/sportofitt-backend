<?php

namespace App;

use Config;
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

    /**
     * Get all vendors available with in the specific distance of mile for 
     * particular area and category.
     * 
     * @param mixed null | float $latitude
     * @param mixed null | $longitude
     * @param mixed integer | null $areaId
     * @param mixed integer | null $category
     * @param integer $offset
     * @param integer $limit
     * @return array
     */
    public function searchVendors($latitude = null, $longitude = null, $areaId = null, $category = null, $offset = 0, $limit = 10)
    {
        $sql = "vendors.id AS id,rt.name as category,vendors.business_name AS title,
            vendors.address AS location,vendors.latitude,vendors.longitude,
            af.is_venue as type,vendors.description,vendors.postcode,
            u.fname AS firstName,u.lname AS lastName,u.profile_picture,
            af.name as facilityName,af.image as facilityImage,af.is_featured AS featured";
        if (null != $latitude && null != $longitude) {
            $sql .= ", ( 3959 * acos( cos( radians($latitude) ) 
        * cos( radians( latitude ) ) 
        * cos( radians( longitude ) - radians($longitude)) + sin( radians($latitude) ) 
        * sin( radians( latitude  ) ) ) ) AS distance";
        }

        $query = self::select(\DB::raw($sql))
                ->join('users AS u', 'vendors.user_id', '=', 'u.id')
                ->join('available_facilities AS af', 'vendors.id', '=', 'af.vendor_id')
                ->join('root_categories AS rt', 'rt.id', '=', 'af.root_category_id')
                // ->where('vendors.is_processed', '=', \DB::raw(1))
                ->where('af.is_active', '=', \DB::raw(1));
        if (null != $areaId) {
            $query->where('vendors.area_id', '=', $areaId);
        }

        if (null != $category) {
            $query->where('af.sub_category_id', '=', $category);
        }

        if (null != $latitude && null != $longitude) {
            $query->having("distance", "<=", Config::get('constants.distanceInMiles'))
                    ->orderBy('distance', 'ASC');
        } else {
            $query->orderBy('vendors.id','DESC');
        }

        $result = $query->skip($offset)->take($limit)->get();
        if (!empty($result) && $result->count() > 0) {
            return $result;
        }

        return array();
    }

    /**
     * Get vendor details according to Id
     * 
     * @param integer  $vendorId
     * @return mixed App\Vendor | boolean
     */
    public function getVendorDetailsById($vendorId)
    {
        if ($vendorId) {
            return self::find($vendorId);
        }

        return false;
    }

    /**
     * Get vendor images
     * 
     * @return array
     * @throws Exception
     */
    public function getVendorImages()
    {
        try {
            $imagesArray = array();
            $images = $this->images()->get();
            if (!empty($images) && $images->count() > 0) {
                foreach ($images as $image) {
                    $fileLocation = public_path(env('VENDOR_FILE_UPLOAD') . sha1($this->id) . "/" . "extra_images/".$image->image_name);
                    if(file_exists($fileLocation)) {
                        $imagesArray[] = \URL::asset(env('VENDOR_FILE_UPLOAD') . sha1($this->id) . "/" . "extra_images/" . $image->image_name);
                    }
                }
            }

            return $imagesArray;
            
        } catch (Exception $ex) {
            throw new Exception($ex);
        }
    }
    
    /**
     * Get features according to vendor
     * 
     * @return array
     */
    public function getVendorsFeatures()
    {
        return array();
    }

}