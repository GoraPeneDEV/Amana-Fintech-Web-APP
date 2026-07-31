<?php

namespace App\Http\Controllers\User\Auth;

use App\Http\Controllers\Controller;
use App\Models\PasswordReset;
use App\Models\User;
use Illuminate\Http\Request;

class ForgotPasswordController extends Controller
{
    public function showLinkRequestForm()
    {
        $pageTitle = "Account Recovery";
        return view('Template::user.auth.passwords.email', compact('pageTitle'));
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

        $country = getUserSelectCountry();
        $user    = User::where("mobile", $request->mobile_number)->where('dial_code', $country->dial_code)->first();

        if (!$user) {
            $notify[] = ['error', 'The account could not be found'];
            return back()->withNotify($notify);
        }

        PasswordReset::where('mobile', $user->mobile)->delete();

        $code                 = verificationCode(6);
        $password             = new PasswordReset();
        $password->mobile     = $user->mobile;
        $password->token      = $code;
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

        $mobile = $user->mobile;
        session()->put('pass_res_sms', $mobile);
        $notify[] = ['success', 'PIN reset sms sent successfully'];
        return to_route('user.password.code.verify')->withNotify($notify);
    }


    public function codeVerify(Request $request)
    {
        $pageTitle = 'Verify Mobile Number';
        $mobile = $request->session()->get('pass_res_sms');
        if (!$mobile) {
            $notify[] = ['error', 'Oops! session expired'];
            return to_route('user.password.request')->withNotify($notify);
        }
        return view('Template::user.auth.passwords.code_verify', compact('pageTitle', 'mobile'));
    }

    public function verifyCode(Request $request)
    {

        $request->validate([
            'code' => 'required',
            ...mobileNumberValidationRule(digitValidation:false)
        ]);

        $code =  str_replace(' ', '', $request->code);

        if (PasswordReset::where('token', $code)->where('mobile', $request->mobile_number)->count() != 1) {
            $notify[] = ['error', 'Verification code doesn\'t match'];
            return to_route('user.password.request')->withNotify($notify);
        }

        $notify[] = ['success', 'You can change your PIN'];
        session()->flash('fpass_mobile', $request->mobile_number);
        return to_route('user.password.reset', $code)->withNotify($notify);
    }
}
