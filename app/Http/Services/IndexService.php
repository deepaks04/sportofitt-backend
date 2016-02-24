<?php namespace App\Http\Services;

use App\AvailableFacility;
use App\Http\Services\BaseService;

class IndexService extends BaseService {

    private $availableFacilities = null;

    /**
     * 
     */
    public function __construct()
    {
        $this->availableFacilities = new AvailableFacility();
    }

    /**
     * Get available facilities.
     * 
     * @param array $where
     * @param array $orderBy
     * @return App\AvailableFacility
     */
    public function getFacilities(array $where = array('is_active', '=', 1), array $orderBy = array('available_facilities.created_at', 'DESC'))
    {
        return $this->availableFacilities->getFacilities($where, $orderBy, $this->limit, $this->offset);
    }

}
