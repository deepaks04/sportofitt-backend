<?php

namespace App\Http\Services;

use App\Vendor;
use App\OpeningHour;
use App\AvailableFacility;
use App\Http\Services\BaseService;

class FacilityService extends BaseService
{

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