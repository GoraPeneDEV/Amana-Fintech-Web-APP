<?php

namespace App\Http\Middleware;

use Closure;
use Auth;

class MobileVerify
{
      /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next, $guard = null)
    {
        if($guard){
            $check = Auth::guard($guard)->check();
            $user = auth()->guard($guard)->user();
        }
        else{
            $guard = 'user';
            $user = auth()->user();
            $check = Auth::check();
        }

        if ($check) {

            if ($user->status && $user->sv) {
                return $next($request);
            }else{
                if ($request->is('api/*')) {
                    $notify[] = 'You need to verify your mobile number first';
                    return apiResponse("mobile_unverified", "error", $notify, [
                        'user' => $user
                    ]);
                }else{
                    return to_route("$guard.authorization");
                }
            }
        }

        abort(403);
    }
}
