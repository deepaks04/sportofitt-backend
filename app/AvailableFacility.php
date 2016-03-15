<?php namespace App;

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
    
    
    public function getFacilityPackages()
    {
        $packages = $this->packageType()
                         ->select('session_package.id','package_type_id','name','description','month','actual_price','discount','is_peak')
                         ->join('package_child','session_package.id','=','package_child.session_package_id')
                         ->where('package_type_id','=',\DB::raw(1))
                         ->get();
        return $packages;
    }
    
    public function getFacilitySessions()
    {
        $sessions = $this->packageType()
                         ->select('id','package_type_id','name','description','duration')
                         ->where('package_type_id','=',\DB::raw(2))
                         ->where('package_type_id','=',\DB::raw(2))
                         ->get();
        return $sessions;
    }
    
    public function getOpenigHoursOfFacility()
    {
        $packages = $this->packageType()
                         ->select('session_package.id','package_type_id','name','description','month','actual_price','discount','is_peak')
                         ->join('opening_hours','session_package.id','=','package_child.session_package_id')
                         ->where('package_type_id','=',\DB::raw(1))
                         ->where('opening_hours.is_active','=',\DB::raw(1))
                         ->get();
        return $packages;
    }
}
