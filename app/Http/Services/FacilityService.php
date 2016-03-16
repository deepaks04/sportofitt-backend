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
     * @param integer $facilityId
     * @return array
     * @throws Exception
     */
    public function getSessionsAndPackages($facilityId)
    {
        try {
            $facility = AvailableFacility::find($facilityId);
            $facility->openingHours = $facility->getOpenigHoursOfFacility($facilityId);
            $facility->packages = $facility->getFacilityPackages($facilityId);
            $facility->sessions = $facility->getFacilitySessions($facilityId);

            return $facility;
        } catch (Exception $ex) {
            throw new Exception($ex);
        }
    }

}