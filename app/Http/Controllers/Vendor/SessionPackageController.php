<?php

namespace App\Http\Controllers\Vendor;

use App\AvailableFacility;
use App\DayMaster;
use App\Duration;
use App\Http\Controllers\Controller;
use App\Http\Requests;
use App\MultipleSession;
use App\OpeningHour;
use App\PackageType;
use App\SessionBooking;
use App\SessionPackage;
use App\SessionPackageChild;
use Auth;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Input;

class SessionPackageController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('vendor');
        if (!Auth::guest()) {
            $this->user = Auth::user();
            $this->vendor = $this->user->vendor()->first();
        }
    }

    /**
     * @return \Illuminate\Contracts\Routing\ResponseFactory|\Symfony\Component\HttpFoundation\Response
     */
    public function types()
    {
        $types = PackageType::all();
        $status = 200;
        $response = [
            "message" => "success",
            "types" => $types
        ];
        return response($response, $status);
    }

    /**
     * Create Package For Facility
     * @param Requests\PackageRequest $request
     * @return \Illuminate\Contracts\Routing\ResponseFactory|\Symfony\Component\HttpFoundation\Response
     */
    public function createPackage(Requests\PackageRequest $request)
    {
        try {
            $facility = AvailableFacility::findOrFail($request->available_facility_id);
            $status = 200;
            $message = "Success";
            $packageType = PackageType::where('slug', '=', 'package')->first();
            $request->created_at = Carbon::now();
            $request->updated = Carbon::now();
            $parentData = Input::only('available_facility_id', 'name', 'description');
            $parentData['package_type_id'] = $packageType->id;
            $package = SessionPackage::create($parentData);
            $childData = Input::only('is_peak', 'actual_price', 'discount', 'month');
            $childData['session_package_id'] = $package->id;
            $packageChild = SessionPackageChild::create($childData);
            $packageInformation = SessionPackage::find($package->id);
            $packageChild = $packageChild->toArray();
            $packageInformation['session_package_id'] = $packageChild['session_package_id'];
            $packageInformation['month'] = $packageChild['month'];
            $packageInformation['actual_price'] = $packageChild['actual_price'];
            $packageInformation['discount'] = $packageChild['discount'];
            $packageInformation['is_peak'] = $packageChild['is_peak'];
        } catch (\Exception $e) {
            $status = 500;
            $message = "Something went wrong " . $e->getMessage();
            $packageInformation = "";
        }
        $response = [
            "message" => $message,
            "package" => $packageInformation
        ];
        return response($response, $status);
    }

    /**
     * @param Requests\PackageRequest $request
     * @param                         $id
     *
     * @return \Illuminate\Contracts\Routing\ResponseFactory|\Symfony\Component\HttpFoundation\Response
     */
    public function updatePackage(Requests\PackageRequest $request, $id)
    {
        try {
            $status = 200;
            $message = "Package data updated successfully";
            $parentData = Input::only('name', 'description');
            $package = SessionPackage::where('id', '=', $id)->update($parentData);
            $childData = Input::only('is_peak', 'actual_price', 'discount', 'month');
            $packageChild = SessionPackageChild::where('session_package_id', '=', $id)->update($childData);
            $packageInformation = SessionPackage::find($id);
            $packageChild = SessionPackageChild::where('session_package_id', '=', $id)->first();
            $packageChild = $packageChild->toArray();
            $packageInformation['session_package_id'] = $packageChild['session_package_id'];
            $packageInformation['month'] = $packageChild['month'];
            $packageInformation['actual_price'] = $packageChild['actual_price'];
            $packageInformation['discount'] = $packageChild['discount'];
            $packageInformation['is_peak'] = $packageChild['is_peak'];

        } catch (\Exception $e) {
            $status = 500;
            $message = "Something went wrong " . $e->getMessage();
            $packageInformation = "";
        }
        $response = [
            "message" => $message,
            "package" => $packageInformation
        ];
        return response($response, $status);
    }

    /**
     * @param Requests\PackageRequest $request
     * @param                         $id
     *
     * @return \Illuminate\Contracts\Routing\ResponseFactory|\Symfony\Component\HttpFoundation\Response
     */
    public function getPackage(Requests\PackageRequest $request, $id)
    {
        try {
            $packageType = PackageType::where('slug', '=', 'package')->first();
            $status = 200;
            $message = "Success";
            $packages = "";
            $facilityDetils = SessionPackage::where(array(
                'available_facility_id' => $id,
                'package_type_id' => $packageType->id,
                'is_active' => 1
            ))->get();
            if (!$facilityDetils->isEmpty()) {
                $facilityDetils = $facilityDetils->toArray();
                $i = 0;
                foreach ($facilityDetils as $facilityDetil) {
                    $packages[$i] = $facilityDetil;
                    $packageChild = SessionPackageChild::where(array('session_package_id' => $facilityDetil['id'], 'is_active' => 1))->first();
                    if ($packageChild != null) {
                        $packageChild = $packageChild->toArray();
                        $packages[$i]['session_package_id'] = $packageChild['session_package_id'];
                        $packages[$i]['month'] = $packageChild['month'];
                        $packages[$i]['actual_price'] = $packageChild['actual_price'];
                        $packages[$i]['discount'] = $packageChild['discount'];
                        $packages[$i]['is_peak'] = $packageChild['is_peak'];
                    } else {
                        $packages[$i]['session_package_id'] = "";
                        $packages[$i]['actual_price'] = "";
                        $packages[$i]['discount'] = "";
                        $packages[$i]['is_peak'] = "";
                    }
                    $i++;
                }
            }
        } catch (\Exception $e) {
            $status = 500;
            $message = "Something went wrong " . $e->getMessage();
            $packages = "";
        }
        $response = [
            "message" => $message,
            "data" => $packages
        ];
        return response($response, $status);
    }

    /**
     * @param Requests\DeletePackageRequest $request
     * @param                               $id
     *
     * @return \Illuminate\Contracts\Routing\ResponseFactory|\Symfony\Component\HttpFoundation\Response
     */
    public function deletePackage(Requests\DeletePackageRequest $request, $id)
    {
        try {
            $status = 200;
            $message = "Package Deleted Successfully";
            SessionPackage::where('id', '=', $id)->update(array('is_active' => 0));
        } catch (\Exception $e) {
            $status = 500;
            $message = "Something went wrong " . $e->getMessage();
        }
        $response = [
            "message" => $message,
            "data" => ""
        ];
        return response($response, $status);
    }

    /**
     * @param Requests\SessionRequest $request
     *
     * @return \Illuminate\Contracts\Routing\ResponseFactory|\Symfony\Component\HttpFoundation\Response
     */
    public function createOpeningTime(Requests\SessionRequest $request)
    {
        try {
            $status = 200;
            $message = "Opening Hour Created Successfully";
            $start = strtotime($request->start);
            $end = strtotime($request->end);
            $timeDifference = $end - $start;
            $openingHour = "";
            $timeDifference = round(abs($timeDifference) / 60, 2);
            $start = date('H:i:s', $start);
            $end = date('H:i:s', $end);

            /* Check First Duration is Available Or Not */
            $checkFacilityInformation = SessionPackage::where('available_facility_id', '=', $request->available_facility_id)->first();
            if ($checkFacilityInformation != null && $checkFacilityInformation->duration != null) {
                $checkFacilityInformation = $checkFacilityInformation->toArray();
                if ($timeDifference >= $checkFacilityInformation['duration']) { // If time Difference Matched
                    //if($request->session_id==0){ //Create Session First Time
                    $packageType = PackageType::where('slug', '=', 'session')->first();
                    $request->created_at = Carbon::now();
                    $request->updated = Carbon::now();
                    $parentData = Input::only('available_facility_id');
                    $parentData['package_type_id'] = $packageType->id;
                    //$session = SessionPackage::create($parentData);
                    $session = SessionPackage::where(array(
                        'available_facility_id' => $request->available_facility_id,
                        'package_type_id' => $packageType->id
                    ))->first()->toArray();
                    $childData = Input::only('is_peak', 'start', 'end', 'day');
                    $childData['session_package_id'] = $session['id'];
                    $sameTimeExists = DB::select(DB::raw("SELECT count(*) as cnt FROM opening_hours WHERE
('" . $start . "' BETWEEN start AND end OR '" . $end . "' BETWEEN start AND end) AND day=" . $childData['day'] . "
 AND session_package_id=" . $session['id'] . " AND is_active = 1"));
//                    dd($sameTimeExists);
                    if ($sameTimeExists[0]->cnt > 0) { //Check If Same Time Already Exists
                        $status = 406;
                        $message = "Time Already Exists";
                        $openingHour = "";
                    } else {
                        $sessionChild = OpeningHour::create($childData);
                        $sessionInformation['parent'] = SessionPackage::find($session['id']);
                        $openingHour = $sessionInformation['parent']->ChildOpeningHours()->orderBy('created_at', 'DESC')->first()->toArray();
                    }
                } else { // Difference not matched
                    $status = 406;
                    $message = "Specified duration not matched with current time difference";
                    $openingHour = "";
                }
            } else { // No Record Found
                $status = 406;
                $message = "Please Update Session duration first";
                $openingHour = "";
            }
        } catch (\Exception $e) {
            $status = 500;
            $message = "Something went wrong " . $e->getMessage();
            $openingHour = "";

        }
        $response = [
            "message" => $message,
            "data" => $openingHour
        ];
        return response($response, $status);
    }

    /**
     * @param Requests\SessionRequest $request
     * @param                         $id
     *
     * @return \Illuminate\Contracts\Routing\ResponseFactory|\Symfony\Component\HttpFoundation\Response
     */
    public function updateOpeningTime(Requests\SessionRequest $request, $id)
    {
        try {
            $status = 200;
            $message = "Opening Hour updated Successfully";
            $start = strtotime($request->start);
            $end = strtotime($request->end);
            $timeDifference = $end - $start;
            $openingHour = "";
            $timeDifference = round(abs($timeDifference) / 60, 2);
            $start = date('H:i:s', $start);
            $end = date('H:i:s', $end);
            /* Check First Duration is Available Or Not */
            $checkFacilityInformation = SessionPackage::where('available_facility_id', '=', $request->available_facility_id)->first();
            if ($checkFacilityInformation != null && $checkFacilityInformation->duration != null) {
                $checkFacilityInformation = $checkFacilityInformation->toArray();
                if ($timeDifference >= $checkFacilityInformation['duration']) { // If time Difference Matched
                    $childData = Input::only('is_peak', 'start', 'end', 'day');
                    $packageType = PackageType::where('slug', '=', 'session')->first();
                    $sessionParentData = SessionPackage::where('available_facility_id', '=', $request->available_facility_id)->where('package_type_id', '=', $packageType->id)->first()->toArray();
                    $sameTimeExists = DB::select(DB::raw("SELECT count(*) as cnt FROM opening_hours WHERE ('" . $start . "' BETWEEN start AND end OR '" . $end . "' BETWEEN start AND end) AND (id!=" . $id . " AND session_package_id=" . $sessionParentData['id'] . ") AND day=" . $childData['day']));
                    if ($sameTimeExists[0]->cnt > 0) { //Check If Same Time Already Exists
                        $message = "Time Already Exists";
                        $sessionInformation = "";
                    } else {
                        $sessionChild = OpeningHour::where('id', $id)->update($childData);
                        $sessionInformation['parent'] = $sessionParentData;
                        $openingHour = OpeningHour::find($id);

                    }
                } else { // Difference not matched
                    $message = "Specified duration not matched with current time difference";
                    $openingHour = "";
                }
            } else { // No Record Found
                $message = "Please Update Session duration first";
                $openingHour = "";
            }

        } catch (\Exception $e) {
            $status = 500;
            $message = "Something went wrong " . $e->getMessage();
            $openingHour = "";
        }
        $response = [
            "message" => $message,
            "data" => $openingHour
        ];
        return response($response, $status);
    }

    /**
     * @param Requests\SessionRequest $request
     * @param                         $id
     *
     * @return \Illuminate\Contracts\Routing\ResponseFactory|\Symfony\Component\HttpFoundation\Response
     */
    public function getOpeningTime(Requests\SessionRequest $request, $id)
    {
        try {
            $status = 200;
            $message = "success";
            $packageType = PackageType::where('slug', '=', 'session')->first();
            $openingTime = "";
            $facilityDetils = SessionPackage::where(array(
                'available_facility_id' => $id,
                'package_type_id' => $packageType->id
            ))->first()->toArray();
            $data = array(
                'session_package_id' => $facilityDetils['id'],
                'is_active' => 1
            );
            $times = OpeningHour::where($data)->get();
            if (!$times->isEmpty()) {
                $openingTime = $times->toArray();
            }
        } catch (\Exception $e) {
            $status = 500;
            $message = "Something went wrong " . $e->getMessage();
            $openingTime = "";
        }
        $response = [
            "message" => $message,
            "data" => $openingTime
        ];
        return response($response, $status);
    }

    /**
     * @param Requests\DeleteOpeningTimeRequest $request
     * @param                                   $id
     *
     * @return \Illuminate\Contracts\Routing\ResponseFactory|\Symfony\Component\HttpFoundation\Response
     */
    public function deleteOpeningTime(Requests\DeleteOpeningTimeRequest $request, $id)
    {
        try {
            $status = 200;
            $message = "Opening Time Deleted Successfully";
            OpeningHour::where('id', '=', $id)->update(array('is_active' => 0));
        } catch (\Exception $e) {
            $status = 500;
            $message = "Something went wrong " . $e->getMessage();
        }
        $response = [
            "message" => $message,
            "data" => ""
        ];
        return response($response, $status);
    }

    /**
     * @param Requests\DurationRequest $request
     *
     * @return \Illuminate\Contracts\Routing\ResponseFactory|\Symfony\Component\HttpFoundation\Response
     */
    public function updateDuration(Requests\DurationRequest $request)
    {
        try {
            $status = 200;
            $message = "Duration updated successfully";
            $duration = $request->duration;
            $checkFacilityInformation = SessionPackage::where('available_facility_id', '=', $request->available_facility_id)->first();
            if ($checkFacilityInformation != null) { //Update If Found
                $data['available_facility_id'] = $request->available_facility_id;
                $data['duration'] = $duration;
                $durationData = SessionPackage::where('available_facility_id', '=', $request->available_facility_id)->update($data);
            } else { //Insert New If Not Found
                $data['available_facility_id'] = $request->available_facility_id;
                $data['duration'] = $duration;
                $data['created_at'] = Carbon::now();
                $data['updated_at'] = Carbon::now();
                $packageType = PackageType::where('slug', '=', 'session')->first();
                $data['package_type_id'] = $packageType->id;
                $durationData = SessionPackage::create($data);
            }
        } catch (\Exception $e) {
            $status = 500;
            $message = "Something went wrong " . $e->getMessage();
        }
        $response = [
            "message" => $message,
        ];
        return response($response, $status);
    }

    /**
     * @return \Illuminate\Contracts\Routing\ResponseFactory|\Symfony\Component\HttpFoundation\Response
     */
    public function getDuration()
    {
        $status = 200;
        $message = "success";
        $duration = Duration::all();
        $response = [
            "message" => $message,
            "duration" => $duration
        ];
        return response($response, $status);
    }

    /**
     * @param Requests\MultipleSessionRequest $request
     *
     * @return \Illuminate\Contracts\Routing\ResponseFactory|\Symfony\Component\HttpFoundation\Response
     */
    public function createSession(Requests\MultipleSessionRequest $request)
    {
        try {
            $status = 200;
            $message = "Session added successfully";
            $sessions = $request->all();
            $previousSession = MultipleSession::where(array(
                'available_facility_id' => $sessions['available_facility_id'],
                'is_active' => 1
            ))->count();
            if ($previousSession == 20) {
                $message = "You can't add more than 20 sessions.";
                $sessionData = "";
            } else {
                $sessions['created_at'] = Carbon::now();
                $sessions['updated_at'] = Carbon::now();
                $sessionData = MultipleSession::create($sessions);
                $sessionData = $sessionData->toArray();
            }
        } catch (\Exception $e) {
            $status = 500;
            $message = "Something went wrong " . $e->getMessage();
            $sessionData = "";
        }
        $response = [
            "message" => $message,
            "data" => $sessionData
        ];
        return response($response, $status);
    }

    /**
     * @param Requests\MultipleSessionRequest $request
     * @param                                 $id
     *
     * @return \Illuminate\Contracts\Routing\ResponseFactory|\Symfony\Component\HttpFoundation\Response
     */
    public function updateSession(Requests\MultipleSessionRequest $request, $id)
    {
        try {
            $status = 200;
            $message = "Session updated successfully";
            $sessions = $request->all();
            unset($sessions['_method']);
            MultipleSession::where('id', $id)->update($sessions);
            $sessionData = MultipleSession::find($id);
        } catch (\Exception $e) {
            $status = 500;
            $message = "Something went wrong " . $e->getMessage();
            $sessionData = "";
        }
        $response = [
            "message" => $message,
            "data" => $sessionData
        ];
        return response($response, $status);
    }

    /**
     * @param Requests\MultipleSessionRequest $request
     * @param                                 $id
     *
     * @return \Illuminate\Contracts\Routing\ResponseFactory|\Symfony\Component\HttpFoundation\Response
     */
    public function deleteSession(Requests\MultipleSessionRequest $request, $id)
    {
        try {
            $status = 200;
            $message = "Session deleted successfully";
            $sessions = $request->all();
            unset($sessions['_method']);
            MultipleSession::where('id', $id)->update(array('is_active' => 0));
            $sessionData = "";
        } catch (\Exception $e) {
            $status = 500;
            $message = "Something went wrong " . $e->getMessage();
            $sessionData = "";
        }
        $response = [
            "message" => $message,
            "data" => $sessionData
        ];
        return response($response, $status);
    }

    /**
     * @param Requests\SessionDataRequest $request
     * @param                             $id
     *
     * @return \Illuminate\Contracts\Routing\ResponseFactory|\Symfony\Component\HttpFoundation\Response
     */
    public function getSessionData(Requests\SessionDataRequest $request, $id)
    {
        try {
            $status = 200;
            $data = array(
                'available_facility_id' => $id,
                'is_active' => 1
            );
            $sessionData = MultipleSession::where($data)->get();
            if ($sessionData->isEmpty()) {
                $message = "Session data not found";
                $session = "";
            } else {
                $message = "success";
                $session = $sessionData->toArray();
            }
        } catch (\Exception $e) {
            $status = 500;
            $message = "Something went wrong " . $e->getMessage();
            $session = "";
        }
        $response = [
            "message" => $message,
            "data" => $session
        ];
        return response($response, $status);
    }

    /**
     * @param Requests\BlockCalendarRequest $request
     *
     * @return \Illuminate\Contracts\Routing\ResponseFactory|\Symfony\Component\HttpFoundation\Response
     */
    public function blockCalendar(Requests\BlockCalendarRequest $request)
    {
        try {
            $status = 200;
            $data = $request->all();
            $data = $this->unsetKeys(array('daySpan', 'dayOffset'), $data);
            $user = Auth::user();
            $sessionBooking = "";
            $data['startsAt'] = str_replace('/', '-', $data['startsAt']);
            $explodedDate = explode(" ",$data['startsAt']);
            $explodedDate[2] = strtoupper($explodedDate[2]);
            $data['startsAt'] = implode(" ",$explodedDate);
            $convertedDate = date('Y-m-d',strtotime($explodedDate[0]));
            $convertedTime = date('H:i:s',strtotime($explodedDate[1]));
            $convertedDateTime = $convertedDate." ".$convertedTime." ".$explodedDate[2];
            //$date = date_parse_from_format('m/d/Y H:i:s',$data['startsAt']);
            //$time = mktime($date['hour'], $date['minute'], $date['second'], $date['month'], $date['day'], $date['year']);
            $formattedDate = date('Y-m-d H:i:s',strtotime($convertedDateTime));
            $start = new Carbon($formattedDate);
            $data['startsAt'] = $start->toDateTimeString();
            $startTime = $start->toTimeString();
            $day = strtolower($start->format('l'));
            $dayMaster = DayMaster::where('slug', '=', $day)->first();
            $data['day'] = $dayMaster->id;
            $packageType = PackageType::where('slug', '=', 'session')->first();
            $sessionPackageMaster = SessionPackage::where(array(
                'available_facility_id' => $data['available_facility_id'],
                'package_type_id' => $packageType->id
            ))->first();
            if ($sessionPackageMaster != null) {
                $end = new Carbon($formattedDate);
                $sessionDuration = $sessionPackageMaster->duration;
                $data['endsAt'] = $end->addMinute($sessionDuration);
                $endTime = $end->toTimeString();
            }
            $openingTimeExists = OpeningHour::where('start', '<=', $startTime)
                ->where('end', '>=', $startTime)
                ->where('start', '<=', $endTime)
                ->where('end', '>=', $endTime)
                ->where('day', '=', $data['day'])
                ->where('is_active', '=', 1)
                ->where('session_package_id', '=', $sessionPackageMaster->id)
                ->first();
//            echo $startTime .'+>>'.$endTime;
//            echo $openingTimeExists->toSql();
//            dd($endTime);
            if ($openingTimeExists != null) { //Opening Time Available
                $startAt = $data['startsAt'];
                $endAt = $data['endsAt'];
                $blockTimeExists = SessionBooking::select('*')->whereRaw(" ('$startAt' between startsAt and endsAt or '$endAt' between startsAt and endsAt )")
                    ->where('is_active', '=', 1)
                    ->where('available_facility_id', '=', $data['available_facility_id'])
                    ->get();//->count();
                $availableFacility = AvailableFacility::find($data['available_facility_id']);//dd($availableFacility->slots);
                if ($availableFacility->slots > $blockTimeExists->count()) { //If Blocked Time Not Exists Already
                    $data['user_id'] = $user->id;
                    $data['booked_or_blocked'] = 2; //1 For Booked And 2 for Blocked.
                    $data['created_at'] = Carbon::now();
                    $data['updated_at'] = Carbon::now();
                    $openingTimeExists = $openingTimeExists->toArray();
                    $data['opening_hour_id'] = $openingTimeExists['id'];
                    $sessionBooking = SessionBooking::create($data);
                    $message = "Blocked Successfully";
                } else { //Blocked Time Already Exists for selected time & Date
                    $status = 406;
                    $message = "Facility is already booked for date & time you have selected";
                }
            } else { //No Opening Time Available For Selected Time & Date
                $status = 406;
                $message = "Opening time isn't available for selected time & date";
            }
        } catch (\Exception $e) {
            $status = 500;
            $message = "Something went wrong " . $e->getMessage();
            $sessionBooking = "";
        }
        $response = [
            "message" => $message,
            "data" => $sessionBooking
        ];
        return response($response, $status);
    }

    /**
     * @param Request $request
     * @param         $id
     */
    public function deleteBlockedData(Requests\BlockCalendarRequest $request, $id)
    {
        try {
            $session = $request->all();
            $getBlockData = SessionBooking::where('id' ,$id)->first();
            $blockTime = strtotime($getBlockData->startsAt);
            $nowDate = strtotime(Carbon::now());
            if($blockTime > $nowDate){
                $status = 200;
                $message = "Blocked Time Successfully Deleted";
                unset($session['_method']);
                $blockData = SessionBooking::where(array('id' => $id, 'user_id' => $this->user->id))->update(array('is_active' => 0));
            } else {
                $status = 406;
                $message = "Previous session can not be deleted.";
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
     *
     */
    public function updateBlockedData(Requests\UpdateBlockCalender $request, $id)
    {
        try {
            $session = $request->all();
            $session = $this->unsetKeys(array('_method', 'daySpan', 'dayOffset'), $session);
            $date = new Carbon($session['startsAt']);
            $sessionBookingData = SessionBooking::where(array('id' => $id, 'user_id' => $this->user->id))->first();
            $sessionPackageMaster = SessionPackage::where(array('available_facility_id' => $sessionBookingData['available_facility_id']))->first();
            if ($sessionPackageMaster != null) {
                $session['startsAt'] = $date->toDateTimeString();
                $startsAt = $date->toTimeString();

                $sessionDuration = $date->addMinute($sessionPackageMaster->duration);
//                 dd($sessionPackageMaster);
                $session['endsAt'] = $sessionDuration->toDateTimeString();
                $endsAt = $sessionDuration->toTimeString(); //date("Y-m-d H:i:s", strtotime($sessionDuration, $time));
//           dd($endsAt);
            }
            $day = strtolower($date->format('l'));
            $dayMaster = DayMaster::where('slug', '=', $day)->first();
            $session['day'] = $dayMaster->id;
            $openingTimeExists = OpeningHour::where('start', '<=', $startsAt)
                ->where('end', '>=', $startsAt)
                ->where('start', '<=', $endsAt)
                ->where('end', '>=', $endsAt)
                ->where('day', '=', $session['day'])
                ->where('is_active', '=', 1)
                ->where('session_package_id', '=', $sessionPackageMaster->id)
                ->first();
            if ($openingTimeExists != null) {
                $startsAt = $session['startsAt'];
                $endsAt = $session['endsAt'];
//                 dd($startsAt);
                $blockTimeExists = SessionBooking::select('*')
                    ->whereRaw(" ('$startsAt' between startsAt and endsAt or '$endsAt' between startsAt and endsAt )")
                    ->where('is_active', '=', 1)
                    ->where('available_facility_id', '=', $sessionBookingData['available_facility_id'])
                    ->get();
                $availableFacility = AvailableFacility::find($sessionBookingData['available_facility_id']);//dd($availableFacility->slots);
                if ($availableFacility->slots > $blockTimeExists->count()) { //If Blocked Time Not Exists Already
                    $blockData = SessionBooking::where(array('id' => $id, 'user_id' => $this->user->id))
                        ->update(array('startsAt' => $startsAt, 'endsAt' => $endsAt));
                    $status = 200;
                    $message = " Blocked Entry updated Successfully";
                } else {
                    $status = 406;
                    $message = "Booking or blocking time already exists for selected date & time";
                }
            } else {
                $status = 406;
                $message = "Opening time isn't available for selected time & date";
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
     * @param Request $request
     * @param         $yearMonth
     *
     * @return \Illuminate\Contracts\Routing\ResponseFactory|\Symfony\Component\HttpFoundation\Response
     */
    public function getBlockData(Request $request, $yearMonth)
    {
        try {
            $message = "success";
            $status = 200;
            $user = Auth::user();//->with('vendor');
            $facilityData = $user->vendor->facility()->where('is_active', 1)->get();
            if (!$facilityData->isEmpty()) {
                $facilities = $facilityData->toArray();
                $start = $yearMonth . '-01 00:00:00';
                $end = $yearMonth . '-31 11:59:59';
                $blockData = [];
                $blockId = 0;
                foreach ($facilities as $facility) {
                    $data = array(
                        'available_facility_id' => $facility['id'],
                        'is_active' => 1
                    );
                    $blockingData = SessionBooking::
                    where('available_facility_id', $facility['id'])
                        ->
                        where('is_active', 1)
                        ->where('startsAt', '>=', $start)
                        ->where('endsAt', '<=', $end)
                        ->get();
                    if (!$blockingData->isEmpty()) {
                        $blockData = array_merge($blockData, $blockingData->toArray());
//                        $blockId++;
                    }
                }
            }
        } catch (\Exception $e) {
            $status = 500;
            $message = "Something went wrong " . $e->getMessage();
            $blockData = "";
        }
        $response = [
            "message" => $message,
            "data" => $blockData
        ];
        return response($response, $status);
    }

    /**
     * @param Request $request
     * @param         $id
     * @param         $yearMonth
     *
     * @return \Illuminate\Contracts\Routing\ResponseFactory|\Symfony\Component\HttpFoundation\Response
     */
    public function getBlockDataFacilityWise(Request $request, $id, $yearMonth)
    {
        try {
            $message = "success";
            $status = 200;
            $user = Auth::user();//->with('vendor');
            $facilityData = $user->vendor->facility;
            if (!$facilityData->isEmpty()) {
                $facilities = $facilityData->toArray();
                $start = $yearMonth . '-01 00:00:00';
                $end = $yearMonth . '-31 11:59:59';
                $blockData = "";

                $facilityId = 0;

                $blockingData = SessionBooking::where('available_facility_id', $id)
                    ->where('is_active', 1)
                    ->where('startsAt', '>=', $start)
                    ->where('endsAt', '<=', $end)
                    ->get();
                if (!$blockingData->isEmpty()) {
                    $blockData = $blockingData->toArray();
                    $facilityId++;
                }
            }
        } catch (\Exception $e) {
            $status = 500;
            $message = "Something went wrong " . $e->getMessage();
            $blockData = "";
        }
        $response = [
            "message" => $message,
            "data" => $blockData
        ];
        return response($response, $status);
    }


    /**
     * @param Request $request
     * @param         $facility_id,$off_peak_count,$peak_count
     *
     * @return actual calculated price
     */
    public function getActualSessionPrice(Requests\SessionPriceRequest $request,$facility_id,$off_peak_count,$peak_count){
        try {
            $message = "success";
            $status = 200;
            $facilityData = AvailableFacility::where('id',$facility_id)->first();
            if ($facilityData) {
                $peak = ($facilityData->off_peak_hour_price * $off_peak_count)+($facilityData->peak_hour_price * $peak_count);
                $price = ['actual_price' => $peak];
            }
        } catch (\Exception $e) {
            $status = 500;
            $message = "Something went wrong " . $e->getMessage();
            $price = "";
        }
        $response = [
            "message" => $message,
            "data" => $price
        ];
        return response($response, $status);

    }
}
