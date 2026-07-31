<?php

namespace App\Http\Controllers\Api\Auth;

use App\Http\Controllers\Controller;
use App\Models\PasswordReset;
use App\Models\User;
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
        $user    = User::where('mobile', $request->mobile_number)->where('dial_code', $country->dial_code)->first();


        if (!$user) {
            $notify[] = 'The account could not be found';
            return apiResponse("user_not_found", "error", $notify);
        }

        $lastReset = PasswordReset::where('mobile', $user->mobile)
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

        PasswordReset::where('mobile', $user->mobile)->delete();

        $code                 = verificationCode(6);
        $password             = new PasswordReset();
        $password->mobile     = $user->mobile;
        $password->token      = $code;
        $password->created_at = Carbon::now();
        $password->save();

        $userIpInfo      = getIpInfo();
        $userBrowserInfo = osBrowser();
        notify($user, 'PASS_RESET_CODE', [
            'code'             => $code,
            'operating_system' => @$userBrowserInfo['os_platform'],
            'browser'          => @$userBrowserInfo['browser'],
            'ip'               => @$userIpInfo['ip'],
            'time'             => @$userIpInfo['time']
        ]);

        $mobile      = $user->mobile;
        $response[] = 'Verification code sent to mobile';
        return apiResponse("code_sent", "success", $response, [
            'mobile' => $mobile
        ]);
    }

    public function verifyCode(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'code'  => 'required',
            ...mobileNumberValidationRule(digitValidation: false)
        ]);

        if ($validator->fails()) {
            return apiResponse("validation_error", "error", $validator->errors()->all());
        }

        $code = $request->code;

        if (PasswordReset::where('token', $code)->where('mobile', $request->mobile_number)->count() != 1) {
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
        $reset = PasswordReset::where('token', $request->token)->orderBy('created_at', 'desc')->first();
        if (!$reset) {
            $response[] = 'Invalid verification code';
            return apiResponse("invalid_code", "error", $response);
        }

        $user           = User::where('mobile', $reset->mobile)->first();
        $user->password = bcrypt($request->pin);
        $user->save();

        $userIpInfo  = getIpInfo();
        $userBrowser = osBrowser();

        notify($user, 'PASS_RESET_DONE', [
            'operating_system' => @$userBrowser['os_platform'],
            'browser'          => @$userBrowser['browser'],
            'ip'               => @$userIpInfo['ip'],
            'time'             => @$userIpInfo['time']
        ]);

        $response[] = 'PIN changed successfully';
        return apiResponse("password_changed", "success", $response);
    }

    protected function validationRules()
    {
        return [
            'token' => 'required',
            ...pinValidationRule(isConfirm: true),
            ...mobileNumberValidationRule(digitValidation: false)
        ];
    }
}
