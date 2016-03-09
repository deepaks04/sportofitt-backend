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
        if (!empty($requestData['lat']) && !empty($requestData['long'])) {
            $vendor = new Vendor();
            return $vendor->searchVendors($requestData['lat'], $requestData['long'], $requestData['area_id'], $requestData['category']);
        }

        return array();
    }
    
    /**
     * Getting details of the search result
     * 
     * @param array $data
     * @return array
     */
    public function getSearchRecordDetails(array $data)
    {
        $result = array();
        $vendorObject = new Vendor();
        $vendor = $vendorObject->getVendorDetailsById($data['vendor_id']);
        if ($vendor) {
            $result['vendor'] = $vendor;
            $result['user'] = $vendor->user()->select('fname','lname','username','profile_picture')->get();
            $result['facilities'] = $vendor->facility()
                                            ->select('available_facilities.*','sub_categories.name AS subCategoryName','sub_categories.slug AS subCategorySlug','root_categories.name AS rootCategoryName','root_categories.slug AS rootCategorySlug')
                                            ->join('sub_categories','available_facilities.sub_category_id','=','sub_categories.id')
                                            ->join('root_categories','available_facilities.root_category_id','=','root_categories.id')
                                            ->get();
            $vendor->images;
            
        }

        return $result;
    }

}