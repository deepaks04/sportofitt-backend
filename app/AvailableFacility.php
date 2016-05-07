<?php namespace App;

use App\SessionPackage;
use Illuminate\Database\Eloquent\Model;

class AvailableFacility extends Model {

    /**
     *
     * @var string
     */
    protected $table = 'available_facilities';

    /**
     *
     * @var array
     */
    protected $guarded = ['id'];

    /**
     *
     * @var array
     */
    protected $hidden = ['created_at', 'updated_at'];

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
     * Facility has multiple images
     * 
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function images()
    {
        return $this->hasMany('App\FacilityImages', 'available_facility_id');
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
    
    public function sessions()
    {
        return $this->hasMany('App\MultipleSession');
    }
    
    /**
     * Package Types
     * 
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function openingHours()
    {
        return $this->hasMany('App\OpeningHour');
    }    
    
    /**
     * Getting sub category
     * 
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function subCategory()
    {
        return $this->belongsTo('App\SubCategory', 'sub_category_id');
    }

    /**
     * Get available facilities.
     * 
     * @param array $where
     * @param array $orderBy
     * @param integer $limit
     * @param integer $offset
     * @return App\AvailableFacility
     */
    public function getFacilities(array $where = array('is_active', '=', 1), array $orderBy = array('available_facilities.created_at', 'DESC'), $limit = 10, $offset = 0)
    {
        $fields = array('available_facilities.id', 'available_facilities.name', 'image', 'description', 'sub_category_id',
            'vendor_id', 'area_id', 'pincode', 'slots', 'cancellation_before_24hrs',
            'cancellation_after_24hrs', 'off_peak_hour_price', 'peak_hour_price',
            'is_featured', 'root_categories.name as rootCategory', 'root_categories.slug as rootCategorySlug');
        $facilities = self::with(['vendor', 'subcategory'])
                ->select($fields)
                ->join('root_categories', 'root_categories.id', '=', 'available_facilities.root_category_id')
                ->where('is_active', '=', '1');

        if ($where && 3 == count($where)) {
            $facilities->where($where[0], $where[1], $where[2]);
        }

        if (2 == count($orderBy)) {
            $facilities->orderBy($orderBy[0], $orderBy[1]);
        }

        return $facilities->take($limit)->skip($offset)->get();
    }
    
    /**
     * Get different packages according to the facility
     * 
     * @return App\SessionPackages
     */
    public function getFacilityPackages()
    {
        $packages = $this->packageType()
                         ->select('session_package.id','package_type_id','name','description','month','actual_price','discount','is_peak')
                         ->join('package_child','session_package.id','=','package_child.session_package_id')
                         ->where('package_type_id','=',\DB::raw(1))
                         ->get();
        
        return $packages;
    }
    
    /**
     * Get multiple sessions according to the facility
     * 
     * @return @return App\MultipleSessions
     */
    public function getFacilitySessions()
    {
        $sessions = $this->sessions()
                         ->select('id','peak','price','off_peak','discount')
                         ->where('is_active','=',\DB::raw(1))
                         ->get();
        
        return $sessions;
    }
    
    /**
     * Get opening hours according to the facility
     * @params $day number of day to get opening hours
     * @params $isPeak is it peak or off peak hour
     * 
     * @return App\OpeningHours
     */
    public function getOpenigHoursOfFacility($day = null, $isPeak = null)
    {
        $query = $this->openingHours()
                      ->select('id','is_peak','day','start','end')
                      ->where('opening_hours.is_active','=',\DB::raw(1));
        if(!empty($day)) {
            $query->where('opening_hours.day','=',$day);
        }
        
        if($isPeak != null) {
           $query->where('opening_hours.is_peak','=',(int)$isPeak);     
        }
        
        
        return $query->orderBy('opening_hours.day','ASC')->get();
   }
    
   /**
    * Get facility details according to the facility id
    * 
    * @param integer $facilityId
    * @return App\AvailableFacitlity
    * @throws \Exception
    */
    public function getFacilityDetails($facilityId)
    {
        try {
            return self::find($facilityId);
        } catch (\Exception $ex) {
            throw new \Exception($ex);
        }
    }
    
    /**
     * Get session according to the facilityid 
     * 
     * @param integer $facilityId
     * @return App\SessionPackage
     * @throws \Exception
     */
    public function getSession($facilityId)
    {
        try {
           return SessionPackage::where('available_facility_id','=',$facilityId)
                                ->where('package_type_id','=',\DB::raw(2))
                                ->where('is_active','=',\DB::raw(1))
                                ->first();
        } catch (\Exception $ex) {
            throw new \Exception($ex);
        }        
    }
    
    /**
     * Get vendor images
     * 
     * @return array
     * @throws Exception
     */
    public function getFacilityImages()
    {
        try {
            $imagesArray = array();
            $images = $this->images()->get();
            if (!empty($images) && $images->count() > 0) {
                foreach ($images as $image) {
                    $imagesArray[] = \URL::asset(env('VENDOR_FILE_UPLOAD') . sha1($this->vendor->user_id) . "/" . "facility_images/" . $image->image_name);
                }
            }

            return $imagesArray;
        } catch (Exception $ex) {
            throw new Exception($ex);
        }
    }
}