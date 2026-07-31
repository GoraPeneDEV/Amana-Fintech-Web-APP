<?php

namespace App\Http\Controllers\Agent\Auth;

use App\Http\Controllers\Controller;
use App\Models\Agent;
use App\Models\AgentPasswordReset;
use Illuminate\Http\Request;

class ForgotPasswordController extends Controller
{
    public function showLinkRequestForm()
    {
        $pageTitle = "Account Recovery";
        return view('Template::agent.auth.passwords.email', compact('pageTitle'));
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
        $agent    = Agent::where("mobile", $request->mobile_number)->where('dial_code', $country->dial_code)->first();

        if (!$agent) {
            $notify[] = ['error', 'The account could not be found'];
            return back()->withNotify($notify);
        }

        AgentPasswordReset::where('mobile', $agent->mobile)->delete();

        $code             = verificationCode(6);
        $password         = new AgentPasswordReset();
        $password->mobile = $agent->mobile;
        $password->token  = $code;
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

        $mobile = $agent->mobile;
        session()->put('pass_res_sms', $mobile);
        $notify[] = ['success', 'PIN reset code sent successfully'];
        return to_route('agent.password.code.verify')->withNotify($notify);
    }



    public function codeVerify(Request $request)
    {
        $pageTitle = 'Verify Mobile Number';
        $mobile = $request->session()->get('pass_res_sms');
        if (!$mobile) {
            $notify[] = ['error', 'Oops! session expired'];
            return to_route('agent.password.request')->withNotify($notify);
        }
        return view('Template::agent.auth.passwords.code_verify', compact('pageTitle', 'mobile'));
    }

    public function verifyCode(Request $request)
    {

        $request->validate([
            'code' => 'required',
            mobileNumberValidationRule(digitValidation:false)
        ]);


        $code =  str_replace(' ', '', $request->code);

        if (AgentPasswordReset::where('token', $code)->where('mobile', $request->mobile_number)->count() != 1) {
            $notify[] = ['error', 'Verification code doesn\'t match'];
            return to_route('agent.password.request')->withNotify($notify);
        }
        $notify[] = ['success', 'You can change your PIN'];
        session()->flash('fpass_mobile', $request->mobile_number);
        return to_route('agent.password.reset', $code)->withNotify($notify);
    }
}
