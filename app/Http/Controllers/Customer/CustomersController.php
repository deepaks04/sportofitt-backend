<?php
namespace App\Http\Controllers\Customer;

use App\Area;
use Illuminate\Http\Request;
use App\Http\Requests;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Auth;
use File;
use URL;

class CustomersController extends Controller
{
    /**
     * Store a newly created resource in storage.
     *
     * @param Request $request            
     * @return Response
     */
    public function store($customerData)
    {
        $result = array();
        $result['status'] = true;
        try{
            DB::table('customers')->insert($customerData);
        }catch (\Exception $e){
            $result['status'] = false;
            $result['message'] = $e->getMessage();
        }
        return $result;
    }

    /**
     * @param Requests\CustomerProfileUpdateRequest $request
     *
     * @return \Illuminate\Contracts\Routing\ResponseFactory|\Symfony\Component\HttpFoundation\Response
     */

    public function updateProfileInformation(Requests\CustomerProfileUpdateRequest $request)
    {
        try {
            $status = 200;
            $message = "Updated Successfully";
            $user = Auth::user();
            $customer = $user->customer()->first();
            $userData = $request->all();
            $userKeys = array(
                'birthdate',
                'gender',
                '_method',
                'pincode',
                'phone_no',
                'area_id'
            );
            $userData = $this->unsetKeys($userKeys, $userData);
            /* File Upload */
            if (isset($userData['profile_picture']) && ! empty($userData['profile_picture'])) {
                /* File Upload Code */
                $vendorUploadPath = public_path() . env('CUSTOMER_FILE_UPLOAD');
                $vendorOwnDirecory = $vendorUploadPath . sha1($user->id);
                $vendorImageUploadPath = $vendorOwnDirecory . "/" . "profile_image";
                /* Create Upload Directory If Not Exists */
                if (! file_exists($vendorImageUploadPath)) {
                    File::makeDirectory($vendorImageUploadPath, $mode = 0777, true, true);
                    chmod($vendorOwnDirecory, 0777);
                    chmod($vendorImageUploadPath, 0777);
                }
                $extension = $request->file('profile_picture')->getClientOriginalExtension();
                $filename = sha1($user->id . time()) . ".{$extension}";
                $userData['profile_picture'] = $filename;
                $request->file('profile_picture')->move($vendorImageUploadPath, $filename);
                chmod($vendorImageUploadPath, 0777);
            }
            $user->update($userData);
            $customerData = $request->all();
            $customerKeys = array(
                'fname',
                'lname',
                '_method',
                'profile_picture',
                'email',
                'username'
            );
            $customerData = $this->unsetKeys($customerKeys, $customerData);
            $birthdate = date('Y-m-d', strtotime($request->birthdate));
            $customerData['birthdate'] = $birthdate;
            $customer->update($customerData);
        } catch (\Exception $e) {
            $status = 500;
            $message = "Updated Successfully";
        }
        $response = [
            "message" => $message
        ];
        return response($response, $status);
    }

    /**
     * get all information filled by customer
     * @return \Illuminate\Contracts\Routing\ResponseFactory|\Symfony\Component\HttpFoundation\Response
     */

    public function getProfileInformation()
    {
        try {
            $user = Auth::user();
            $user['customer'] = $user->customer()->first();
            $status = 200;
            $message = "Success";
            if ($user->profile_picture == null) {
                $user['profile_picture'] = $user->profile_picture;
            } else {
                $vendorUploadPath = URL::asset(env('CUSTOMER_FILE_UPLOAD'));
                $vendorOwnDirecory = $vendorUploadPath . "/" . sha1($user->id) . "/" . "profile_image/";
                $user['profile_picture'] = $vendorOwnDirecory . $user->profile_picture;
            }
            $area = Area::find($user['customer']->area_id);
            $user['customer']['area'] = $area->name;
        } catch (\Exception $e) {
            $status = 200;
            $message = "Success " . $e->getMessage();
            $user="";
        }
        $response = [
            "message" => $message,
            "user" => $user
        ];
        return response($response, $status);
    }

}
