<?php

namespace App\Http\Services;

use App\Vendor;
use App\AvailableFacility;
use App\Http\Services\BaseService;

class IndexService extends BaseService
{

    /**
     * Get available facilities.
     * 
     * @param array $where
     * @param array $orderBy
     * @return App\AvailableFacility
     */
    public function getFacilities(array $where = array('is_active', '=', 1), array $orderBy = array('available_facilities.created_at', 'DESC'))
    {
        $availableFacilities = new AvailableFacility();
        return $availableFacilities->getFacilities($where, $orderBy, $this->limit, $this->offset);
    }

    /**
     * Get vendors depending upon the requested parameters
     * 
     * @param array $requestData
     * @return mixed array|collection
     */
    public function getVendors(array $requestData)
    {
        try {
            $lat =  (!empty($requestData['lat']))?$requestData['lat']:null;
            $long =  (!empty($requestData['long']))?$requestData['long']:null;
            $areaId =  (!empty($requestData['area_id']))?$requestData['area_id']:null;
            $category =  (!empty($requestData['category']))?$requestData['category']:null;

            $vendor = new Vendor();
            $vendors = $vendor->searchVendors($lat, $long, $areaId, $category);
            if($vendors) {
                foreach($vendors as $vendor) {
                    $vendor->type = (1 == $vendor->type)?'Venue':'Coaching';
                    $vendor->gallery = $vendor->getVendorImages();
                    $vendor->features = $vendor->getVendorsFeatures();
                    $vendor->color = '';
                    $vendor->item_specific = new \stdClass();
                    $vendor->rating = 0;
                    $vendor->type_icon = '';
                    $vendor->url = '';
                }                
            }
            
            return $vendors;
            
        } catch (Exception $ex) {
            throw new Exception($ex);
        }
    }
    
/**
     * Getting details of the search result
     * 
     * @param integer $vendor_id
     * @return array
     */
    public function getVendorDetails($vendor_id)
    {
        $result = array();
        $vendorObject = new Vendor();
        $vendor = $vendorObject->getVendorDetailsById($vendor_id);
        if ($vendor) {
            $result['vendor'] = $vendor;
            $result['vendor']['user'] = $vendor->user()->select('fname', 'lname', 'username', 'profile_picture')->first();
            $result['vendor']['facilities'] = $vendor->facility()
                    ->select('available_facilities.id', 'available_facilities.name')
                    ->join('sub_categories', 'available_facilities.sub_category_id', '=', 'sub_categories.id')
                    ->join('root_categories', 'available_facilities.root_category_id', '=', 'root_categories.id')
                    ->get();
            $vendor->images = $vendor->getVendorImages();
        }

        return $result;
    }

}