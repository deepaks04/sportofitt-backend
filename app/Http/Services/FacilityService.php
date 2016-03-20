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
            $facility = AvailableFacility::find($facilityId);
            $facility->openingHours = $facility->getOpenigHoursOfFacility();
            $facility->packages = $facility->getFacilityPackages();
            $facility->sessions = $facility->getFacilitySessions();

            return $facility;
        } catch (\Exception $ex) {
            throw new \Exception($ex);
        }
    }

}