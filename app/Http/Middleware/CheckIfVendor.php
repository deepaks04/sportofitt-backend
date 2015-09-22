<?php

namespace App\Http\Middleware;

use Closure;
use Auth;
use App\User;
use App\Role;

class CheckIfVendor
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
        $role = Role::where('slug','=','vendor')->first();
        $user = Auth::user();
        if ($user->role_id != $role->id) {
            return response('Unauthorized.', 401);
        }
        return $next($request);
    }
}
