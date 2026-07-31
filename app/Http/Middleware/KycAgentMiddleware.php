<?php

namespace App\Http\Middleware;

use App\Constants\Status;
use Closure;
use Illuminate\Http\Request;

class KycAgentMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next)
    {

        $user = $request->is('api/*') ? auth()->user() : auth('agent')->user() ;
        if ($request->is('api/*') && ($user->kv == Status::KYC_UNVERIFIED || $user->kv == Status::KYC_PENDING)) {
            $notify[] = 'You are unable to withdraw due to KYC verification';
            return apiResponse("kyc_verification", "error", $notify);
        }
        if ($user->kv == Status::KYC_UNVERIFIED) {
            $notify[] = ['error', 'You are not KYC verified. For being KYC verified, please provide these information'];
            return to_route('agent.kyc.form')->withNotify($notify);
        }
        if ($user->kv == Status::KYC_PENDING) {
            $notify[] = ['warning', 'Your documents for KYC verification is under review. Please wait for admin approval'];
            return to_route('agent.home')->withNotify($notify);
        }
        return $next($request);
    }
}
