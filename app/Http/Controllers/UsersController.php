<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use App\Http\Controllers\Vendor\VendorsController;
use DB;
use Carbon\Carbon;
use App\User;
use Mail;
use Auth;

class UsersController extends Controller
{
    public function __construct()
    {
        //$this->middleware('auth');
        $this->middleware('guest',['only'=>['storeVendor', 'confirm']]);
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
    public function storeCustomer(Request $request)
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
            $userData = $request->all();
            $userData['password'] = bcrypt($request->password);
            $userData['is_active'] = 0; //will be 1 after email verification
            $userData['status_id'] = 4; //By Default Pending
            $userData['role_id'] = 2; //Vendor Role Id
            $userData['remember_token'] = csrf_token();
            $userData['updated_at'] = Carbon::now();
            $userData['created_at'] = Carbon::now();
            unset($userData['business_name']);
            //$user = User::create($userData); Massassignment
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
