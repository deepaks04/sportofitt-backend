<?php
namespace App\Http\Controllers\Vendor;

use App\BillingInfo;
use App\OpeningHour;
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
use Mockery\Exception;
use URL;
use App\Billing;
use Carbon\Carbon;
use App\AvailableFacility;
use App\SubCategory;


class VendorsController extends Controller
{

    public function __construct()
    {
        $this->middleware('auth', [
            'except' => [
                'store'
            ]
        ]);
        $this->middleware('vendor', [
            'except' => [
                'store'
            ]
        ]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param Request $request
     * @return Response
     */
    public function store($vendorData)
    {
        $result = array();
        $result['status'] = true;
        try{
            DB::table('vendors')->insert($vendorData);
        }catch (\Exception $e){
            $result['status'] = false;
            $result['message'] = $e->getMessage();
        }
        return $result;
    }

    /**
     * Update First Time Login Flag
     * * @return \Illuminate\Contracts\Routing\ResponseFactory|\Symfony\Component\HttpFoundation\Response
     */
    public function updateFirstLoginFlag()
    {
        try {
            $user = Auth::user();
            $flag = $user->vendor()->update(array(
                'is_processed' => 1
            ));
            $message = "Success";
            $status = 200;
        } catch (\Exception $e) {
            $message = "Something went wrong";
            $status = 500;
        }
        $response = [
            'message' => $message
        ];
        return response($response, $status);
    }

    /**
     * Get Vendor Profile Data
     *
     * @return \Illuminate\Contracts\Routing\ResponseFactory|\Symfony\Component\HttpFoundation\Response
     */
    public function getProfile()
    {
        $user = Auth::user();
        $vendorUploadPath = URL::asset(env('VENDOR_FILE_UPLOAD'));
        $vendorOwnDirecory = $vendorUploadPath . "/" . sha1($user->id) . "/" . "profile_image/";
        $vendor = $user->vendor;
        $myProfile['fname'] = $user->fname;
        $myProfile['lname'] = $user->lname;
        $myProfile['email'] = $user->email;
        if ($user->profile_picture == null) {
            $myProfile['profile_picture'] = $user->profile_picture;
        } else {
            $myProfile['profile_picture'] = $vendorOwnDirecory . $user->profile_picture;
        }
        $myProfile['username'] = $user->username;
        $myProfile['business_name'] = $vendor->business_name;
        $myProfile['address'] = $vendor->address;
        $myProfile['longitude'] = $vendor->longitude;
        $myProfile['latitude'] = $vendor->latitude;
        $myProfile['area_id'] = $vendor->area_id;
        $myProfile['contact'] = $vendor->contact;
        $myProfile['description'] = $vendor->description;
        $status = 200;
        $response = [
            'message' => 'success',
            'profile' => $myProfile
        ];
        return response($response, $status);
    }

    /**
     * Vendor Update Profile Settings
     *
     * @param Requests\UpdateVendorProfileRequest $request
     * @return \Illuminate\Contracts\Routing\ResponseFactory|\Symfony\Component\HttpFoundation\Response
     */
    public function updateProfile(Requests\UpdateVendorProfileRequest $request)
    {
        try {
            $status = 200;
            $message= "Settings updated successfully";
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
            $systemUser = User::find(Auth::user()->id);
            if ($request->file('profile_picture')!=null) {
                /* File Upload Code */
                $vendorUploadPath = public_path() . env('VENDOR_FILE_UPLOAD');
                $vendorOwnDirecory = $vendorUploadPath . sha1($systemUser->id);
                $vendorImageUploadPath = $vendorOwnDirecory . "/" . "profile_image";
                /* Create Upload Directory If Not Exists */
                if (! file_exists($vendorImageUploadPath)) {
                    File::makeDirectory($vendorImageUploadPath, $mode = 0777, true, true);
                }
                $extension = $request->file('profile_picture')->getClientOriginalExtension();
                $filename = sha1($systemUser->id . time()) . ".{$extension}";
                $request->file('profile_picture')->move($vendorImageUploadPath, $filename);
                /* Rename file */
                $user['profile_picture'] = $filename;
            }
            $systemUser->update($user);
            $systemUser->vendor()->update($vendor);
        } catch (\Exception $e) {
            echo $e->getMessage();
            $status = 500;
            $message="Something Went Wrong";
        }
        $response = [
            'message' => $message
        ];
        return response($response, $status);
    }

    /**
     * get billing information
     *
     * @return \Illuminate\Contracts\Routing\ResponseFactory|\Symfony\Component\HttpFoundation\Response
     */


    public function getBillingInformation()
    {
        $getUserData = $this->getVendorInfo();
        $user=$getUserData['user'];
        $vendor=$getUserData['vendor'];
        $billing = $vendor->billingInfo()->first();
        if ($billing != null) {
            $message = 'success';
            $status = 200;
            $billing = $billing->toArray();
        } else {
            $status = 200;
            $message = 'Please update your billing information';
        }
        $response = [
            "message" => $message,
            "billing" => $billing
        ];
        return response($response, $status);
    }

    /**
     * Insert/Update Billing Details
     *
     * @param Requests\Billing $request
     * @return \Illuminate\Contracts\Routing\ResponseFactory|\Symfony\Component\HttpFoundation\Response
     */
    public function updateBillingInformation(Requests\Billing $request)
    {
        try {
            $getUserData = $this->getVendorInfo();
            $user=$getUserData['user'];
            $vendor=$getUserData['vendor'];
            $billing = $vendor->billingInfo()->first();
            if ($billing!= null) { // Update If exists
                $data = $request->all();
                unset($data['_method']);
                if($data['registration_no'] == ''){
                   $data['registration_no']=null;
                }
                if($data['service_tax_no'] == ''){
                    $data['service_tax_no']=null;
                }
                if($data['pan_no'] == ''){
                   $data['pan_no']=null;
                }
                if($data['vat'] == ''){
                    $data['vat']=null;
                }
                $vendor->billingInfo()->update($data);
            } else { // Insert if not exists
                $data = $request->all();
                unset($data['_method']);
                $data['vendor_id'] = $vendor->id;
                $data['created_at'] = Carbon::now();
                $data['updated_at'] = Carbon::now();
                $vendor->billingInfo()->insert($data);
            }

            $status = 200;
            $message = "saved successfully";
        } catch (\Exception $e) {
            echo $e->getMessage();
            $status = 500;
            $message = "something went wrong";
        }
        $response = [
            "message" => $message
        ];
        return response($response, $status);
    }

    /**
     * Get Bank Details
     *
     * @return \Illuminate\Contracts\Routing\ResponseFactory|\Symfony\Component\HttpFoundation\Response
     */
    public function getBankDetails()
    {
        $getUserData = $this->getVendorInfo();
        $user=$getUserData['user'];
        $vendor=$getUserData['vendor'];
        $bank = $vendor->bankInfo()->first();
        if ($bank != null) {
            $message = 'success';
            $status = 200;
            $bank = $bank->toArray();
        } else {
            $status = 200;
            $message = 'Please update your bank details';
        }
        $response = [
            "message" => $message,
            "bank" => $bank
        ];
        return response($response, $status);
    }

    /**
     * Insert/Update Bank Details
     *
     * @param Requests\BankDetails $request
     * @return \Illuminate\Contracts\Routing\ResponseFactory|\Symfony\Component\HttpFoundation\Response
     */
    public function updateBankDetails(Requests\BankDetails $request)
    {
        try {
            $getUserData = $this->getVendorInfo();
            $user=$getUserData['user'];
            $vendor=$getUserData['vendor'];
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
        } catch (\Exception $e) {
            echo $e->getMessage();
            $status = 500;
            $message = "something went wrong";
        }
        $response = [
            "message" => $message
        ];
        return response($response, $status);
    }

    /**
     * Insert Images
     *
     * @param Requests\ImagesRequest $request
     * @return \Illuminate\Contracts\Routing\ResponseFactory|\Symfony\Component\HttpFoundation\Response
     */
    public function addImages(Requests\ImagesRequest $request)
    {
        try {
            $file = $request->image_name;
            $user = Auth::user();
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
        } catch (\Exception $e) {
            $status = 500;
            $message = "something went wrong" . $e->getMessage();
        }
        $images = $vendor->images()->count();
        $response = [
            "message" => $message,
            "image_count" => $images
        ];
        return response($response, $status);
    }

    /**
     * Get All extra images of vendor facility
     *
     * @return \Illuminate\Contracts\Routing\ResponseFactory|\Symfony\Component\HttpFoundation\Response
     */
    public function getImages()
    {
        $getUserData = $this->getVendorInfo();
        $user=$getUserData['user'];
        $vendor=$getUserData['vendor'];
        $imageCount = $vendor->images()->count();
        if ($imageCount == 0) {
            $status = 200;
            $message = "Images not found. Please upload some";
            $images = null;
        } else {
            $status = 200;
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
        $response = [
            "message" => $message,
            "images" => $images
        ];
        return response($response, $status);
    }

    /**
     * Delete Extra Image - one at a time
     *
     * @param Requests\DeleteImageRequest $request
     * @param
     *            $id
     * @return \Illuminate\Contracts\Routing\ResponseFactory|\Symfony\Component\HttpFoundation\Response
     */
    public function deleteImage(Requests\DeleteImageRequest $request, $id)
    {
        try {
            $getUserData = $this->getVendorInfo();
            $user=$getUserData['user'];
            $vendor=$getUserData['vendor'];
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
            $message = "something went wrong";
        }
        $response = [
            "message" => $message
        ];
        return response($response, $status);
    }

    /**
     * @param Requests\AddFacilityRequest $request
     *
     * @return \Illuminate\Contracts\Routing\ResponseFactory|\Symfony\Component\HttpFoundation\Response
     */
    public function createFacility(Requests\AddFacilityRequest $request)
    {
        try {
            $facility = $request->all();
            $facility = $this->unsetKeys(array(
                'duration'
            ), $facility);
            $getUserData = $this->getVendorInfo();
            $user=$getUserData['user'];
            $vendor=$getUserData['vendor'];
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
            }
        } catch (\Exception $e) {
            $status = 500;
            $message = "Something went wrong : " . $e->getMessage();
        }
        $response = [
            "message" => $message
        ];
        return response($response, $status);
    }

    /**
     * Get All facility created by vendor
     *
     * @return \Illuminate\Contracts\Routing\ResponseFactory|\Symfony\Component\HttpFoundation\Response
     */
    public function getFacility()
    {
        $getUserData = $this->getVendorInfo();
        $user=$getUserData['user'];
        $vendor=$getUserData['vendor'];
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
            "facility" => $facility,
            "facilityCount" => $facilityCount
        ];
        return response($response, $status);
    }

    /**
     * @param $id
     *
     * @return \Illuminate\Contracts\Routing\ResponseFactory|\Symfony\Component\HttpFoundation\Response
     */
    public function getFacilityById($id)
    {
        $status = 200;
        $message = "success";
        $getUserData = $this->getVendorInfo();
        $user=$getUserData['user'];
        $vendor=$getUserData['vendor'];
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
            $message = "Facility not found. Please create some";
            $facility = null;
        }
        $response = [
            "message" => $message,
            "facility" => $facility
        ];
        return response($response, $status);
    }


    public function enableDisableFacility(Requests\EnabledisableRequest $request,$id)
    {
        try {
            $getUserData = $this->getVendorInfo();
            $session=$request->all();
            $user=$getUserData['user'];
            $status = 200;
            $message = "facility successfully updated";
            unset($session['_method']);
            $blockData = AvailableFacility::where(array('id'=>$id))->update(array('is_active'=>$session['is_active']));
             } catch (\Exception $e) {
            $status = 500;
            $message = "something went wrong";
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
    public function updateFacility(Requests\AddFacilityRequest $request, $id)
    {
        try {
            $facility = $request->all();
            $facility = $this->unsetKeys(array(
                '_method',
                'duration'
            ), $facility);
            $getUserData = $this->getVendorInfo();
            $user=$getUserData['user'];
            $vendor=$getUserData['vendor'];
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
        } catch (\Exception $e) {
            $status = 500;
            $message = "Something went wrong";
        }
        $response = [
            "message" => $message
        ];
        return response($response, $status);
    }

    /**
     * @param Requests\FacilityInfoRequest $request
     * @param                              $id
     *
     * @return \Illuminate\Contracts\Routing\ResponseFactory|\Symfony\Component\HttpFoundation\Response
     */
    public function getFacilityDetailInformation(Requests\FacilityInfoRequest $request, $id)
    {
        $getUserData = $this->getVendorInfo();
        $user=$getUserData['user'];
        $vendor=$getUserData['vendor'];
        $facilities = $vendor->facility()->where(array('id'=>$id))->first();
        $sessionPackageInfoType = null;
        $openingHours = "";
        $packageInformation = "";
        $facilityData = "";
        if($facilities!=null){
            $facilities = $facilities->toArray();
            $facilityId = 0;
            $noOfFacility = 0;
            $facilityDetails = null;
                    $facilities['category']['sub'] = SubCategory::find($facilities['sub_category_id'])->toArray();
                    $facilities['category']['root'] = RootCategory::find($facilities['category']['sub']['root_category_id'])->toArray();
                    $sessionDuration = $this->getDurationData($facilities['id']);
                    $facilities['duration'] = $sessionDuration;

                $facilityDetails['information'] = $facilities;
                $facilityData = $facilities;
                $packages = SessionPackage::where('available_facility_id','=',$facilities['id'])->get();
                if($packages!=null){
                    $packages = $packages->toArray();

                    foreach ($packages as $package) {
                        $type = PackageType::where('id', '=', $package['package_type_id'])->first()->toArray();
                        if ($type['slug'] == 'package') {
                            $sessionPackageInfoType[$type['slug']][$i]['parent'] = $package;
                            $packageInformation[$i]['parent'] = $package;
                            $packageChild = SessionPackageChild::where(array('session_package_id'=>$package['id'],'is_active'=>1))->first();
                            if($packageChild!=null){
                                $packageChild = $packageChild->toArray();
                                $sessionPackageInfoType[$type['slug']][$i]['child'] = $packageChild;
                                $packageInformation[$i]['child'] = $packageChild;
                            }else{
                                $sessionPackageInfoType[$type['slug']][$i]['child'] = "";
                                $packageInformation[$i]['child'] = "";
                            }

                            $facilityId ++;
                        }
                        if($type['slug']=='session'){
                            //$sessionPackageInfoType[$type['slug']][$i]['parent'] = $package;
                            $packageChild = OpeningHour::where(array('session_package_id'=>$package['id'],'is_active'=>1))->get();
                            $j = 0;
                            if ($packageChild != null) {
                                $packageChild = $packageChild->toArray();
                                foreach($packageChild as $child){
                                    //$sessionPackageInfoType[$type['slug']][$j]['child'] = $child;
                                    $sessionPackageInfoType['opening_hours'][$j] = $child;
                                    $openingHours[$j] = $child;
                                    $j++;
                                }
                            }else{
                                $sessionPackageInfoType['opening_hours'][$j] = "";
                            }
                        }
                    }
                }

        }

        $status = 200;
        $response = [
            "message" => "success",
            "user" => $user,
            "facility" => $facilityData,
            "openingHours" => $openingHours,
            "packageInformation" => $packageInformation
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
    public function getVendorInfo()
    {
        $user = Auth::user();
        $vendor = $user->vendor()->first();
        $userData['user']=$user;
        $userData ['vendor']=$vendor;
        return $userData;
    }
}
