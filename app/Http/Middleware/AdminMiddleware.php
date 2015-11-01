<?php
namespace App\Http\Middleware;

use Closure;

class AdminMiddleware
{

    /**
     * Handle an incoming request.
     *
     * @param \Illuminate\Http\Request $request            
     * @param \Closure $next            
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        $role = Role::where('slug', '=', 'superadmin')->first();
        $user = Auth::user();
        if ($user->role_id != $role->id) {
            return response('Unauthorized.', 401);
        }
        return $next($request);
    }
}
