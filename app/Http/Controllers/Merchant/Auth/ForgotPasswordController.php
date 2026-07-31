<?php

namespace App\Http\Controllers\Merchant\Auth;

use App\Http\Controllers\Controller;
use App\Models\Merchant;
use App\Models\MerchantPasswordReset;
use Illuminate\Http\Request;

class ForgotPasswordController extends Controller
{
    public function showLinkRequestForm()
    {
        $pageTitle = "Account Recovery";
        return view('Template::merchant.auth.passwords.email', compact('pageTitle'));
    }

    public function sendResetCodeEmail(Request $request)
    {
        $request->validate([
            ...mobileNumberValidationRule()
        ]);

        if (!verifyCaptcha()) {
            $notify[] = ['error', 'Invalid captcha provided'];
            return back()->withNotify($notify);
        }

        $country  = getUserSelectCountry();
        $merchant = Merchant::where("mobile", $request->mobile_number)->where('dial_code', $country->dial_code)->first();

        if (!$merchant) {
            $notify[] = ['error', 'The account could not be found'];
            return back()->withNotify($notify);
        }

        MerchantPasswordReset::where('mobile', $merchant->mobile)->delete();

        $code             = verificationCode(6);
        $password         = new MerchantPasswordReset();
        $password->mobile = $merchant->mobile;
        $password->token  = $code;
        $password->save();

        $merchantIpInfo      = getIpInfo();
        $merchantBrowserInfo = osBrowser();

        notify($merchant, 'PASS_RESET_CODE', [
            'code'             => $code,
            'operating_system' => @$merchantBrowserInfo['os_platform'],
            'browser'          => @$merchantBrowserInfo['browser'],
            'ip'               => @$merchantIpInfo['ip'],
            'time'             => @$merchantIpInfo['time']
        ]);

        $mobile = $merchant->mobile;
        session()->put('pass_res_sms', $mobile);
        $notify[] = ['success', 'PIN reset code sent successfully'];
        return to_route('merchant.password.code.verify')->withNotify($notify);
    }



    public function codeVerify(Request $request)
    {
        $pageTitle = 'Verify Mobile Number';
        $mobile = $request->session()->get('pass_res_sms');
        if (!$mobile) {
            $notify[] = ['error', 'Oops! session expired'];
            return to_route('merchant.password.request')->withNotify($notify);
        }
        return view('Template::merchant.auth.passwords.code_verify', compact('pageTitle', 'mobile'));
    }

    public function verifyCode(Request $request)
    {
        $request->validate([
            'code'   => 'required',
            ...mobileNumberValidationRule(digitValidation: false)
        ]);

        $code =  str_replace(' ', '', $request->code);

        if (MerchantPasswordReset::where('token', $code)->where('mobile', $request->mobile_number)->count() != 1) {
            $notify[] = ['error', 'Verification code doesn\'t match'];
            return to_route('merchant.password.request')->withNotify($notify);
        }

        $notify[] = ['success', 'You can change your PIN'];
        session()->flash('fpass_mobile', $request->mobile_number);
        return to_route('merchant.password.reset', $code)->withNotify($notify);
    }
}
