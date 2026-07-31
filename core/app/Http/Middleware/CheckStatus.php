<?php

namespace App\Http\Middleware;

use Closure;
use Auth;

class CheckStatus
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

            if ($user->status  && $user->ev  && $user->sv  && $user->tv) {
                return $next($request);
            }else{
                if ($request->is('api/*')) {
                    $notify[] = 'You need to verify your account first.';
                    return apiResponse("unverified", "error", $notify, [
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
