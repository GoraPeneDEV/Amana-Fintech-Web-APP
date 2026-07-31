<?php

namespace App\Http\Middleware;

use App\Constants\Status;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class MobileNumberVerification
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next, $guard = null): Response
    {


        if ($guard) {
            $check = Auth::guard($guard)->check();
            $user = auth()->guard($guard)->user();
        } else {
            $guard = 'user';
            $user = auth()->user();
            $check = Auth::check();
        }

        if ($check) {

            if ($user->sv != Status::VERIFIED) {
                return to_route("$guard.authorization");
            } else {
                return $next($request);
            }
        }
    }
}
