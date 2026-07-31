<?php

namespace App\Http\Controllers\Merchant\Auth;

use App\Constants\Status;
use App\Http\Controllers\Controller;
use App\Lib\Intended;
use App\Models\AdminNotification;
use App\Models\Merchant;
use App\Models\UserLogin;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class RegisterController extends Controller
{

    protected function guard()
    {
        return auth()->guard('merchant');
    }

    public function showRegistrationForm()
    {
        $pageTitle = "Register";
        Intended::identifyRoute();
        return view('Template::merchant.auth.register', compact('pageTitle'));
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
        $exists  = Merchant::where('dial_code', $country->dial_code)->where('mobile', $request->mobile_number)->exists();

        if ($exists) {
            $notify[] = ['error', 'The mobile number is already exists'];
            return back()->withNotify($notify);
        }

        event(new Registered($merchant = $this->create($request->all())));

        $this->guard()->login($merchant);


        return $this->registered($request, $merchant)
            ?: redirect($this->redirectPath());
    }

    protected function create(array $data)
    {
        $country = getUserSelectCountry();

        $merchant               = new Merchant();
        $merchant->mobile       = $data['mobile_number'];
        $merchant->dial_code    = $country->dial_code;
        $merchant->sv           = gs('sv') ? Status::NO : Status::YES;
        $merchant->kv           = Status::NO;
        $merchant->ev           = Status::NO;
        $merchant->country_code = $country->code;
        $merchant->country_name = $country->name;
        $merchant->save();

        $adminNotification              = new AdminNotification();
        $adminNotification->merchant_id = $merchant->id;
        $adminNotification->title       = 'New merchant registered';
        $adminNotification->click_url   = urlPath('admin.merchants.detail', $merchant->id);
        $adminNotification->save();

        //Login Log Create
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

        $merchantAgent          = osBrowser();
        $merchantLogin->merchant_id = $merchant->id;
        $merchantLogin->user_ip = $ip;

        $merchantLogin->browser = @$merchantAgent['browser'];
        $merchantLogin->os      = @$merchantAgent['os_platform'];
        $merchantLogin->save();

        return $merchant;
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
        $merchant = Merchant::where('username', $request->username)->exists();

        if ($merchant) {
            $message[] = "The username already exists";
            return apiResponse('exists', 'error', $message, [
                'field'         => "username",
                'error_message' => "The username already exists"
            ]);
        }

        $merchant = Merchant::where('email', $request->email)->exists();
        if ($merchant) {
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
        return to_route('merchant.home');
    }
}
