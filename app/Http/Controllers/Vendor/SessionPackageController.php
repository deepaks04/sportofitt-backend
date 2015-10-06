<?php

namespace App\Http\Controllers\Vendor;

use Carbon\Carbon;
use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use App\AvailableFacility;
use App\PackageType;
use App\SessionPackage;
use App\SessionPackageChild;

class SessionPackageController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('vendor');
    }

    public function types(){
        $types = PackageType::all();
        $status = 200;
        $response = [
            "message" => "success",
            "types" => $types
        ];
        return response($response,$status);
    }

    /**
     * Create Package For Facility
     * @param Requests\PackageRequest $request
     * @return \Illuminate\Contracts\Routing\ResponseFactory|\Symfony\Component\HttpFoundation\Response
     */
    public function createPackage(Requests\PackageRequest $request){
        try{
            $facility = AvailableFacility::findOrFail($request->available_facility_id);
            $status = 200;
            $message = "Success";
            if($request->package_id==0){ //Create Package First Time
                $packageType = PackageType::where('slug','=','package')->first();//dd($packageType->id);
                $request->created_at = Carbon::now();
                $request->updated = Carbon::now();
                $parentData = $request->all();
                $childData = $request->all();
                $parentData = $this->unsetKeys(array('is_peak','actual_price','discount','package_id','month'),$parentData);
                $parentData['package_type_id'] = $packageType->id;
                $package = SessionPackage::create($parentData);
                $childData = $this->unsetKeys(array('package_id','available_facility_id','package_type_id','name','description'),$childData);
                $childData['session_package_id'] = $package->id;
                $packageChild = SessionPackageChild::create($childData);
                $packageInformation['parent'] = SessionPackage::find($package->id);//->with('latestChild')->toArray();
                $packageInformation['child'] = $packageInformation['parent']->child()->orderBy('created_at','DESC')->first()->toArray();
            }else{ //Update Existing Package & Create New Child Row in Table
                $childData = $request->all();
                $childData = $this->unsetKeys(array('package_id','available_facility_id','package_type_id','name','description'),$childData);
                $childData['session_package_id'] = $request->package_id;
                $packageChild = SessionPackageChild::create($childData);
                $packageInformation['parent'] = SessionPackage::find($request->package_id);//->with('latestChild')->toArray();
                $packageInformation['child'] = $packageInformation['parent']->child()->orderBy('created_at','DESC')->first()->toArray();
            }
            $response = [
                "message" => $message,
                "package" => $packageInformation
            ];
        }catch(\Exception $e){
            $status = 500;
            $message = "Something went wrong ".$e->getMessage();
            $response = [
                "message" => $message,
            ];
        }
        return response($response,$status);
    }

    public function createSession(Requests\SessionRequest $request){
        try{
            $status = 200;
            $message = "Success";
            if($request->session_id==0){ //Create Session First Time
                $packageType = PackageType::where('slug','=','session')->first();//dd($packageType->id);
                $request->created_at = Carbon::now();
                $request->updated = Carbon::now();
                $parentData = $request->all();
                $childData = $request->all();
                $parentData = $this->unsetKeys(array('is_peak','actual_price','discount','session_id','day','start','end','duration'),$parentData);
                $parentData['package_type_id'] = $packageType->id;
                $session = SessionPackage::create($parentData);
                $childData = $this->unsetKeys(array('session_id','available_facility_id','name','description'),$childData);
                $childData['session_package_id'] = $session->id;
                $sessionChild = SessionPackageChild::create($childData);
                $sessionInformation['parent'] = SessionPackage::find($session->id);//->with('latestChild')->toArray();
                $sessionInformation['child'] = $sessionInformation['parent']->child()->orderBy('created_at','DESC')->first()->toArray();
            }else{ //Update Existing Session & Create New Child Row in Table
                $childData = $request->all();
                $childData = $this->unsetKeys(array('session_id','available_facility_id','name','description'),$childData);
                $childData['session_package_id'] = $request->session_id;
                $packageChild = SessionPackageChild::create($childData);
                $sessionInformation['parent'] = SessionPackage::find($request->session_id);//->with('latestChild')->toArray();
                $sessionInformation['child'] = $sessionInformation['parent']->child()->orderBy('created_at','DESC')->first()->toArray();
            }
            $response = [
                "message" => $message,
                "session" => $sessionInformation
            ];
            //dd($request->all());
        }catch(\Exception $e){
            $status = 500;
            $message = "Something went wrong ".$e->getMessage();
            $response = [
                "message" => $message,
            ];
        }
        return response($response,$status);
    }
}
