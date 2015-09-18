<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use App\Http\Controllers\Vendor\VendorsController;
use App\Http\Controllers\Customer\CustomersController;
use DB;
use Carbon\Carbon;
use App\User;
use Mail;
use Auth;
use App\Role;
use App\Status;

class UsersController extends Controller
{
    public function __construct()
    {
        //$this->middleware('auth');
        $this->middleware('guest',['only'=>['storeVendor', 'confirm','storeCustomer']]);
    }
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
     * Store a newly created resource in storage.
     *
     * @param  Request  $request
     * @return Response
     */
    public function storeVendor(Requests\CreateVendorRequest $request)
    {
        try{
            $status =200;
            $response = [
                "message" => "Vendor Registered Successfully! Please check your email for the instructions on how to confirm your account"
            ];
            $role = Role::where('slug','vendor')->first();
            $userStatus = Status::where('slug','pending')->first();
            $userData = $request->all();
            $userData['password'] = bcrypt($request->password);
            $userData['is_active'] = 0; //will be 1 after email verification
            $userData['status_id'] = $userStatus->id; //By Default Pending
            $userData['role_id'] = $role->id; //Vendor Role Id
            $userData['remember_token'] = csrf_token();
            $userData['updated_at'] = Carbon::now();
            $userData['created_at'] = Carbon::now();
            unset($userData['business_name']);
            //$user = User::create($userData); //Mass assignment
            //$user->id; last inserted id
            $userId = DB::table('users')->insertGetId($userData);
            $vendorData['business_name'] = $request->business_name;
            $vendorData['user_id'] = $userId;
            $vendorData['updated_at'] = Carbon::now();
            $vendorData['created_at'] = Carbon::now();
            //Calling a method that is from the VendorsController
            $result = (new VendorsController)->store($vendorData);
            if($result['status']){
                Mail::send('emails.activation', $userData, function($message) use ($userData){
                    $message->to($userData['email'])->subject('Account Confirmation');
                });
            }else{
                User::destroy($userId);
                throw new \Exception($result['message']);
            }
        }catch (\Exception $e){
            $status =500;
            $response = [
                "message" => "Something Went Wrong",
                //"message" => "Something Went Wrong, Vendor Registration Unsuccessful!".$e->getMessage(),
            ];
        }
        return response($response,$status);
    }


    /**
     * Store a newly created resource in storage.
     *
     * @param  Request  $request
     * @return Response
     */
    public function storeCustomer(Requests\CreateCustomerRequest $request)
    {
        try{
            $status =200;
            $response = [
                "message" => "Registered Successfully! Please check your email for the instructions on how to confirm your account"
            ];
            $role = Role::where('slug','customer')->first();
            $userStatus = Status::where('slug','pending')->first();
            $userData = $request->all();
            $userData['password'] = bcrypt($request->password);
            $userData['is_active'] = 0; //will be 1 after email verification
            $userData['status_id'] = (int)$userStatus->id; //By Default Pending
            $userData['role_id'] = (int)$role->id; //User Role Id
            $userData['remember_token'] = csrf_token();
            $userData['updated_at'] = Carbon::now();
            $userData['created_at'] = Carbon::now();
            unset($userData['gender']);
            unset($userData['area_id']);
            //$user = User::create($userData); //Mass assignment
            //$user->id; last inserted id
            $userId = DB::table('users')->insertGetId($userData);
            $customerData['gender'] = (int)$request->gender;
            $customerData['area_id'] = (int)$request->area_id;
            $customerData['user_id'] = (int)$userId;
            $customerData['updated_at'] = Carbon::now();
            $customerData['created_at'] = Carbon::now();
            //Calling a method that is from the VendorsController
            $result = (new CustomersController)->store($customerData);
            if($result['status']){
                //$environment = app()->environment();
                //if ($environment=='production') {
                    // The environment is local
                    Mail::send('emails.activation', $userData, function($message) use ($userData){
                        $message->to($userData['email'])->subject('Account Confirmation');
                    });
                //}
            }else{
                User::destroy($userId);
                throw new \Exception($result['message']);
            }
        }catch (\Exception $e){
            $status =500;
            $response = [
                "message" => "Something Went Wrong",
                //"message" => "Something Went Wrong, Vendor Registration Unsuccessful!".$e->getMessage(),
            ];
        }
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

    /**
     * Confirm User email & activate his account.
     *
     * @param  string  $confirmation
     * @return Response
     */
    public function confirm($confirmation){
        $user = User::where('remember_token',$confirmation)->first();
        if($user==null){ //no record found
            $status =404;
            $response = [
                "message" => "Sorry!! No User found",
            ];
        }else{
            if($user->is_active){ //already confirmed
                $status =200;
                $response = [
                    "message" => "Your account already confirmed",
                ];
            }else{
                User::where('remember_token', $confirmation)->update(array('is_active' => 1));
                $status =200;
                $response = [
                    "message" => "Your account is confirmed, you can now login to your account",
                ];
            }
        }
        return response($response,$status);
    }
}
