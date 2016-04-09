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
            $facility = AvailableFacility::select('available_facilities.*', 'sub_categories.name As subcategoryName', 'root_categories.name as rootCategoryName')
                    ->join('sub_categories', 'available_facilities.sub_category_id', '=', 'sub_categories.id')
                    ->join('root_categories', 'available_facilities.root_category_id', '=', 'root_categories.id')
                    ->join('vendors', 'available_facilities.vendor_id', '=', 'vendors.id')
                    ->where('available_facilities.is_active', '=', \DB::raw(1))
                    ->where('available_facilities.id', '=', $facilityId)
                    ->first();
            $facility->vendor = $facility->vendor()
                    ->select('vendors.*', 'areas.name as areaName')
                    ->join('areas', 'areas.id', '=', 'vendors.area_id')
                    ->first();
            $facility->vendor->images = $facility->getFacilityImages();
            $openingHours = $facility->getOpenigHoursOfFacility();
            $facility->openingHours = $this->getOpeningHoursInGroup($openingHours);
            $facility->packages = $facility->getFacilityPackages();
            $facility->sessions = $facility->getFacilitySessions();

            return $facility;
        } catch (\Exception $ex) {
            throw new \Exception($ex);
        }
    }

    /**
     * Get opening hoirs according to the type of group provided
     * 
     * @param App\OpeningHour $openingHours
     * @param string $format
     * @return array
     */
    private function getOpeningHoursInGroup($openingHours, $format = 'peak_off_peak')
    {
        $response = array();
        if (!empty($openingHours) && $openingHours->count() > 0) {
            switch ($format) {
                case 'peak_off_peak':
                    $response = $this->getOpenigHoursGroupByPeakOffPeak($openingHours);
                    break;
            }
        }

        return $response;
    }
    
    /**
     * Get opening hours group by peak and off peak
     * 
     * @param App\OpeningHours $openingHours
     * @return array
     */
    private function getOpenigHoursGroupByPeakOffPeak($openingHours)
    {
        $response = array('peak' => array(), 'offpeak' => array());
        foreach ($openingHours as $openingHour) {
            $timingArray = array('start' => date('h:i A', strtotime('1/1/1970 '.$openingHour->start)), 'end' => date('h:i A', strtotime('1/1/1970 '.$openingHour->end)));
            if (1 == $openingHour->is_peak) {
                $response['peak'][$openingHour->day][] = $timingArray ;
            }

            if (0 == $openingHour->is_peak) {
                $response['offpeak'][$openingHour->day][] = $timingArray ;
            }
        }

        return $response;
    }

}