<?php

namespace App\Http\Controllers\Admin;

use App\AvailableFacility;
use App\Billing;
use App\PackageType;
use App\RootCategory;
use App\SessionPackage;
use App\SubCategory;
use App\VendorImages;
use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use App\Status;
use App\User;
use App\Role;
use Auth;
use App\Area;
use Carbon\Carbon;
use DB;
use URL;
use Illuminate\Support\Facades\Route;
use File;

class VendorController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return Response
     */
    public function __construct()
    {
        // $this->middleware('auth');
        $this->middleware('auth');
        $this->middleware('admin');
        $this->middleware('verify.vendor');

    }

    /**
     * @return \Illuminate\Contracts\Routing\ResponseFactory|\Symfony\Component\HttpFoundation\Response
     */
    public function skull(){
        try{
            $status = 200;
            $message = "";
        }catch(\Exception $e){
            $status = 500;
            $message = "Something went wrong ".$e->getMessage();
        }
        $response = [
            "message" => $message
        ];
        return response($response, $status);
    }

    /**
     * @return \Illuminate\Contracts\Routing\ResponseFactory|\Symfony\Component\HttpFoundation\Response
     */
    public function getVendorList(){
        try{
            $status = 200;
            $message = "success";
            $role = Role::where('slug','vendor')->first();
            $userCount = User::where('role_id',$role->id)->count();
            if($userCount>0){
                $users = User::where('role_id',$role->id)->with('vendor')->get();
                $i = 0;
                foreach($users as $user){
                    $userData[$i] = $user;
                    if($user->vendor->area_id!=null){
                        $area = Area::find($user->vendor->area_id);
                        $userData[$i]['area'] = $area;
                    }else{
                        $userData[$i]['area'] = "";
                    }
                    $currentStatus = Status::find($user->status_id);
                    $userData[$i]['status'] = $currentStatus;
                    $i++;
                }
                $users = $userData;
            }else{
                $message = "no record found";
                $users = "";
            }
        }catch(\Exception $e){
            $status = 500;
            $message = "Something went wrong ".$e->getMessage();
            $users = "";
        }
        $response = [
            "message" => $message,
            "data"=> $users
        ];
        return response($response, $status);
    }

    /**
     * @param Requests\CreateVendorRequest $request
     *
     * @return \Illuminate\Contracts\Routing\ResponseFactory|\Symfony\Component\HttpFoundation\Response
     */
    public function create(Requests\CreateVendorRequest $request){
        try{
            $status = 200;
            $message = "Vendor registered Successfully";
            $role = Role::where('slug', 'vendor')->first();
            $userStatus = Status::where('slug', 'pending')->first();
            $vendor = "";
            $userData = $request->all();
            $userData['password'] = bcrypt($request->password);
            $userData['is_active'] = 1; // will be 1 after email verification
            $userData['status_id'] = $userStatus->id; // By Default Pending
            $userData['role_id'] = $role->id; // Vendor Role Id
            $userData['remember_token'] = csrf_token();
            $userData['updated_at'] = Carbon::now();
            $userData['created_at'] = Carbon::now();
            unset($userData['business_name']);
            unset($userData['password2']);
            // $user = User::create($userData); //Mass assignment
            // $user->id; last inserted id
            $userId = DB::table('users')->insertGetId($userData);
            $vendorData['business_name'] = $request->business_name;
            $vendorData['user_id'] = $userId;
            $vendorData['updated_at'] = Carbon::now();
            $vendorData['created_at'] = Carbon::now();
            // Calling a method that is from the VendorsController
            $result = $this->insert($vendorData);
            if (!$result['status']) {
                User::destroy($userId);
                throw new \Exception($result['message']);
            }else{
                $vendor = User::where('id',$userId)->with('vendor')->first();
            }
        }catch(\Exception $e){
            $status = 500;
            $message = "Something went wrong ".$e->getMessage();
            $vendor = "";
        }
        $response = [
            "message" => $message,
            "data"=> $vendor
        ];
        return response($response, $status);
    }

    /**
     * @param $vendorData
     *
     * @return mixed
     */
    public function insert($vendorData){
        try {
            DB::table('vendors')->insert($vendorData);
            $result['status'] = true;
            return $result;
        } catch (\Exception $e) {
            $result['status'] = false;
            $result['message'] = $e->getMessage();
            return $result;
        }
    }

    /**
     * @param Requests\UpdateVendorProfileRequest $request
     * @param                                     $id
     *
     * @return \Illuminate\Contracts\Routing\ResponseFactory|\Symfony\Component\HttpFoundation\Response
     */
    public function updateProfile(Requests\UpdateVendorProfileRequest $request,$id)
    {
        try {
            $status = 200;
            $message =  "Settings updated successfully";
            $vendorInformation = "";
            $user = $request->all();
            $vendor = $request->all();
            $userKeys = array(
                'email',
                'username',
                'business_name',
                'longitude',
                'latitude',
                'area_id',
                'description',
                '_method',
                'address',
                'contact',
                'postcode',
                'commission'
            );
            $user = $this->unsetKeys($userKeys, $user);
            $vendorKeys = array(
                'email',
                'username',
                'fname',
                'lname',
                '_method',
                'profile_picture'
            );
            $vendor = $this->unsetKeys($vendorKeys, $vendor);
            $systemUser = User::findOrFail($id);
            if ($request->file('profile_picture')!=null) {
                /* File Upload Code */
                $vendorUploadPath = public_path().env('VENDOR_FILE_UPLOAD');
                $vendorOwnDirecory = $vendorUploadPath.sha1($systemUser->id);
                $vendorImageUploadPath = $vendorOwnDirecory."/"."profile_image";
                /* Create Upload Directory If Not Exists */
                if (! file_exists($vendorImageUploadPath)) {
                    File::makeDirectory($vendorImageUploadPath, $mode = 0777, true, true);
                    // chmod($vendorOwnDirecory, 0777);
                    // chmod($vendorImageUploadPath, 0777);
                }
                $extension = $request->file('profile_picture')->getClientOriginalExtension();
                $filename = sha1($systemUser->id . time()) . ".{$extension}";
                $request->file('profile_picture')->move($vendorImageUploadPath, $filename);
                // chmod($vendorImageUploadPath, 0777);

                /* Rename file */
                $user['profile_picture'] = $filename;
            }
            $systemUser->update($user);
            $systemUser->vendor()->update($vendor);
            $vendorInformation = User::where("id",$id)->with('vendor')->first();
            if ($vendorInformation->profile_picture != null) {
                $vendorUploadPath = URL::asset(env('VENDOR_FILE_UPLOAD'));
                $vendorOwnDirecory = $vendorUploadPath . "/" . sha1($systemUser->id) . "/" . "profile_image/";
                $vendorInformation->profile_picture = $vendorOwnDirecory.$vendorInformation->profile_picture;
            }
        } catch (\Exception $e) {
            $status = 500;
            $vendorInformation = "";
            $message = "Something went wrong ".$e->getMessage();
        }
        $response = [
            "message" => $message,
            "data" => $vendorInformation
        ];
        return response($response, $status);
    }

    /**
     * @param $id
     *
     * @return \Illuminate\Contracts\Routing\ResponseFactory|\Symfony\Component\HttpFoundation\Response
     */
    public function getProfile($id)
    {
        try{
            $status = 200;
            $message = "success";
            $user = User::where('id',$id)->with('vendor')->first();
            $vendorUploadPath = URL::asset(env('VENDOR_FILE_UPLOAD'));
            $vendorOwnDirecory = $vendorUploadPath . "/" . sha1($user->id) . "/" . "profile_image/";
            if ($user->profile_picture == null) {
                $user->profile_picture = $user->profile_picture;
            } else {
                $user->profile_picture = $vendorOwnDirecory . $user->profile_picture;
            }
        }catch(\Exception $e){
            $status = 500;
            $message = "Something went wrong ".$e->getMessage();
            $user = "";
        }
        $response = [
            "message" => $message,
            "data" => $user
        ];
        return response($response, $status);
    }

    /**
     * @param $id
     *
     * @return \Illuminate\Contracts\Routing\ResponseFactory|\Symfony\Component\HttpFoundation\Response
     */
    public function getBillingInformation($id)
    {
        try{
            $user = User::findOrFail($id);
            $vendor = $user->vendor()->first();
            $billing = $vendor->billingInfo()->first();
            if ($billing != null) {
                $message = 'success';
                $status = 200;
                $billing = $billing->toArray();
            } else {
                $status = 200;
                $message = 'Please update your billing information';
                $billing = "";
            }
        }catch(\Exception $e){
            $status = 500;
            $message = "Something went wrong ".$e->getMessage();
            $billing = "";
        }
        $response = [
            "message" => $message,
            "data" => $billing
        ];
        return response($response, $status);
    }

    /**
     * @param Requests\Billing $request
     * @param                  $id
     *
     * @return \Illuminate\Contracts\Routing\ResponseFactory|\Symfony\Component\HttpFoundation\Response
     */
    public function updateBillingInformation(Requests\Billing $request,$id)
    {
        try {
            $user =  User::findOrFail($id);
            $vendor = $user->vendor()->first();
            $billingInformation = "";
            $billing = $vendor->billingInfo()->first();
            if ($billing != null) { // Update If exists
                $data = $request->all();
                unset($data['_method']);
                $vendor->billingInfo()->update($data);
            } else { // Insert if not exists
                $data = $request->all();
                unset($data['_method']);
                $data['vendor_id'] = $vendor->id;
                $data['created_at'] = Carbon::now();
                $data['updated_at'] = Carbon::now();
                $vendor->billingInfo()->insert($data);
            }
            $billingInformation = Billing::find($id);
            $status = 200;
            $message = "saved successfully";
        } catch (\Exception $e) {
            $status = 500;
            $message = "something went wrong ".$e->getMessage();;
            $billingInformation = "";
        }
        $response = [
            "message" => $message,
            "data" => $billingInformation
        ];
        return response($response, $status);
    }

    /**
     * @param $id
     *
     * @return \Illuminate\Contracts\Routing\ResponseFactory|\Symfony\Component\HttpFoundation\Response
     */
    public function getBankDetails($id)
    {
        try{
            $status = 200;
            $message = "";
            $user = User::findOrFail($id);
            $vendor = $user->vendor()->first();
            $bank = $vendor->bankInfo()->first();
            if ($bank != null) {
                $message = 'success';
                $bank = $bank->toArray();
            } else {
                $message = 'Please update your bank details';
                $bank = "";
            }
        }catch(\Exception $e){
            $status = 500;
            $message = "Something went wrong ".$e->getMessage();
            $bank = "";
        }
        $response = [
            "message" => $message,
            "data" => $bank
        ];
        return response($response, $status);
    }

    /**
     * @param Requests\BankDetails $request
     * @param                      $id
     *
     * @return \Illuminate\Contracts\Routing\ResponseFactory|\Symfony\Component\HttpFoundation\Response
     */
    public function updateBankDetails(Requests\BankDetails $request,$id)
    {
        try {
            $user = User::findOrFail($id);
            $vendor = $user->vendor()->first();
            $billing = $vendor->bankInfo()->first();
            if ($billing != null) { // Update If exists
                $data = $request->all();
                unset($data['_method']);
                $vendor->bankInfo()->update($data);
            } else { // Insert if not exists
                $data = $request->all();
                unset($data['_method']);
                $data['vendor_id'] = $vendor->id;
                $data['created_at'] = Carbon::now();
                $data['updated_at'] = Carbon::now();
                $vendor->bankInfo()->insert($data);
            }
            $status = 200;
            $message = "saved successfully";
            $billingInfo = $vendor->bankInfo()->first();
        } catch (\Exception $e) {
            $billingInfo = "";
            $status = 500;
            $message = "something went wrong ".$e->getMessage();
        }
        $response = [
            "message" => $message,
            "data"=>$billingInfo
        ];
        return response($response, $status);
    }

    /**
     * @param Requests\ImagesRequest $request
     * @param                        $id
     *
     * @return \Illuminate\Contracts\Routing\ResponseFactory|\Symfony\Component\HttpFoundation\Response
     */
    public function addImages(Requests\ImagesRequest $request,$id)
    {
        try {
            $file = $request->image_name;
            $user = User::findOrFail($id);
            $maxUploadLimit = (int) env('VENDOR_IMAGE_UPLOAD_LIMIT');
            $vendor = $user->vendor()->first();
            $images = $vendor->images()->count();
            if ($images == $maxUploadLimit) { // Do not allowed to upload more than 10 Images
                $status = 406;
                $message = "Cannot Upload more than " . $maxUploadLimit . " images";
            } else { // Insert if not reached to max limit
                $status = 200;
                $message = "saved successfully";
                $data = $request->all();
                /* File Upload Code */
                $vendorUploadPath = public_path() . env('VENDOR_FILE_UPLOAD');
                $vendorOwnDirecory = $vendorUploadPath . sha1($user->id);
                $vendorImageUploadPath = $vendorOwnDirecory . "/" . "extra_images";
                /* Create Upload Directory If Not Exists */
                if (! file_exists($vendorImageUploadPath)) {
                    File::makeDirectory($vendorImageUploadPath, $mode = 0777, true, true);
                }
                $random = mt_rand(1, 1000000);
                $extension = $file->getClientOriginalExtension();
                $filename = sha1($user->id . $random) . ".{$extension}";
                $file->move($vendorImageUploadPath, $filename);
                /* Rename file */
                $data['image_name'] = $filename;
                $data['vendor_id'] = $vendor->id;
                $data['created_at'] = Carbon::now();
                $data['updated_at'] = Carbon::now();
                $vendor->images()->insert($data);
            }
            $vendorImages = $vendor->images()->get();
            if($vendorImages!=null || !$vendorImages->isEmpty()){
                $vendorUploadPath = URL::asset(env('VENDOR_FILE_UPLOAD'));
                $vendorOwnDirecory = $vendorUploadPath . "/" . sha1($user->id) . "/" . "extra_images/";
                $i = 0;
                foreach($vendorImages as $vendorImage){
                    $vimages[$i] = $vendorImage;
                    if ($vimages[$i]->image_name == null) {
                        $vimages[$i]->image_name = $vimages[$i]->image_name;
                    } else {
                        $vimages[$i]->image_name = $vendorOwnDirecory . $vimages[$i]->image_name;
                    }
                    $i++;
                }
                $vendorImages = $vimages;
            }
        } catch (\Exception $e) {
            // echo $e->getMessage();
            $status = 500;
            $message = "something went wrong" . $e->getMessage();
            $vendorImages = "";
        }
        $images = $vendor->images()->count();
        $response = [
            "message" => $message,
            "data" => $vendorImages
        ];
        return response($response, $status);
    }

    /**
     * @param $id
     *
     * @return \Illuminate\Contracts\Routing\ResponseFactory|\Symfony\Component\HttpFoundation\Response
     */
    public function getImages($id)
    {
        try{
            $status = 200;
            $user = User::findOrFail($id);
            $vendor = $user->vendor()->first();
            $imageCount = $vendor->images()->count();
            if ($imageCount == 0) {
                $message = "Images not found. Please upload some";
                $images = "";
            } else {
                $message = "success";
                $images = $vendor->images()
                    ->get()
                    ->toArray();
                $vendorUploadPath = URL::asset(env('VENDOR_FILE_UPLOAD'));
                $url = $vendorUploadPath . "/" . sha1($user->id) . "/" . "extra_images/";
                for ($i = 0; $i < $imageCount; $i ++) {
                    $images[$i]['image_name'] = $url . $images[$i]['image_name'];
                }
            }
        }catch(\Exception $e){
            $status = 500;
            $message = "Something went wrong ".$e->getMessage();
        }
        $response = [
            "message" => $message,
            "data" => $images
        ];
        return response($response, $status);
    }

    /**
     * @param $userId
     * @param $id
     *
     * @return \Illuminate\Contracts\Routing\ResponseFactory|\Symfony\Component\HttpFoundation\Response
     */
    public function deleteImage($userId,$id)
    {
        try {
            $user = User::findOrFail($userId);
            $vendor = $user->vendor()->first();
            $image = VendorImages::where(array('id'=>$id,'vendor_id'=>$vendor->id))->first();
            if ($image!=null) {
                $image->delete();
                $status = 200;
                $message = "Image deleted successfully";
                $vendorUploadPath = public_path() . env('VENDOR_FILE_UPLOAD');
                $path = $vendorUploadPath . "/" . sha1($user->id) . "/" . "extra_images/";
                File::delete($path . $image->image_name);
            } else {
                $status = 500;
                $message = "image not found";
            }
        } catch (\Exception $e) {
            $status = 500;
            $message = "something went wrong ".$e->getMessage();
        }
        $response = [
            "message" => $message
        ];
        return response($response, $status);
    }

    /**
     * @param Requests\AddFacilityRequest $request
     * @param                             $id
     *
     * @return \Illuminate\Contracts\Routing\ResponseFactory|\Symfony\Component\HttpFoundation\Response
     */
    public function createFacility(Requests\AddFacilityRequest $request,$id)
    {   dd($request->all());
        try {
            $facility = $request->all();
            $facility = $this->unsetKeys(array(
                'duration'
            ), $facility);
            $user = User::findOrFail($id);
            $vendor = $user->vendor()->first();
            $newFacility = "";
            $facilityExists = AvailableFacility::where('vendor_id', '=', $vendor->id)->where('sub_category_id', '=', $facility['sub_category_id'])->count();
            if ($facilityExists) { // If Facility already exists
                $status = 406; // Not Acceptable
                $message = "Facility already exists";
            } else { // If not then create
                $status = 200;
                $message = "New facility added successfully";
                $facility['vendor_id'] = $vendor->id;
                $facility['created_at'] = Carbon::now();
                $facility['updated_at'] = Carbon::now();
                $newFacility = AvailableFacility::create($facility);
                $sessionUpdateData['duration'] = $request->duration;
                $durationStatus = $this->updateDuration($newFacility->id, $sessionUpdateData);
                $newFacility->duration = $request->duration;
            }
        } catch (\Exception $e) {
            $status = 500;
            $message = "Something went wrong : " . $e->getMessage();
            $newFacility = "";
        }
        $response = [
            "message" => $message,
            "data"=> $newFacility
        ];
        return response($response, $status);
    }

    /**
     * @param $facilityId
     * @param $sessionUpdateData
     *
     * @return bool
     */
    public function updateDuration($facilityId, $sessionUpdateData)
    {
        try {
            $checkFacilityInformation = SessionPackage::where('available_facility_id', '=', $facilityId)->first();
            if ($checkFacilityInformation != null) { // Update If Found
                $durationData = SessionPackage::where('available_facility_id', '=', $facilityId)->update($sessionUpdateData);
            } else { // Insert New If Not Found
                $sessionUpdateData['available_facility_id'] = $facilityId;
                $sessionUpdateData['created_at'] = Carbon::now();
                $sessionUpdateData['updated_at'] = Carbon::now();
                $packageType = PackageType::where('slug', '=', 'session')->first();
                $sessionUpdateData['package_type_id'] = $packageType->id;
                $durationData = SessionPackage::create($sessionUpdateData);
            }
            return true;
        } catch (\Exception $e) {
            return false;
        }
    }

    /**
     * @param $id
     *
     * @return \Illuminate\Contracts\Routing\ResponseFactory|\Symfony\Component\HttpFoundation\Response
     */
    public function getFacility($id)
    {
        $user = User::findOrFail($id);
        $vendor = $user->vendor()->first();
        $facilityCount = $vendor->facility()->count();
        if ($facilityCount == 0) {
            $status = 200;
            $message = "Facility not found. Please create some";
            $facilityCount = null;
            $facility = null;
        } else {
            $status = 200;
            $message = "success";
            $facility = $vendor->facility()
                ->get()
                ->toArray();
            for ($i = 0; $i < $facilityCount; $i ++) {
                $facility[$i]['category']['sub'] = SubCategory::find($facility[$i]['sub_category_id'])->toArray();
                $facility[$i]['category']['root'] = RootCategory::find($facility[$i]['category']['sub']['root_category_id'])->toArray();
                $sessionDuration = $this->getDurationData($facility[$i]['id']);
                $facility[$i]['duration'] = $sessionDuration;
            }
            $vendorUploadPath = URL::asset(env('VENDOR_FILE_UPLOAD'));
            $url = $vendorUploadPath . "/" . sha1($user->id) . "/" . "facility_images/";
            for ($i = 0; $i < $facilityCount; $i ++) {
                $facility[$i]['image'] = $url . $facility[$i]['image'];
            }
        }
        $response = [
            "message" => $message,
            "data" => $facility,
        ];
        return response($response, $status);
    }

    /**
     * @param $facilityId
     *
     * @return int
     */
    public function getDurationData($facilityId)
    {
        $packageType = PackageType::where('slug', '=', 'session')->first();
        $data = array(
            'available_facility_id' => $facilityId,
            'package_type_id' => $packageType->id
        );
        $durationData = SessionPackage::where($data)->first();
        if ($durationData != null) {
            return $durationData->duration;
        } else {
            return 0;
        }
    }

    /**
     * @param $uid
     * @param $id
     *
     * @return \Illuminate\Contracts\Routing\ResponseFactory|\Symfony\Component\HttpFoundation\Response
     */
    public function getFacilityById($uid,$id)
    {
        $status = 200;
        $message = "success";
        $user = User::findOrFail($uid);
        $vendor = $user->vendor()->first();
        $facility = $vendor->facility()->find($id);
        if ($facility != null) {
            $facility = $facility->toArray();
            $sessionDuration = $this->getDurationData($facility['id']);
            $facility['duration'] = $sessionDuration;
            $vendorUploadPath = URL::asset(env('VENDOR_FILE_UPLOAD'));
            $url = $vendorUploadPath . "/" . sha1($user->id) . "/" . "facility_images/";
            $facility['image'] = $url . $facility['image'];
        } else {
            $status = 200;
            $message = "Facility not found.";
            $facility = null;
        }
        $response = [
            "message" => $message,
            "facility" => $facility
        ];
        return response($response, $status);
    }

    /**
     * @param Requests\AddFacilityRequest $request
     * @param                             $uid
     * @param                             $id
     *
     * @return \Illuminate\Contracts\Routing\ResponseFactory|\Symfony\Component\HttpFoundation\Response
     */
    public function updateFacility(Requests\AddFacilityRequest $request, $uid, $id)
    {
        try {
            $facility = $request->all();
            $facilityInformation = "";
            $facility = $this->unsetKeys(array(
                '_method',
                'duration'
            ), $facility);
            $user = User::findOrFail($uid);
            $vendor = $user->vendor()->first();
            $status = 200;
            $message = "facility updated successfully";
            /* If File Exists then */
            if (isset($facility['image']) && ! empty($facility['image'])) {
                /* File Upload Code */
                $vendorUploadPath = public_path() . env('VENDOR_FILE_UPLOAD');
                $vendorOwnDirecory = $vendorUploadPath . sha1($user->id);
                $vendorImageUploadPath = $vendorOwnDirecory . "/" . "facility_images";
                /* Create Upload Directory If Not Exists */
                if (! file_exists($vendorImageUploadPath)) {
                    File::makeDirectory($vendorImageUploadPath, $mode = 0777, true, true);
                    chmod($vendorOwnDirecory, 0777);
                    chmod($vendorImageUploadPath, 0777);
                }
                $extension = $request->file('image')->getClientOriginalExtension();
                $filename = sha1($user->id . time()) . ".{$extension}";
                $request->file('image')->move($vendorImageUploadPath, $filename);
                chmod($vendorImageUploadPath, 0777);

                /* Rename file */
                $facility['image'] = $filename;
            }
            $facility['vendor_id'] = $vendor->id;
            AvailableFacility::where('id', '=', $id)->update($facility);
            $sessionUpdateData['duration'] = $request->duration;
            $sessionUpdatedData = $this->updateDuration($id, $sessionUpdateData);
            if($sessionUpdatedData){
                $facilityInformation = AvailableFacility::find($id);
                $facilityInformation->duration = $request->duration;
            }
        } catch (\Exception $e) {
            $status = 500;
            $message = "Something went wrong";
            $facilityInformation = "";
        }
        $response = [
            "message" => $message,
            "data" => $facilityInformation
        ];
        return response($response, $status);
    }
}
