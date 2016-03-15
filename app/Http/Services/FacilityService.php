<?php

namespace App\Http\Services;

use App\Vendor;
use App\OpeningHour;
use App\AvailableFacility;
use App\Http\Services\BaseService;

class FacilityService extends BaseService
{

    /**
     * Getting details of the search result
     * 
     * @param array $data
     * @return array
     */
    public function getVendorDetails(array $data)
    {
        $result = array();
        $vendorObject = new Vendor();
        $vendor = $vendorObject->getVendorDetailsById($data['vendor_id']);
        if ($vendor) {
            $result['vendor'] = $vendor;
            $result['user'] = $vendor->user()->select('fname', 'lname', 'username', 'profile_picture')->get();
            $result['facilities'] = $vendor->facility()
                    ->select('available_facilities.*', 'sub_categories.name AS subCategoryName', 'sub_categories.slug AS subCategorySlug', 'root_categories.name AS rootCategoryName', 'root_categories.slug AS rootCategorySlug')
                    ->join('sub_categories', 'available_facilities.sub_category_id', '=', 'sub_categories.id')
                    ->join('root_categories', 'available_facilities.root_category_id', '=', 'root_categories.id')
                    ->get();
            $vendor->images;
        }

        return $result;
    }
    
    
    public function getBookingInformation($facilityId)
    {
        try {
            $facility = AvailableFacility::find($facilityId);
            $openingHours = $this->getOpenigHoursOfFacility($facilityId);
            $packages = $facility->getFacilityPackages($facilityId);
            $sessions = $facility->getFacilitySessions($facilityId);
        } catch (Exception $ex) {
            throw new Exception($ex);
        }
    }
    
    public function getOpenigHoursOfFacility($facilityId)
    {
        $openingHours = new OpeningHour();
        
    }
}