<?php

namespace App\Http\Traits;

trait DistanceLatLongTrait
{

    /**
     * earth's radius in miles
     * 
     * @var integer 
     */
    public $radius = 3959;

    /**
     * Distance in miles to calculate the lat/long 
     * 
     * @var integer
     */
    public $distance = 5;

    /**
     * Compute max and min latitudes / longitudes for search square by
     * provided lat/long and distance in miles.
     * 
     * @param float $latitude
     * @param float $longitude
     * @return array
     */
    public function getSearchSquareLatitudeLongitude($latitude, $longitude)
    {
        $result = array();

        if (!empty($latitude) && !empty($longitude)) {
            $result['latNorth'] = rad2deg(asin(sin(deg2rad($latitude)) * cos($this->distance / $this->radius) + cos(deg2rad($latitude)) * sin($this->distance / $this->radius) * cos(deg2rad(0))));
            $result['latSouth'] = rad2deg(asin(sin(deg2rad($latitude)) * cos($this->distance / $this->radius) + cos(deg2rad($latitude)) * sin($this->distance / $this->radius) * cos(deg2rad(180))));
            $result['lonEast'] = rad2deg(deg2rad($longitude) + atan2(sin(deg2rad(90)) * sin($this->distance / $this->radius) * cos(deg2rad($latitude)), cos($this->distance / $this->radius) - sin(deg2rad($latitude)) * sin(deg2rad($latNorth))));
            $result['lonWest'] = rad2deg(deg2rad($longitude) + atan2(sin(deg2rad(270)) * sin($this->distance / $this->radius) * cos(deg2rad($latitude)), cos($this->distance / $this->radius) - sin(deg2rad($latitude)) * sin(deg2rad($latNorth))));
        }

        return $result;
    }

}