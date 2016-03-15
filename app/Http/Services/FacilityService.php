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

 //deepaks04@outlook.com, aquanta15@gmail.com, deepak04@gmail.com