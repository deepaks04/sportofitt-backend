<?php

namespace App\Http\Controllers\Customer;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Auth;

class CustomersController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return Response
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  Request  $request
     * @return Response
     */
    public function store($customerData)
    {
        try{
            DB::table('customers')->insert($customerData);
            $result['status'] = true;
            return $result;
        }catch (\Exception $e){
            $result['status'] = false;
            $result['message'] = $e->getMessage();
            return $result;

        }
    }

    public function updateProfileInformation(Requests\CustomerProfileUpdateRequest $request){
        try{
            $status = 200;
            $message = "Updated Successfully";
            $user = Auth::user();
            $customer = $user->customer()->first();
            $userData = $request->all();
            $userKeys = array('birthdate','gender','_method','pincode','phone_no','area_id');
            $userData = $this->unsetKeys($userKeys,$userData);
            $user->update($userData);
            $customerData = $request->all();
            $customerKeys = array('fname','lname','_method','profile_picture','email','username');
            $customerData = $this->unsetKeys($customerKeys,$customerData);//dd($request->birthdate);
            $birthdate = strtotime($request->birthdate);
            $birthdate = date('Y-m-d',$birthdate);
            $customerData['birthdate'] = $birthdate;
            $customer->update($customerData);
        }catch(\Exception $e){
            $status = 500;
            $message = "Updated Successfully";
        }
        $response = [
            "message" => $message,
        ];
        return response($response,$status);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  Request  $request
     * @param  int  $id
     * @return Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return Response
     */
    public function destroy($id)
    {
        //
    }
}
