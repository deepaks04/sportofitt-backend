<?php

namespace App\Http\Services;

use App\Vendor;
use App\AvailableFacility;
use App\Http\Traits\DistanceLatLongTrait;
use App\Http\Services\BaseService;

class IndexService extends BaseService
{

    use DistanceLatLongTrait;

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

    public function getVendors($requestData)
    {
        if (!empty($requestData['lat']) && !empty($requestData['long'])) {
            //compute max and min latitudes / longitudes for search square 
            $squareLatLong = $this->getSearchSquareLatitudeLongitude($requestData['lat'], $requestData['long']);

            $vendor = new Vendor();
            $availableVendors = $vendor->getVendorsAccordingToLatLong($squareLatLong, $requestData['lat'], $requestData['long']);
        }

        return array();
    }

}