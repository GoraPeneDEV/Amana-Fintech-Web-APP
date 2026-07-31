<?php

namespace App\Http\Controllers\Agent\Auth;

use App\Constants\Status;
use App\Http\Controllers\Controller;
use App\Lib\Intended;
use App\Models\Agent;
use App\Models\UserLogin;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class LoginController extends Controller
{
    use AuthenticatesUsers;

    public function showLoginForm()
    {
        Intended::identifyRoute();
        $pageTitle = "Login";
        $qrCode = getQrCodeUrlForLogin('agent');
        return view('Template::agent.auth.login', compact('pageTitle', 'qrCode'));
    }

    
    protected function guard()
    {
        return auth()->guard('agent');
    }

    public function login(Request $request)
    {

        $this->validateLogin($request);

        $request->session()->regenerateToken();

        if (!verifyCaptcha()) {
            $notify[] = ['error', 'Invalid captcha provided'];
            return back()->withNotify($notify);
        }
        if (
            method_exists($this, 'hasTooManyLoginAttempts') &&
            $this->hasTooManyLoginAttempts($request)
        ) {
            $this->fireLockoutEvent($request);
            return $this->sendLockoutResponse($request);
        }

        if ($this->attemptLogin($request)) {
            return $this->sendLoginResponse($request);
        }

        $this->incrementLoginAttempts($request);

        return $this->sendFailedLoginResponse($request);
    }

    public function findUsername()
    {
        $login = request()->input('username');

        $fieldType = filter_var($login, FILTER_VALIDATE_EMAIL) ? 'email' : 'username';
        request()->merge([$fieldType => $login]);
        return $fieldType;
    }

    protected function validateLogin($request)
    {
        $validator  = Validator::make($request->all(), [
            ...pinValidationRule(),
            ...mobileNumberValidationRule()
        ]);
        if ($validator->fails()) {
            Intended::reAssignSession();
            $validator->validate();
        }
    }

    public function logout()
    {
        $this->guard('agent')->logout();
        request()->session()->invalidate();

        $notify[] = ['success', 'You have been logged out.'];
        return to_route('agent.login')->withNotify($notify);
    }

    public function authenticated(Request $request, $agent)
    {
        $agent->tv = $agent->ts == Status::VERIFIED ? Status::UNVERIFIED : Status::VERIFIED;
        $agent->save();

        $ip        = getRealIP();
        $exist     = UserLogin::where('user_ip', $ip)->first();
        $agentLogin = new UserLogin();
        if ($exist) {
            $agentLogin->longitude    = $exist->longitude;
            $agentLogin->latitude     = $exist->latitude;
            $agentLogin->city         = $exist->city;
            $agentLogin->country_code = $exist->country_code;
            $agentLogin->country      = $exist->country;
        } else {
            $info                    = json_decode(json_encode(getIpInfo()), true);
            $agentLogin->longitude    = @implode(',', $info['long']);
            $agentLogin->latitude     = @implode(',', $info['lat']);
            $agentLogin->city         = @implode(',', $info['city']);
            $agentLogin->country_code = @implode(',', $info['code']);
            $agentLogin->country      = @implode(',', $info['country']);
        }

        $agentAgent              = osBrowser();
        $agentLogin->agent_id    = $agent->id;
        $agentLogin->user_ip     = $ip;

        $agentLogin->browser = @$agentAgent['browser'];
        $agentLogin->os      = @$agentAgent['os_platform'];
        $agentLogin->save();


        $redirection = Intended::getRedirection();
        return $redirection ? $redirection : to_route('agent.home');
    }

    protected function attemptLogin(Request $request)
    {
        $country = getUserSelectCountry();
        $agent   = Agent::where("mobile", $request->mobile_number)->where('dial_code', $country->dial_code)->first();

        if ($agent) {
            $credentials = [
                'mobile'   => $agent->mobile,
                'password' => $request->pin,
                'dial_code' => $country->dial_code
            ];
            return $this->guard()->attempt(
                $credentials,
                $request->boolean('remember')
            );
        }
    }

    public function qrCodeLogin(Request $request, $id)
    {
        $request->validate([
            'qrcode' => 'required'
        ]);
        return qrCodeLoginAttempt('agent',$id,$request->qrcode);
    }
}
