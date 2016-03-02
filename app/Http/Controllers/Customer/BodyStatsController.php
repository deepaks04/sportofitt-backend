<?php namespace App\Http\Controllers\Customer;

use App\Http\Services\StatsService;
use App\Http\Controllers\Controller;
use App\Http\Helpers\APIResponse;
use App\Http\Requests\BodyStatsRequest;

class BodyStatsController extends Controller {

    public function __construct()
    {
        try {
            $this->service = new StatsService();
        } catch (Exception $exception) {
            throw new Exception($exception->getMessage(), $exception->getStatusCode(), $exception);
        }
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        try {
            $userBodyStats = $this->service->getUserBodyStats();
            APIResponse::$data = $userBodyStats;
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
     * @param  App\Http\Requests\BodyStatsRequest $request
     * @return \Illuminate\Http\Response
     */
    public function store(BodyStatsRequest $request)
    {
        try {
            $data = $request->all();
            $saved = $this->service->saveUserBodyStats($data);
            if ($saved) {
                APIResponse::$message['success'] = 'Information has been saved';
                APIResponse::$data = $this->service->getUserBodyStats();
                $customer = $this->service->user->customer;
                if (isset($customer)) {
                    $birthDetails = explode("-", $customer->birthdate);
                    APIResponse::$data['year'] = (int)$birthDetails[0];
                    APIResponse::$data['month'] = (int)$birthDetails[1];
                    APIResponse::$data['day'] = (int)$birthDetails[2];
                    APIResponse::$data['gender'] = $customer->gender;
                }
            }
        } catch (Exception $exception) {
            APIResponse::handleException($exception);
        }

        return APIResponse::sendResponse();
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
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
     * @param  App\Http\Requests\BodyStatsRequest  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(BodyStatsRequest $request, $id)
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
