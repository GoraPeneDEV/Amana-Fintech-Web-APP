<?php

namespace App\Http\Controllers\Api\Auth;

use App\Constants\Status;
use App\Http\Controllers\Controller;
use App\Models\AdminNotification;
use App\Models\User;
use App\Models\UserLogin;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Laravel\Sanctum\PersonalAccessToken;

class LoginController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Login Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles authenticating users for the application and
    | redirecting them to your home screen. The controller uses a trait
    | to conveniently provide its functionality to your applications.
    |a
    */

    use AuthenticatesUsers;

    /**
     * Where to redirect users after login.
     *
     * @var string
     */


    /**
     * Create a new controller instance.
     *
     * @return void
     */

    public function authentication(Request $request)
    {
        $validator = Validator::make($request->all(), [
            ...mobileNumberValidationRule()
        ]);

        if ($validator->fails()) {
            return apiResponse("validation_error", "error", $validator->errors()->all());
        }

        $country = getUserSelectCountry();


        $user    = User::where("mobile", $request->mobile_number)->where('dial_code', $country->dial_code)->first();

        if ($user) {

            if (!$user->password && $request->pin) {
                $notify = ['Please setup the the PIN'];
                return apiResponse("pin_not_exists", "error", $notify);
            }

            if (($user->sv == Status::UNVERIFIED) || ($user->sv == Status::VERIFIED && !$user->password)) {
                if ($user->sv == Status::VERIFIED && !$user->password) {
                    $user->sv = Status::UNVERIFIED;
                    $user->save();
                }

                if ($user->status == Status::USER_DELETE) {
                    return apiResponse("account_delete", "error",);
                }

                sendOtp($user);
                $data['access_token'] = $user->createToken('user_token', ['user'])->plainTextToken;
                $data['user']         = $user;
                $data['token_type']   = 'Bearer';
                $notify[]             = 'Please verify your mobile.';
                return apiResponse("mobile_verification_required", "success", $notify, $data);
            }

            if ($user->status == Status::USER_DELETE) {
                $notify = ['This account has been deleted'];
                return apiResponse("account_deleted", "error", $notify);
            }
            if ($user->status == Status::USER_BAN) {
                $notify = ['This account has been banned'];
                return apiResponse("account_ban", "error", $notify);
            }
            if (!$user->password) {
                $notify = ['Please setup the the PIN'];
                return apiResponse("pin_not_exists", "error", $notify);
            }
            if (!$request->pin) {
                $notify = ['The pin filed is required'];
                return apiResponse("pin_required", "error", $notify);
            }

            if (Hash::check($request->pin, $user->password)) {
                $tokenResult = $user->createToken('user_token', ['user'])->plainTextToken;
                $this->authenticated($request, $user);
                return apiResponse("login_success", "success", ['Login Successful'], [
                    'user'         => $user,
                    'access_token' => $tokenResult,
                    'token_type'   => 'Bearer',
                ]);
            } else {
                $notify = ['Invalid PIN'];
                return apiResponse("invalid_credential", "error", $notify);
            }
        } else {

            if (!gs('registration')) {
                $notify[] = 'User Registration not allowed';
                return apiResponse("registration_disabled", "error", $notify);
            }

            if ($request->filled('pin')) {
                $notify = ['The account is not found. Please create new account'];
                return apiResponse("user_not_found", "error", $notify);
            }


            $user               = new User();
            $user->mobile       = $request->mobile_number;
            $user->dial_code    = $country->dial_code;
            $user->sv           = gs('sv') ? Status::NO : Status::YES;
            $user->country_code = $country->code;
            $user->country_name = $country->name;

            $user->kv = Status::UNVERIFIED;
            $user->ev = Status::UNVERIFIED;
            $user->ts = Status::DISABLE;
            $user->tv = Status::ENABLE;
            $user->save();


            // Create Admin Notification
            $adminNotification            = new AdminNotification();
            $adminNotification->user_id   = $user->id;
            $adminNotification->title     = 'New user registered';
            $adminNotification->click_url = urlPath('admin.users.detail', $user->id);
            $adminNotification->save();

            //Login Log Create
            $ip        = getRealIP();
            $exist     = UserLogin::where('user_ip', $ip)->first();
            $userLogin = new UserLogin();

            //Check exist or not
            if ($exist) {
                $userLogin->longitude =  $exist->longitude;
                $userLogin->latitude =  $exist->latitude;
                $userLogin->city =  $exist->city;
                $userLogin->country_code = $exist->country_code;
                $userLogin->country =  $exist->country;
            } else {
                $info = json_decode(json_encode(getIpInfo()), true);
                $userLogin->longitude =  @implode(',', $info['long']);
                $userLogin->latitude =  @implode(',', $info['lat']);
                $userLogin->city =  @implode(',', $info['city']);
                $userLogin->country_code = @implode(',', $info['code']);
                $userLogin->country =  @implode(',', $info['country']);
            }

            $userAgent = osBrowser();
            $userLogin->user_id = $user->id;
            $userLogin->user_ip =  $ip;

            $userLogin->browser = @$userAgent['browser'];
            $userLogin->os = @$userAgent['os_platform'];
            $userLogin->save();

            $data['access_token'] = $user->createToken('user_token', ['user'])->plainTextToken;
            $data['user']         = $user;
            $data['token_type']   = 'Bearer';
            $notify[]             = 'User registered. Please verify your mobile.';

            sendOtp($user);

            return apiResponse("mobile_verification_required", "success", $notify, $data);
        }
    }

    public function logout()
    {
        auth()->user()->tokens()->delete();

        $notify[] = 'Logout Successful';
        return apiResponse("logout", "success", $notify);
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
    }

    public function checkToken(Request $request)
    {
        $validationRule = [
            'token' => 'required',
        ];

        $validator = Validator::make($request->all(), $validationRule);

        if ($validator->fails()) {
            return apiResponse("validation_error", "error", $validator->errors()->all());
        }
        $accessToken = PersonalAccessToken::findToken($request->token);
        if ($accessToken) {
            $notify[]      = 'Token exists';
            $data['token'] = $request->token;
            return apiResponse("token_exists", "success", $notify, $data);
        }

        $notify[] = "Token doesn't exists";

        return apiResponse("token_not_exists", "error", $notify);
    }

    public function loginWithQrCode($encodedCode)
    {
        return verifyQrCodeForLogin($encodedCode, 'user');
    }
}
