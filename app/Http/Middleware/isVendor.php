<?php

namespace App\Http\Middleware;

use App\Role;
use App\User;
use Closure;
use Symfony\Component\HttpFoundation\Request;

class isVendor
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        if($request->uid!=null){
            $role = Role::where('slug', '=', 'vendor')->first();
            try{
                $user = User::findOrFail($request->uid);
                if ($user->role_id != $role->id) {
                    return response('Unauthorized.', 401);
                }
            }catch(\Exception $e){
                $status = 500;
                $message = "something went wrong".$e->getMessage();
                $response = [
                    "message" => $message,
                    "data"=>""
                ];
                return response($response, $status);
            }
        }
        return $next($request);
    }
}
