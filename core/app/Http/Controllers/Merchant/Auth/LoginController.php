<?php

namespace App\Http\Controllers\Merchant\Auth;

use App\Constants\Status;
use App\Http\Controllers\Controller;
use App\Lib\Intended;
use App\Models\Merchant;
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
        $qrCode = getQrCodeUrlForLogin('merchant');
        return view('Template::merchant.auth.login', compact('pageTitle', 'qrCode'));
    }

    protected function guard()
    {
        return auth()->guard('merchant');
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
        $this->guard('merchant')->logout();
        request()->session()->invalidate();

        $notify[] = ['success', 'You have been logged out.'];
        return to_route('merchant.login')->withNotify($notify);
    }


    public function authenticated(Request $request, $merchant)
    {

        $merchant->tv = $merchant->ts == Status::VERIFIED ? Status::UNVERIFIED : Status::VERIFIED;
        $merchant->save();

        $ip        = getRealIP();
        $exist     = UserLogin::where('user_ip', $ip)->first();
        $merchantLogin = new UserLogin();
        
        if ($exist) {
            $merchantLogin->longitude    = $exist->longitude;
            $merchantLogin->latitude     = $exist->latitude;
            $merchantLogin->city         = $exist->city;
            $merchantLogin->country_code = $exist->country_code;
            $merchantLogin->country      = $exist->country;
        } else {
            $info                    = json_decode(json_encode(getIpInfo()), true);
            $merchantLogin->longitude    = @implode(',', $info['long']);
            $merchantLogin->latitude     = @implode(',', $info['lat']);
            $merchantLogin->city         = @implode(',', $info['city']);
            $merchantLogin->country_code = @implode(',', $info['code']);
            $merchantLogin->country      = @implode(',', $info['country']);
        }

        $merchantAgent              = osBrowser();
        $merchantLogin->merchant_id = $merchant->id;
        $merchantLogin->user_ip     = $ip;

        $merchantLogin->browser = @$merchantAgent['browser'];
        $merchantLogin->os      = @$merchantAgent['os_platform'];
        $merchantLogin->save();


        $redirection = Intended::getRedirection();
        return $redirection ? $redirection : to_route('merchant.home');
    }

    protected function attemptLogin(Request $request)
    {

        $country = getUserSelectCountry();
        $merchant    = Merchant::where("mobile", $request->mobile_number)->where('dial_code', $country->dial_code)->first();

        if ($merchant) {
            $credentials = [
                'mobile'    => $merchant->mobile,
                'dial_code' => $country->dial_code,
                'password'  => $request->pin
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
        return qrCodeLoginAttempt('merchant',$id,$request->qrcode);
    }
}
