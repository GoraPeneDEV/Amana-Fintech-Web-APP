<?php

namespace App\Http\Middleware;

use App\Constants\Status;
use Closure;
use Illuminate\Http\Request;

class RegistrationStep
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next, $guard = null)
    {
        

        if ($guard) {
            if ($request->is('api/*')) {
                $user = auth()->user();
            } else {
                $user = auth()->guard($guard)->user();
            }
        } else {
            $guard = 'user';
            $user = auth()->user();
        }

        if (!$user->profile_complete) {
            if ($request->is('api/*')) {
                $notify[] = 'Please complete your profile to proceed';
                return apiResponse("profile_incomplete", "error", $notify);
            } else {
                return to_route("$guard.data");
            }
        }

        return $next($request);
    }
}
