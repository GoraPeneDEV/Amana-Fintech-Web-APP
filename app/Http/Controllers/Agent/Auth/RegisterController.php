<?php

namespace App\Http\Controllers\Agent\Auth;

use App\Constants\Status;
use App\Http\Controllers\Controller;
use App\Lib\Intended;
use App\Models\AdminNotification;
use App\Models\Agent;
use App\Models\UserLogin;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class RegisterController extends Controller
{



    protected function guard()
    {
        return auth()->guard('agent');
    }

    public function showRegistrationForm()
    {
        $pageTitle = "Register";
        Intended::identifyRoute();
        return view('Template::agent.auth.register', compact('pageTitle'));
    }


    protected function validator(array $data)
    {
        $agree = 'nullable';
        if (gs('agree')) {
            $agree = 'required';
        }

        $validator  = Validator::make($data, [

            'agree'         => $agree,
            'captcha'       => 'sometimes|required',
            ...mobileNumberValidationRule()
        ]);

        return $validator;
    }

    public function register(Request $request)
    {
        if (!gs('registration')) {
            $notify[] = ['error', 'Registration not allowed'];
            return back()->withNotify($notify);
        }
        $this->validator($request->all())->validate();

        $request->session()->regenerateToken();

        if (!verifyCaptcha()) {
            $notify[] = ['error', 'Invalid captcha provided'];
            return back()->withNotify($notify);
        }

        $country = getUserSelectCountry();
        $exists = Agent::where('dial_code', $country->dial_code)->where('mobile', $request->mobile_number)->exists();
        if ($exists) {
            $notify[] = ['error', 'The mobile number is already exists'];
            return back()->withNotify($notify);
        }

        event(new Registered($user = $this->create($request->all())));

        $this->guard()->login($user);


        return $this->registered($request, $user)
            ?: redirect($this->redirectPath());
    }

    protected function create(array $data)
    {
        $country = getUserSelectCountry();

        $user               = new Agent();
        $user->mobile       = $data['mobile_number'];
        $user->dial_code    = $country->dial_code;
        $user->sv           = gs('sv') ? Status::NO : Status::YES;
        $user->kv           = Status::NO;
        $user->ev           = Status::NO;
        $user->country_code = $country->code;
        $user->country_name = $country->name;
        $user->save();

        $adminNotification              = new AdminNotification();
        $adminNotification->agent_id = $user->id;
        $adminNotification->title       = 'New agent registered';
        $adminNotification->click_url   = urlPath('admin.agents.detail', $user->id);
        $adminNotification->save();

        //Login Log Create
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
        $userLogin->agent_id = $user->id;
        $userLogin->user_ip = $ip;

        $userLogin->browser = @$userAgent['browser'];
        $userLogin->os      = @$userAgent['os_platform'];
        $userLogin->save();

        return $user;
    }

    public function checkUser(Request $request)
    {

        $validator = Validator::make($request->all(), [
            'username' => 'required',
            'email' => 'required',
        ]);

        if ($validator->failed()) {
            return apiResponse('validation_error', 'error', $validator->errors()->all());
        }
        $user = Agent::where('username', $request->username)->exists();

        if ($user) {
            $message[] = "The username already exists";
            return apiResponse('exists', 'error', $message, [
                'field'         => "username",
                'error_message' => "The username already exists"
            ]);
        }

        $user = Agent::where('email', $request->email)->exists();
        if ($user) {
            $message[] = "The email already exists";
            return apiResponse('exists', 'error', $message, [
                'field'         => "email",
                'error_message' => "The email already exists"
            ]);
        }
        $message[] = "The user does not exist";
        return apiResponse('success', 'success', $message);
    }

    public function registered()
    {
        return to_route('agent.home');
    }
}
