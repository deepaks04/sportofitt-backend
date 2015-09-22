<?php

namespace App\Http\Controllers\Vendor;

use App\BillingInfo;
use App\Vendor;
use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use DB;
use Auth;
use App\User;
use File;
use URL;
use App\Billing;

class VendorsController extends Controller
{
    public function __construct()
    {
        //$this->middleware('auth');
        $this->middleware('auth',['except'=>['store']]);
        $this->middleware('vendor',['except'=>['store']]);
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
    public function store($vendorData)
    {
        try{
            DB::table('vendors')->insert($vendorData);
            $result['status'] = true;
            return $result;
        }catch (\Exception $e){
            $result['status'] = false;
            $result['message'] = $e->getMessage();
            return $result;

        }
    }

    /**
     * Get Vendor Profile Data
     * @return \Illuminate\Contracts\Routing\ResponseFactory|\Symfony\Component\HttpFoundation\Response
     */
    public function getProfile(){
        $user = Auth::user();
        $vendorUploadPath = URL::asset(env('VENDOR_FILE_UPLOAD'));
        $vendorOwnDirecory = $vendorUploadPath."/".sha1($user->id)."/"."profile_image/";
        $vendor = $user->vendor;
        $myProfile['fname'] = $user->fname;
        $myProfile['lname'] = $user->lname;
        $myProfile['email'] = $user->email;
        $myProfile['profile_picture'] = $vendorOwnDirecory.$user->profile_picture;
        $myProfile['username'] = $user->username;
        $myProfile['business_name'] = $vendor->business_name;
        $myProfile['address'] = $vendor->address;
        $myProfile['longitude'] = $vendor->longitude;
        $myProfile['latitude'] = $vendor->latitude;
        $myProfile['area_id'] = $vendor->area_id;
        $myProfile['contact'] = $vendor->contact;
        $myProfile['description'] = $vendor->description;
        $status= 200;
        $response = [
            'message' => 'success',
            'profile' => $myProfile
        ];
        return response($response,$status);
    }

    /**
     * Vendor Update Profile Settings
     * @param Requests\UpdateVendorProfileRequest $request
     * @return \Illuminate\Contracts\Routing\ResponseFactory|\Symfony\Component\HttpFoundation\Response
     */
    public function updateProfile(Requests\UpdateVendorProfileRequest $request){
        try{
            $status =200;
            $response = [
                "message" => "Settings updated successfully"
            ];
            $user = $request->all();
            $vendor = $request->all();
            $userKeys = array('business_name','longitude','latitude','area_id','description','_method','address','contact');
            $user = $this->unsetKeys($userKeys,$user);
            $vendorKeys = array('fname','lname','_method','profile_picture');
            $vendor = $this->unsetKeys($vendorKeys,$vendor);
            $systemUser = User::find(Auth::user()->id);
            /* File Upload Code */
            $vendorUploadPath = public_path().env('VENDOR_FILE_UPLOAD');
            $vendorOwnDirecory = $vendorUploadPath.sha1($systemUser->id);
            $vendorImageUploadPath = $vendorOwnDirecory."/"."profile_image";
            /* Create Upload Directory If Not Exists */
            if(!file_exists($vendorImageUploadPath)){
                File::makeDirectory($vendorImageUploadPath, $mode = 0777,true,true);
                chmod($vendorOwnDirecory, 0777);
                chmod($vendorImageUploadPath, 0777);
            }
            $extension = $request->file('profile_picture')->getClientOriginalExtension();
            $filename = sha1($systemUser->id.time()).".{$extension}";
            $request->file('profile_picture')->move($vendorImageUploadPath, $filename);
            chmod($vendorImageUploadPath, 0777);

            /* Rename file */
            $user['profile_picture'] = $filename;
            $systemUser->update($user);
            $systemUser->vendor()->update($vendor);
        }catch(\Exception $e){
            echo $e->getMessage();exit;
            $status =500;
            $response = [
                "message" => "Something Went Wrong",
            ];
        }
        return response($response,$status);
    }

    /**
     * @param $keys
     * @param $array
     * @return mixed
     */
    public function unsetKeys($keys,$array){
        foreach($keys as $key) {
            unset($array[$key]);
        }
        return $array;
    }

    /**
     * get billing information
     * @return \Illuminate\Contracts\Routing\ResponseFactory|\Symfony\Component\HttpFoundation\Response
     */
    public function getBillingInformation(){
        $user = Auth::user();
        $vendor = $user->vendor()->first();
        $billing = $vendor->billingInfo()->first()->toArray();
        if($billing!=null){
            $message = 'success';
        }else{
            $message = 'Please update your billing information';
        }
        $status =200;
        $response = [
            "message" => $message,
            "billing" => $billing
        ];
        return response($response,$status);
    }

    public function updateBillingInformation(Requests\Billing $request){
        try{
            $user = Auth::user();
            $vendor = $user->vendor()->first();
            $billing = $vendor->billingInfo()->first()->toArray();
            if($billing!=null){ //Update If exists
                $data = $request->all();
                unset($data['_method']);
                $vendor->billingInfo()->update($data);
            }else{ //Insert if not exists
                $data = $request->all();
                unset($data['_method']);
                $data['vendor_id'] = $vendor->id;
                $vendor->billingInfo()->insert($data);
                //$billingInfo = Billing::create($data);
                //$user->vendor()->update(array('billing_info_id'=>$billingInfo->id));
            }
            $status =200;
            $message = "saved successfully";
        }catch(\Exception $e){
            echo $e->getMessage();
            $status =500;
            $message = "something went wrong";
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
