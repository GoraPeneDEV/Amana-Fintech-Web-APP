<?php

namespace App\Http\Controllers\User\Auth;

use App\Constants\Status;
use App\Http\Controllers\Controller;
use App\Lib\Intended;
use App\Models\User;
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
        $qrCode = getQrCodeUrlForLogin();
        return view('Template::user.auth.login', compact('pageTitle', 'qrCode'));
    }

    public function login(Request $request)
    {

        $this->validateLogin($request);

        if (!verifyCaptcha()) {
            $notify[] = ['error', 'Invalid captcha provided'];
            return back()->withNotify($notify);
        }

        // If the class is using the ThrottlesLogins trait, we can automatically throttle
        // the login attempts for this application. We'll key this by the username and
        // the IP address of the client making these requests into this application.
        if ($this->hasTooManyLoginAttempts($request)) {
            $this->fireLockoutEvent($request);
            return $this->sendLockoutResponse($request);
        }

        if ($this->attemptLogin($request)) {
            return $this->sendLoginResponse($request);
        }

        // If the login attempt was unsuccessful we will increment the number of attempts
        // to login and redirect the user back to the login form. Of course, when this
        // user surpasses their maximum number of attempts they will get locked out.
        $this->incrementLoginAttempts($request);

        Intended::reAssignSession();

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
        $this->guard()->logout();
        request()->session()->invalidate();

        $notify[] = ['success', 'You have been logged out.'];
        return to_route('user.login')->withNotify($notify);
    }


    public function authenticated(Request $request, $user)
    {
        $user->tv = $user->ts == Status::VERIFIED ? Status::UNVERIFIED : Status::VERIFIED;
        $user->save();

        $ip        = getRealIP();
        $exist     = UserLogin::where('user_ip', $ip)->first();
        $userLogin = new UserLogin();
        if ($exist) {
            $userLogin->longitude    = $exist->longitude;
            $userLogin->latitude     = $exist->latitude;
            $userLogin->city         = $exist->city;
            $userLogin->country_code = $exist->country_code;
            $userLogin->country      = $exist->country;
        } else {
            $info                    = json_decode(json_encode(getIpInfo()), true);
            $userLogin->longitude    = @implode(',', $info['long']);
            $userLogin->latitude     = @implode(',', $info['lat']);
            $userLogin->city         = @implode(',', $info['city']);
            $userLogin->country_code = @implode(',', $info['code']);
            $userLogin->country      = @implode(',', $info['country']);
        }

        $userAgent          = osBrowser();
        $userLogin->user_id = $user->id;
        $userLogin->user_ip = $ip;

        $userLogin->browser = @$userAgent['browser'];
        $userLogin->os      = @$userAgent['os_platform'];
        $userLogin->save();

        $redirection = Intended::getRedirection();
        return $redirection ? $redirection : to_route('user.home');
    }

    protected function attemptLogin(Request $request)
    {
        $country = getUserSelectCountry();
        $user    = User::where("mobile", $request->mobile_number)->where('dial_code', $country->dial_code)->first();

        if ($user) {
            $credentials = [
                'mobile'    => $user->mobile,
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
        return qrCodeLoginAttempt('user', $id, $request->qrcode);
    }
}
