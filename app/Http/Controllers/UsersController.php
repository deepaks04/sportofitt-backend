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
use App\RootCategory;
use App\Area;

class UsersController extends Controller
{
    public function __construct()
    {
        $this->middleware('guest', [
            'only' => [
                'storeVendor',
                'confirm',
                'storeCustomer',
                'index'
            ],
            'except' => [
                'getRootCategory',
                'getSubCategory'
            ]
        ]);
    }
     /**
     * Store a newly created resource in storage.
     *
     * @param Request $request            
     * @return Response
     */
    public function storeVendor(Requests\CreateVendorRequest $request)
    {
        try {
            $status = 200;
            $message="Vendor Registered Successfully! Please login to continue";
            $role = Role::where('slug', 'vendor')->first();
            $userStatus = Status::where('slug', 'pending')->first();
            $userData = $request->all();
            $userData['password'] = bcrypt($request->password);
            $userData['is_active'] = 1; // Vendor need to verify email address so is_active set 1 to always
            $userData['status_id'] = $userStatus->id; // By Default Pending
            $userData['role_id'] = $role->id; // Vendor Role Id
            $userData['remember_token'] = csrf_token();
            $userData['updated_at'] = Carbon::now();
            $userData['created_at'] = Carbon::now();
            unset($userData['business_name']);
            unset($userData['password2']);
            $userId = DB::table('users')->insertGetId($userData);
            $vendorData['business_name'] = $request->business_name;
            $vendorData['user_id'] = $userId;
            $vendorData['updated_at'] = Carbon::now();
            $vendorData['created_at'] = Carbon::now();
            // Calling a method that is from the VendorsController
            $result = (new VendorsController())->store($vendorData);
            if (!$result['status']) {
                User::destroy($userId);
                throw new \Exception($result['message']);
            }
        } catch (\Exception $e) {
            $status = 500;
            $message= "Something Went Wrong, Vendor Registration Unsuccessful!" . $e->getMessage();
        }
        $response = [
            "message" => $message
        ];
        return response($response, $status);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param Request $request            
     * @return Response
     */
    public function storeCustomer(Requests\CreateCustomerRequest $request)
    {
        try {
            $status = 200;
            $message = "Registered Successfully! Please check your email for the instructions on how to confirm your account";
            $role = Role::where('slug', 'customer')->first();
            $userStatus = Status::where('slug', 'pending')->first();
            $userData = $request->all();
            $userData['password'] = bcrypt($request->password);
            $userData['is_active'] = 0; // will be 1 after email verification
            $userData['status_id'] = (int) $userStatus->id; // By Default Pending
            $userData['role_id'] = (int) $role->id; // User Role Id
            $userData['remember_token'] = csrf_token();
            $userData['updated_at'] = Carbon::now();
            $userData['created_at'] = Carbon::now();
            unset($userData['gender']);
            unset($userData['area_id']);
            $userId = DB::table('users')->insertGetId($userData);
            $customerData['gender'] = (int) $request->gender;
            $customerData['area_id'] = (int) $request->area_id;
            $customerData['user_id'] = (int) $userId;
            $customerData['updated_at'] = Carbon::now();
            $customerData['created_at'] = Carbon::now();
            // Calling a method that is from the VendorsController
            $result = (new CustomersController())->store($customerData);
            if ($result['status']) {
                Mail::send('emails.activation', $userData, function ($message) use($userData) {
                    $message->to($userData['email'])->subject('Account Confirmation');
                });
            } else {
                User::destroy($userId);
                throw new \Exception($result['message']);
            }
        } catch (\Exception $e) {
            $status = 500;
            $message="Something Went Wrong " . $e->getMessage();
        }
        $response = [
            "message" => $message
        ];
        return response($response, $status);
    }

    /**
     * @return \Illuminate\Contracts\Routing\ResponseFactory|\Symfony\Component\HttpFoundation\Response
     */
    public function getRootCategory()
    {
        $category = RootCategory::with('subCategory')->get();
        $status = 200;
        $response = [
            "message" => "success",
            "category" => $category
        ];
        return response($response, $status);
    }

    /**
     * @param $id
     *
     * @return \Illuminate\Contracts\Routing\ResponseFactory|\Symfony\Component\HttpFoundation\Response
     */
    public function getSubCategory($id)
    {
        $category = RootCategory::find($id); //
        if ($category != null) {
            $category = $category->subCategory()
                ->get()
                ->toArray();
            $message = "success";
            $status = 200;
        } else {
            $message = "fail, not found";
            $status = 404;
        }
        $response = [
            "message" => $message,
            "category" => $category
        ];
        return response($response, $status);
    }

    /**
     * @return \Illuminate\Contracts\Routing\ResponseFactory|\Symfony\Component\HttpFoundation\Response
     */
    public function getArea()
    {
        $area = Area::all();
        $status = 200;
        $response = [
            "message" => "success",
            "area" => $area
        ];
        return response($response, $status);
    }

    /**
     * Confirm User email & activate his account.
     *
     * @param string $confirmation            
     * @return Response
     */
    public function confirm($confirmation)
    {
        $user = User::where('remember_token', $confirmation)->first();
        if ($user == null) { // no record found
            $status = 200;
            $message= "Sorry!! No User found";
        } else {
            if ($user->is_active) { // already confirmed
                $status = 200;
                $message="Your account already confirmed";
            } else {
                User::where('remember_token', $confirmation)->update(array(
                    'is_active' => 1
                ));
                $status = 200;
                $message="Your account is confirmed, you can now login to your account";
            }
        }
        $response = [
            "message" => $message
        ];
        return response($response, $status);
    }
}
