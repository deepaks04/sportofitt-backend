<?php

namespace App\Http\Controllers\Vendor;

use App\BillingInfo;
use App\PackageType;
use App\RootCategory;
use App\SessionPackage;
use App\SessionPackageChild;
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
use Carbon\Carbon;
use App\AvailableFacility;
use App\SubCategory;

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
     * Update First Time Login Flag
     */
    public function updateFirstLoginFlag(){
        try{
            $user = Auth::user();
            $flag = $user->vendor()->update(array('is_processed'=>1));
            $message = "Success";
            $status = 200;
        }catch(\Exception $e){
            $message = "Something went wrong";
            $status = 500;
        }
        $response = [
            'message' => $message
        ];
        return response($response,$status);
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
        if($user->profile_picture==null){
            $myProfile['profile_picture'] = $user->profile_picture;
        }else{
            $myProfile['profile_picture'] = $vendorOwnDirecory.$user->profile_picture;
        }
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
            unset($request['email']);
            unset($request['username']);
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
     * get billing information
     * @return \Illuminate\Contracts\Routing\ResponseFactory|\Symfony\Component\HttpFoundation\Response
     */
    public function getBillingInformation(){
        $user = Auth::user();
        $vendor = $user->vendor()->first();
        $billing = $vendor->billingInfo()->first();
        if($billing!=null){
            $message = 'success';
            $status =200;
            $billing = $billing->toArray();
        }else{
            $status =404;
            $message = 'Please update your billing information';
        }
        $response = [
            "message" => $message,
            "billing" => $billing
        ];
        return response($response,$status);
    }

    /**
     * Insert/Update Billing Details
     * @param Requests\Billing $request
     * @return \Illuminate\Contracts\Routing\ResponseFactory|\Symfony\Component\HttpFoundation\Response
     */
    public function updateBillingInformation(Requests\Billing $request){
        try{
            $user = Auth::user();
            $vendor = $user->vendor()->first();
            $billing = $vendor->billingInfo()->first();
            if($billing!=null){ //Update If exists
                $data = $request->all();
                unset($data['_method']);
                $vendor->billingInfo()->update($data);
            }else{ //Insert if not exists
                $data = $request->all();
                unset($data['_method']);
                $data['vendor_id'] = $vendor->id;
                $data['created_at'] = Carbon::now();
                $data['updated_at'] = Carbon::now();
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
     * Get Bank Details
     * @return \Illuminate\Contracts\Routing\ResponseFactory|\Symfony\Component\HttpFoundation\Response
     */
    public function getBankDetails(){
        $user = Auth::user();
        $vendor = $user->vendor()->first();
        $bank = $vendor->bankInfo()->first();
        if($bank!=null){
            $message = 'success';
            $status =200;
            $bank = $bank->toArray();
        }else{
            $status =404;
            $message = 'Please update your bank details';
        }
        $response = [
            "message" => $message,
            "bank" => $bank
        ];
        return response($response,$status);
    }

    /**
     * Insert/Update Bank Details
     * @param Requests\BankDetails $request
     * @return \Illuminate\Contracts\Routing\ResponseFactory|\Symfony\Component\HttpFoundation\Response
     */
    public function updateBankDetails(Requests\BankDetails $request){
        try{
            $user = Auth::user();
            $vendor = $user->vendor()->first();
            $billing = $vendor->bankInfo()->first();
            if($billing!=null){ //Update If exists
                $data = $request->all();
                unset($data['_method']);
                $vendor->bankInfo()->update($data);
            }else{ //Insert if not exists
                $data = $request->all();
                unset($data['_method']);
                $data['vendor_id'] = $vendor->id;
                $data['created_at'] = Carbon::now();
                $data['updated_at'] = Carbon::now();
                $vendor->bankInfo()->insert($data);
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
     * Insert Images
     * @param Requests\ImagesRequest $request
     * @return \Illuminate\Contracts\Routing\ResponseFactory|\Symfony\Component\HttpFoundation\Response
    */
    public function addImages(Requests\ImagesRequest $request){
        try{
            $file = $request->image_name;
            $user = Auth::user();
            $maxUploadLimit = (int)env('VENDOR_IMAGE_UPLOAD_LIMIT');
            $vendor = $user->vendor()->first();
            $images = $vendor->images()->count();
            if($images==$maxUploadLimit){ // Do not allowed to upload more than 10 Images
                $status = 406;
                $message = "Cannot Upload more than ".$maxUploadLimit." images";
            }else{ //Insert if not reached to max limit
                $status =200;
                $message = "saved successfully";
                $data = $request->all();
                /* File Upload Code */
                $vendorUploadPath = public_path().env('VENDOR_FILE_UPLOAD');
                $vendorOwnDirecory = $vendorUploadPath.sha1($user->id);
                $vendorImageUploadPath = $vendorOwnDirecory."/"."extra_images";
                /* Create Upload Directory If Not Exists */
                if(!file_exists($vendorImageUploadPath)){
                    File::makeDirectory($vendorImageUploadPath, $mode = 0777,true,true);
                    chmod($vendorOwnDirecory, 0777);
                    chmod($vendorImageUploadPath, 0777);
                }

                chmod($vendorImageUploadPath, 0777);
                // foreach($files as $file){
                    $random = mt_rand(1,1000000);
                    $extension = $file->getClientOriginalExtension();
                    $filename = sha1($user->id.$random).".{$extension}";
                    $file->move($vendorImageUploadPath, $filename);
                    /* Rename file */
                    $data['image_name'] = $filename;
                    $data['vendor_id'] = $vendor->id;
                    $data['created_at'] = Carbon::now();
                    $data['updated_at'] = Carbon::now();
                    $vendor->images()->insert($data);
                // }
            }
        }catch(\Exception $e){
            // echo $e->getMessage();
            $status =500;
            $message = "something went wrong".$e->getMessage();
        }
        $images = $vendor->images()->count();
        $response = [
            "message" => $message,
            "image_count" => $images
        ];
        return response($response,$status);
    }

    /**
     * Get All extra images of vendor facility
     * @return \Illuminate\Contracts\Routing\ResponseFactory|\Symfony\Component\HttpFoundation\Response
     */
    public function getImages(){
        $user = Auth::user();
        $vendor = $user->vendor()->first();
        $imageCount = $vendor->images()->count();
        if($imageCount == 0){
            $status = 404;
            $message = "Images not found. Please upload some";
            $images = null;
        }else{
            $status = 200;
            $message = "success";
            $images = $vendor->images()->get()->toArray();
            $vendorUploadPath = URL::asset(env('VENDOR_FILE_UPLOAD'));
            $url = $vendorUploadPath."/".sha1($user->id)."/"."extra_images/";
            for($i=0;$i<$imageCount;$i++){
                $images[$i]['image_name'] = $url.$images[$i]['image_name'];
            }
        }
        $response = [
            "message" => $message,
            "images" => $images
        ];
        return response($response,$status);
    }

    /**
     * Delete Extra Image - one at a time
     * @param Requests\DeleteImageRequest $request
     * @param $id
     * @return \Illuminate\Contracts\Routing\ResponseFactory|\Symfony\Component\HttpFoundation\Response
     */
    public function deleteImage(Requests\DeleteImageRequest $request ,$id){
        try{
            $user = Auth::user();
            $vendor = $user->vendor()->first();
            $image = $vendor->images('id','=',$id)->first();
            $status = $image->delete();
            if($status){
                $status = 200;
                $message = "Image deleted successfully";
                $vendorUploadPath = public_path().env('VENDOR_FILE_UPLOAD');
                $path= $vendorUploadPath."/".sha1($user->id)."/"."extra_images/";
                File::delete($path.$image->image_name);
            }else{
                $status =500;
                $message = "something went wrong";
            }
        }catch(\Exception $e){
            $status =500;
            $message = "something went wrong";
        }
        $response = [
            "message" => $message,
        ];
        return response($response,$status);
    }

    public function createFacility(Requests\AddFacilityRequest $request){
        try{
            $facility = $request->all();
            $user = Auth::user();
            $vendor = $user->vendor()->first();
            $facilityExists = AvailableFacility::where('vendor_id','=',$vendor->id)->where('sub_category_id','=',$facility['sub_category_id'])->count();
            if($facilityExists){ // If Facility already exists
                $status = 406; //Not Acceptable
                $message = "Facility already exists";
            }else{ //If not then create
                $status = 200;
                $message = "New facility added successfully";
                /* File Upload Code */
                $vendorUploadPath = public_path().env('VENDOR_FILE_UPLOAD');
                $vendorOwnDirecory = $vendorUploadPath.sha1($user->id);
                $vendorImageUploadPath = $vendorOwnDirecory."/"."facility_images";
                /* Create Upload Directory If Not Exists */
                if(!file_exists($vendorImageUploadPath)){
                    File::makeDirectory($vendorImageUploadPath, $mode = 0777,true,true);
                    chmod($vendorOwnDirecory, 0777);
                    chmod($vendorImageUploadPath, 0777);
                }
                $extension = $request->file('image')->getClientOriginalExtension();
                $filename = sha1($user->id.time()).".{$extension}";
                $request->file('image')->move($vendorImageUploadPath, $filename);
                chmod($vendorImageUploadPath, 0777);

                /* Rename file */
                $facility['image'] = $filename;
                $facility['vendor_id'] = $vendor->id;
                $facility['created_at'] = Carbon::now();
                $facility['updated_at'] = Carbon::now();
                AvailableFacility::create($facility);
            }
        }catch(\Exception $e){
            $status = 500;
            $message = "Something went wrong : " . $e->getMessage();
        }
        $response = [
            "message" => $message,
        ];
        return response($response,$status);
    }

    /**
     * Get All facility created by vendor
     * @return \Illuminate\Contracts\Routing\ResponseFactory|\Symfony\Component\HttpFoundation\Response
     */
    public function getFacility(){
        $user = Auth::user();
        $vendor = $user->vendor()->first();
        $facilityCount = $vendor->facility()->count();
        if($facilityCount == 0){
            $status = 404;
            $message = "Facility not found. Please create some";
            $facilityCount = null;
            $facility = null;
        }else{
            $status = 200;
            $message = "success";
            $facility = $vendor->facility()->get()->toArray();
            /*foreach($facility as $singleFacility){
                //dd($singleFacility['sub_category_id']);
                $singleFacility['category']['sub'] = SubCategory::find($singleFacility['sub_category_id'])->toArray();
                //$singleFacility['category'] = $singleFacility['category']->toArray();
                //dd($singleFacility);
            }*/
            for($i=0;$i<$facilityCount;$i++){
                $facility[$i]['category']['sub'] = SubCategory::find($facility[$i]['sub_category_id'])->toArray();
                $facility[$i]['category']['root'] = RootCategory::find($facility[$i]['category']['sub']['root_category_id'])->toArray();
            }
            $vendorUploadPath = URL::asset(env('VENDOR_FILE_UPLOAD'));
            $url = $vendorUploadPath."/".sha1($user->id)."/"."facility_images/";
            for($i=0;$i<$facilityCount;$i++){
                $facility[$i]['image'] = $url.$facility[$i]['image'];
            }
        }
        $response = [
            "message" => $message,
            "facility" => $facility,
            "facilityCount" => $facilityCount
        ];
        return response($response,$status);
    }


    public function getFacilityById($id){
        $status = 200;
        $message = "success";
        $user = Auth::user();
        $vendor = $user->vendor()->first();
        $facility = $vendor->facility()->find($id);
        if($facility!=null) {
        $facility = $facility->toArray();
        $vendorUploadPath = URL::asset(env('VENDOR_FILE_UPLOAD'));
        $url = $vendorUploadPath."/".sha1($user->id)."/"."facility_images/";
            $facility['image'] = $url.$facility['image'];
        }else{
            $status = 404;
            $message = "Facility not found. Please create some";
            $facility = null;
        }
        $response = [
            "message" => $message,
            "facility" => $facility
        ];
        return response($response,$status);
    }
    public function updateFacility(Requests\AddFacilityRequest $request,$id){
        try{
            $facility = $request->all();
            unset($facility['_method']);
            $user = Auth::user();
            $vendor = $user->vendor()->first();

            $status = 200;
            $message = "facility updated successfully";
            /* If File Exists then */
            if(isset($facility['image']) && !empty($facility['image'])){
                /* File Upload Code */
                $vendorUploadPath = public_path().env('VENDOR_FILE_UPLOAD');
                $vendorOwnDirecory = $vendorUploadPath.sha1($user->id);
                $vendorImageUploadPath = $vendorOwnDirecory."/"."facility_images";
                /* Create Upload Directory If Not Exists */
                if(!file_exists($vendorImageUploadPath)){
                    File::makeDirectory($vendorImageUploadPath, $mode = 0777,true,true);
                    chmod($vendorOwnDirecory, 0777);
                    chmod($vendorImageUploadPath, 0777);
                }
                $extension = $request->file('image')->getClientOriginalExtension();
                $filename = sha1($user->id.time()).".{$extension}";
                $request->file('image')->move($vendorImageUploadPath, $filename);
                chmod($vendorImageUploadPath, 0777);

                /* Rename file */
                $facility['image'] = $filename;
            }
            $facility['vendor_id'] = $vendor->id;
            AvailableFacility::where('id','=',$id)->update($facility);
        }catch(\Exception $e){
            echo $e->getMessage();exit;
            $status = 500;
            $message = "Something went wrong";
        }
        $response = [
            "message" => $message,
        ];
        return response($response,$status);
    }

    public function getFacilityDetailInformation(Requests\FacilityInfoRequest $request,$id){
        $user = Auth::user();
        $vendor = $user->vendor()->first();
        $facilities = $vendor->facility()->where(array('id'=>$id))->get();
        $sessionPackageInfoType = null;
        if($facilities!=null){
            $facilities = $facilities->toArray();dd($facilities);
            $i = 0;
            foreach($facilities as $facility){
                $facilityCount = count($facilities);
                for($j=0;$i<$facilityCount;$i++){
                    $facilities[$j]['category']['sub'] = SubCategory::find($facilities[$j]['sub_category_id'])->toArray();
                    $facilities[$j]['category']['root'] = RootCategory::find($facilities[$j]['category']['sub']['root_category_id'])->toArray();
                }
                $packages = SessionPackage::where('available_facility_id','=',$facility['id'])->get();
                if($packages!=null){
                    $packages = $packages->toArray();

                    foreach($packages as $package){
                        $type = PackageType::where('id','=',$package['package_type_id'])->first()->toArray();
                        //dd($type);
                        $sessionPackageInfoType[$type['slug']][$i]['parent'] = $package;
                        $packageChild = SessionPackageChild::where(array('session_package_id'=>$package['id'],'is_active'=>1))->first();
                        if($packageChild!=null){
                            $packageChild = $packageChild->toArray();
                            $sessionPackageInfoType[$type['slug']][$i]['child'] = $packageChild;
                        }else{
                            $sessionPackageInfoType[$type['slug']][$i]['child'] = null;
                        }

                        $i++;
                    }
                }
            }
        }
        $status = 200;
        $response = [
            "message" => "success",
            "user" => $user,
            "vendor" => $vendor,
            "facility" => $facilities,
            "sessionPackage" => $sessionPackageInfoType
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
