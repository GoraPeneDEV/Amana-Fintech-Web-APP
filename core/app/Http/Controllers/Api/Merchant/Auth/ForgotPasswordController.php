<?php

namespace App\Http\Controllers\Api\Merchant\Auth;

use App\Http\Controllers\Controller;
use App\Models\Merchant;
use App\Models\MerchantPasswordReset;
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

        $country  = getUserSelectCountry();
        $merchant = Merchant::where('mobile', $request->mobile_number)->where('dial_code', $country->dial_code)->first();

        if (!$merchant) {
            $notify[] = 'The account could not be found';
            return apiResponse("merchant_not_found", "error", $notify);
        }

        $lastReset = MerchantPasswordReset::where('mobile', $merchant->mobile)
            ->orderBy('created_at', 'desc')
            ->first();

        $cooldownTime = 120;

        if ($lastReset && $lastReset->created_at->diffInSeconds(now()) < $cooldownTime) {
            $remainingTime = $cooldownTime - $lastReset->created_at->diffInSeconds(now());

            $notify[] = 'Please wait for ' . $remainingTime . ' seconds before requesting a new code.';
            return apiResponse("cooldown_active", "error", $notify, [
                'remaining_seconds' => $remainingTime
            ]);
        }

        MerchantPasswordReset::where('mobile', $merchant->mobile)->delete();

        $code                 = verificationCode(6);
        $password             = new MerchantPasswordReset();
        $password->mobile     = $merchant->mobile;
        $password->token      = $code;
        $password->created_at = Carbon::now();
        $password->save();

        $merchantIpInfo      = getIpInfo();
        $merchantBrowserInfo = osBrowser();

        notify($merchant, 'PASS_RESET_CODE', [
            'code' => $code,
            'operating_system' => @$merchantBrowserInfo['os_platform'],
            'browser' => @$merchantBrowserInfo['browser'],
            'ip' => @$merchantIpInfo['ip'],
            'time' => @$merchantIpInfo['time']
        ]);

        $mobile = $merchant->mobile;
        $response[] = 'Verification code sent to mobile';
        return apiResponse("code_sent", "success", $response, [
            'mobile' => $mobile
        ]);
    }

    public function verifyCode(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'code' => 'required',
            ...mobileNumberValidationRule(digitValidation: false)
        ]);

        if ($validator->fails()) {
            return apiResponse("validation_error", "error", $validator->errors()->all());
        }
        $code =  $request->code;

        if (MerchantPasswordReset::where('token', $code)->where('mobile', $request->mobile_number)->count() != 1) {
            $notify[] = "Verification code doesn't match";
            return apiResponse("code_not_match", "error", $notify);
        }

        $response[] = 'You can change your PIN.';
        return apiResponse("success", "success", $response);
    }

    public function reset(Request $request)
    {
        $validator = Validator::make($request->all(), $this->rules());

        if ($validator->fails()) {
            return apiResponse("validation_error", "error", $validator->errors()->all());
        }

        $reset = MerchantPasswordReset::where('token', $request->token)->orderBy('created_at', 'desc')->first();
        if (!$reset) {
            $response[] = 'Invalid verification code';
            return apiResponse("invalid_code", "error", $response);
        }

        $merchant = Merchant::where('mobile', $reset->mobile)->first();
        $merchant->password = bcrypt($request->pin);
        $merchant->save();

        $merchantIpInfo = getIpInfo();
        $merchantBrowser = osBrowser();

        notify($merchant, 'PASS_RESET_DONE', [
            'operating_system' => @$merchantBrowser['os_platform'],
            'browser' => @$merchantBrowser['browser'],
            'ip' => @$merchantIpInfo['ip'],
            'time' => @$merchantIpInfo['time']
        ]);

        $response[] = 'PIN changed successfully';
        return apiResponse("password_changed", "success", $response);
    }

    protected function rules()
    {
        return [
            'token' => 'required',
            ...pinValidationRule(isConfirm: true),
            ...mobileNumberValidationRule(digitValidation: false)
        ];
    }
}
