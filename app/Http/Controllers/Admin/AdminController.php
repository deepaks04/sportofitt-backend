<?php
namespace App\Http\Controllers\Admin;

use App\Area;
use App\Status;
use App\User;
use Illuminate\Http\Request;
use App\Http\Requests;
use App\Http\Controllers\Controller;
use Auth;
use App\Role;

class AdminController extends Controller
{

    /**
     * Handle an incoming request.
     *
     * @param \Illuminate\Http\Request $request            
     * @param \Closure $next            
     * @return mixed
     */
    public function __construct()
    {
        $this->middleware('auth', [
            'except' => [
                'create'
            ]
        ]);
        $this->middleware('admin', [
            'except' => [
                'create'
            ]
        ]);
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
}
