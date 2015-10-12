<?php

namespace App\Http\Controllers\Admin;

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
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        $role = Role::where('slug','=','superadmin')->first();
        $user = Auth::user();
        if ($user->role_id != $role->id){
            return response('Unauthorized.', 401);
        }
        return $next($request);
    }
}
