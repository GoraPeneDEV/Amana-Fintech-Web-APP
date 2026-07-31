<?php

namespace App\Http\Controllers\Api\Agent\Auth;

use App\Http\Controllers\Controller;
use App\Models\Agent;
use App\Models\AgentPasswordReset;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ForgotPasswordController extends Controller
{
    public function sendResetCode(Request $request)
    {
        $validator = Validator::make($request->all(), [
            ...mobileNumberValidationRule()
        ]);

        if ($validator->fails()) {
            return apiResponse("validation_error", "error", $validator->errors()->all());
        }

        $country = getUserSelectCountry();
        $agent   = Agent::where('mobile', $request->mobile_number)->where('dial_code', $country->dial_code)->first();

        if (!$agent) {
            $notify[] = 'The account could not be found';
            return apiResponse("user_not_found", "error", $notify);
        }

        $lastReset = AgentPasswordReset::where('mobile', $agent->mobile)
            ->orderBy('created_at', 'desc')
            ->first();

        $cooldownTime = 120;

        if ($lastReset && $lastReset->created_at->diffInSeconds(now()) < $cooldownTime) {
            $remainingTime = $cooldownTime - $lastReset->created_at->diffInSeconds(now());

            $notify[] = 'Please wait for ' . getAmount($remainingTime) . ' seconds before requesting a new code.';
            return apiResponse("cooldown_active", "error", $notify, [
                'remaining_seconds' => getAmount($remainingTime)
            ]);
        }

        AgentPasswordReset::where('mobile', $agent->mobile)->delete();

        $code                 = verificationCode(6);

        $password             = new AgentPasswordReset();
        $password->mobile     = $agent->mobile;
        $password->token      = $code;
        $password->created_at = Carbon::now();
        $password->save();

        $agentIpInfo      = getIpInfo();
        $agentBrowserInfo = osBrowser();


        notify($agent, 'PASS_RESET_CODE', [
            'code'             => $code,
            'operating_system' => @$agentBrowserInfo['os_platform'],
            'browser'          => @$agentBrowserInfo['browser'],
            'ip'               => @$agentIpInfo['ip'],
            'time'             => @$agentIpInfo['time']
        ]);

        $mobile      = $agent->mobile;
        $response[] = 'Verification code sent to mobile';
        return apiResponse("code_sent", "success", $response, [
            'mobile' => $mobile
        ]);
    }

    public function verifyCode(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'code'   => 'required',
            ...mobileNumberValidationRule(digitValidation: false)
        ]);

        if ($validator->fails()) {
            return apiResponse("validation_error", "error", $validator->errors()->all());
        }

        $code =  $request->code;

        if (AgentPasswordReset::where('token', $code)->where('mobile', $request->mobile_number)->count() != 1) {
            $notify[] = "Verification code doesn't match";
            return apiResponse("code_not_match", "error", $notify);
        }

        $response[] = 'You can change your password.';
        return apiResponse("success", "success", $response);
    }

    public function reset(Request $request)
    {
        $validator = Validator::make($request->all(), $this->validationRules());

        if ($validator->fails()) {
            return apiResponse("validation_error", "error", $validator->errors()->all());
        }

        $reset = AgentPasswordReset::where('token', $request->token)->orderBy('created_at', 'desc')->first();

        if (!$reset) {
            $response[] = 'Invalid verification code';
            return apiResponse("invalid_code", "error", $response);
        }

        $agent = Agent::where('mobile', $reset->mobile)->first();
        $agent->password = bcrypt($request->pin);
        $agent->save();

        $agentIpInfo  = getIpInfo();
        $agentBrowser = osBrowser();

        notify($agent, 'PASS_RESET_DONE', [
            'operating_system' => @$agentBrowser['os_platform'],
            'browser' => @$agentBrowser['browser'],
            'ip' => @$agentIpInfo['ip'],
            'time' => @$agentIpInfo['time']
        ]);

        $response[] = 'PIN changed successfully';
        return apiResponse("password_changed", "success", $response);
    }

    protected function validationRules()
    {
        return [
            'token' => 'required',
            ...pinValidationRule(isConfirm: true),
            ...mobileNumberValidationRule(digitValidation: false),
        ];
    }
}
