<?php

namespace App\Http\Controllers\Vendor;

use App\Duration;
use Carbon\Carbon;
use Faker\Provider\DateTime;
use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use App\AvailableFacility;
use App\PackageType;
use App\SessionPackage;
use App\SessionPackageChild;
use Illuminate\Support\Facades\DB;

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
                $packageType = PackageType::where('slug','=','package')->first();
                $request->created_at = Carbon::now();
                $request->updated = Carbon::now();
                $parentData = $request->all();
                $childData = $request->all();
                $parentData = $this->unsetKeys(array('child_id','is_peak','actual_price','discount','package_id','month'),$parentData);
                $parentData['package_type_id'] = $packageType->id;
                $package = SessionPackage::create($parentData);
                $childData = $this->unsetKeys(array('child_id','package_id','available_facility_id','package_type_id','name','description'),$childData);
                $childData['session_package_id'] = $package->id;
                $packageChild = SessionPackageChild::create($childData);
                $packageInformation['parent'] = SessionPackage::find($package->id);
                $packageInformation['child'] = $packageInformation['parent']->child()->orderBy('created_at','DESC')->first()->toArray();
            }else{ //Update Existing Package & Create New Child Row in Table
                $childData = $request->all();
                $childData = $this->unsetKeys(array('child_id','package_id','available_facility_id','package_type_id','name','description'),$childData);
                $childData['session_package_id'] = $request->package_id;
                if($request->child_id!=0){ // Update Previous Child Row (Inactive)
                    SessionPackageChild::where('id',$request->child_id)->update(array('is_active'=>0));
                }
                $packageChild = SessionPackageChild::create($childData);
                $packageInformation['parent'] = SessionPackage::find($request->package_id);
                $packageInformation['child'] = $packageChild->toArray();
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
                "package" => ""
            ];
        }
        return response($response,$status);
    }

    public function createOpeningTime(Requests\SessionRequest $request){
        try{
            $status = 200;
            $message = "Session Created Successfully";
            $start = strtotime($request->start);
            $end = strtotime($request->end);
            $timeDifference = $end - $start;
            //$timeDifference = date('i:s', $timeDifference);
            $timeDifference = round(abs($timeDifference) / 60,2);

            $start = date('H:i:s',$start);
            $end = date('H:i:s',$end);

            /* Check First Duration is Available Or Not */
            $checkFacilityInformation = SessionPackage::where('available_facility_id','=',$request->available_facility_id)->first();

            if($checkFacilityInformation!=null && $checkFacilityInformation->duration!=null){
                $checkFacilityInformation = $checkFacilityInformation->toArray();
                if($timeDifference>=$checkFacilityInformation['duration']){ // If time Difference Matched
                    if($request->session_id==0){ //Create Session First Time
                        $packageType = PackageType::where('slug','=','session')->first();
                        $request->created_at = Carbon::now();
                        $request->updated = Carbon::now();
                        $parentData = $request->all();
                        $childData = $request->all();
                        $parentData = $this->unsetKeys(array('is_peak','actual_price','discount','session_id','day','start','end'),$parentData);
                        $parentData['package_type_id'] = $packageType->id;
                        //$session = SessionPackage::create($parentData);
                        $session = SessionPackage::where('available_facility_id','=',$request->available_facility_id)->first()->toArray();
                        $childData = $this->unsetKeys(array('session_id','available_facility_id','name','description'),$childData);
                        $childData['session_package_id'] = $session['id'];
                        //$sameTimeExists = SessionPackageChild::where('start','<',$start)->where('end','>',$start)->where('day','=',$request->day)->where('is_active','=',1)->orWhere('end','>',$end)->where('start','<',$end)->where('day','=',$request->day)->where('is_active','=',1)->orderBy('created_at','DESC')->first();
                        //DB::enableQueryLog();
                        //$sameTimeExists = SessionPackageChild::where('start','<',$start)->where('end','>',$start)->where('is_active','=',1)->orWhere('end','>',$end)->where('start','<',$end)->where('is_active','=',1)->orderBy('created_at','DESC')->first();

                        //$sameTimeExists = SessionPackageChild::whereBetween('start',[$start,$end])->orWhereBetween('end',[$start,$end])->where('day','=',$childData['day'])->count();
                        $sameTimeExists = DB::select(DB::raw("SELECT count(*) as cnt FROM session_package_child WHERE ('".$start."' BETWEEN start AND end OR '".$end."' BETWEEN start AND end) AND day=".$childData['day']." AND session_package_id=".$session['id']));
                        //$queries = DB::getQueryLog();

                        if($sameTimeExists[0]->cnt>0){ //Check If Same Time Already Exists
                            $message = "Time Already Exists";
                            $sessionInformation = "";
                        }else{
                            $sessionChild = SessionPackageChild::create($childData);
                            $sessionInformation['parent'] = SessionPackage::find($session['id']);
                            $sessionInformation['child'] = $sessionInformation['parent']->child()->orderBy('created_at','DESC')->first()->toArray();
                        }
                    }else{ //Update Existing Session & Create New Child Row in Table
                        $message = "Session Updated Successfully";
                        $childData = $request->all();
                        $childData = $this->unsetKeys(array('child_id','session_id','available_facility_id','name','description'),$childData);
                        //$childData['id'] = $request->session_id;
                        /*if($request->child_id!=0){
                            $sameTimeExists = SessionPackageChild::where('start','<',$start)->where('end','>',$start)->where('day','=',$request->day)->where('id','!=',$request->child_id)->where('is_active','=',1)->orWhere('end','>',$end)->where('start','<',$end)->where('day','=',$request->day)->where('id','!=',$request->child_id)->where('is_active','=',1)->orderBy('created_at','DESC')->first();
                        }else{
                            $sameTimeExists = SessionPackageChild::where('start','<',$start)->where('end','>',$start)->where('day','=',$request->day)->where('is_active','=',1)->orWhere('end','>',$end)->where('start','<',$end)->where('day','=',$request->day)->where('is_active','=',1)->orderBy('created_at','DESC')->first();
                        }*/
                        //DB::enableQueryLog();
                        //$sameTimeExists = SessionPackageChild::whereBetween('start',[$start,$end])->orWhereBetween('end',[$start,$end])->where('id','!=',$request->session_id)->count();
                        //$sameTimeExists = SessionPackageChild::select(DB::Raw("start between $start and $end or end between $start and $end"));
                        $sessionParentData = SessionPackage::where('available_facility_id','=',$request->available_facility_id)->first()->toArray();
                        $sameTimeExists = DB::select(DB::raw("SELECT count(*) as cnt FROM session_package_child WHERE ('".$start."' BETWEEN start AND end OR '".$end."' BETWEEN start AND end) AND (id!=".$request->session_id." AND session_package_id=".$sessionParentData['id'].") AND day=".$childData['day']));
                        //$queries = DB::getQueryLog();

                        if($sameTimeExists[0]->cnt>0){ //Check If Same Time Already Exists
                            $message = "Time Already Exists";
                            $sessionInformation = "";
                        }else{
                            //if($request->child_id!=0){ // Update Previous Child Row (Inactive)
                            $sessionChild = SessionPackageChild::where('id',$request->session_id)->update($childData);
                            $sessionInformation['parent'] = $sessionParentData;
                            $sessionInformation['child'] = SessionPackageChild::find($request->session_id);
                            //}
                            //$packageChild = SessionPackageChild::create($childData);
                            //$sessionInformation['parent'] = SessionPackage::find($request->session_id);
                            //$sessionInformation['child'] = $sessionInformation['parent']->child()->where(array('is_active'=>1))->first()->toArray();
                        }
                    }
                }else{ // Difference not matched
                    $message = "Specified duration not matched with current time difference";
                    $sessionInformation = "";
                }
            }else{ // No Record Found
                $message = "Please Update Session duration first";
                $sessionInformation = "";
            }
            $response = [
                "message" => $message,
                "session" => $sessionInformation
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



    public function updateDuration(Requests\DurationRequest $request){
        try{
            $status = 200;
            $message = "Duration updated successfully";
            $duration = $request->duration;
            //$duration = date('H:i:s',$duration);
            $checkFacilityInformation = SessionPackage::where('available_facility_id','=',$request->available_facility_id)->first();
            if($checkFacilityInformation!=null){ //Update If Found
                $data['available_facility_id'] = $request->available_facility_id;
                $data['duration'] = $duration;
                $durationData = SessionPackage::where('available_facility_id','=',$request->available_facility_id)->update($data);
            }else{ //Insert New If Not Found
                $data['available_facility_id'] = $request->available_facility_id;
                $data['duration'] = $duration;
                $data['created_at'] = Carbon::now();
                $data['updated_at'] = Carbon::now();
                $packageType = PackageType::where('slug','=','session')->first();
                $data['package_type_id'] = $packageType->id;
                $durationData = SessionPackage::create($data);
                //$durationData = $durationData->get()->toArray();
            }
        }catch (\Exception $e){
            $status = 500;
            $message = "Something went wrong ".$e->getMessage();
        }
        $response = [
            "message" => $message,
        ];
        return response($response,$status);
    }


    public function getDuration(){
        $status = 200;
        $message = "success";
        $duration = Duration::all();
        $response = [
            "message" => $message,
            "duration" => $duration
        ];
        return response($response,$status);
    }
}
