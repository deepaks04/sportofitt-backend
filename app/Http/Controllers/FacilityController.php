<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Helpers\APIResponse;
use App\Http\Services\FacilityService;
use App\Http\Controllers\Controller;

class FacilityController extends Controller
{
    
    /**
     *
     * @var mixed null | App\Http\Services\IndexService
     */
    protected $service = null;

    public function __construct()
    {
        $this->service = new FacilityService();
    }
    
    /**
     * Showing booking information of the respective facility.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        try {
            $data = $request->all();
            if (!empty($data['facility_id'])) {
                $bookingInformation = $this->service->getSessionsAndPackages($data['facility_id']);
                APIResponse::$data = $bookingInformation;
            } else {
                APIResponse::$isError = true;
                APIResponse::$message['error'] = 'Select facility';
            }
            
        } catch (Exception $exception) {
            APIResponse::handleException($exception);
        }
        
        return APIResponse::sendResponse();
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
