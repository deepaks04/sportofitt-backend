<?php

namespace App\Http\Services;

use App\AvailableFacility;
use App\Http\Services\BaseService;

class FacilityService extends BaseService
{

    /**
     * Get opening hours, sessions and packages information about the facility
     * according to the facility id 
     * 
     * @param App\AvailableFacility $facility
     * @return array
     * @throws Exception
     */
    public function getSessionsAndPackages(App\AvailableFacility $facility)
    {
        try {
            $facility->openingHours = $facility->getOpenigHoursOfFacility();
            $facility->packages = $facility->getFacilityPackages();
            $facility->sessions = $facility->getFacilitySessions();

            return $facility;
        } catch (Exception $ex) {
            throw new Exception($ex);
        }
    }

    /**
     * Get facility details according to the facility id
     * 
     * @param integer $facilityId
     * @return App\AvailableFacility
     * @throws \Exception
     */
    public function getFacilityDetailsById($facilityId)
    {
        try {
            $facility = AvailableFacility::select('available_facilities.*','vendors.latitude',
                        'vendors.longitude','vendors.address','areas.name as areaName',
                        'sub_categories.name As subcategoryName','root_categories.name as rootCategoryName')
                ->join('sub_categories', 'available_facilities.sub_category_id', '=', 'sub_categories.id')
                ->join('root_categories', 'available_facilities.root_category_id', '=', 'root_categories.id')
                ->join('vendors', 'available_facilities.vendor_id', '=', 'vendors.id')
                ->join('areas', 'areas.id', '=', 'vendors.area_id')
                ->where('available_facilities.is_active', '=', \DB::raw(1))                    
                ->where('available_facilities.id', '=', $facilityId)                    
                ->first();    
            $facility->openingHours = $facility->getOpenigHoursOfFacility();
            $facility->packages = $facility->getFacilityPackages();
            $facility->sessions = $facility->getFacilitySessions();

            return $facility;
        } catch (\Exception $ex) {
            throw new \Exception($ex);
        }
    }

}