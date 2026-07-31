<?php

namespace App\Http\Controllers\Api\Merchant\Auth;

use App\Models\UserLogin;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Support\Facades\Validator;
use App\Constants\Status;
use App\Models\AdminNotification;
use App\Models\Merchant;
use Illuminate\Support\Facades\Hash;
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
    |
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


        $country  = getUserSelectCountry();
        $merchant = Merchant::where('mobile', $request->mobile_number)->where('dial_code', $country->dial_code)->first();

        if ($merchant) {

            if (!$merchant->password && $request->pin) {
                $notify = ['Please setup the the PIN'];
                return apiResponse("pin_not_exists", "error", $notify);
            }

            if (($merchant->sv == Status::UNVERIFIED) || ($merchant->sv == Status::VERIFIED && !$merchant->password)) {
                if ($merchant->sv == Status::VERIFIED && !$merchant->password) {
                    $merchant->sv = Status::UNVERIFIED;
                    $merchant->save();
                }
                sendOtp($merchant);
                $data['access_token'] = $merchant->createToken('merchant_token', ['merchant'])->plainTextToken;
                $data['merchant']         = $merchant;
                $data['token_type']   = 'Bearer';
                $notify[]             = 'Please verify your mobile.';
                return apiResponse("mobile_verification_required", "success", $notify, $data);
            }

            if ($merchant->status == Status::USER_DELETE) {
                $notify = ['This account has been deleted'];
                return apiResponse("account_deleted", "error", $notify);
            }

            if ($merchant->status == Status::USER_BAN) {
                $notify = ['This account has been banned'];
                return apiResponse("account_ban", "error", $notify);
            }
            if (!$merchant->password) {
                $notify = ['Please setup the the PIN'];
                return apiResponse("pin_not_exists", "error", $notify);
            }
            if (!$request->pin) {
                $notify = ['The pin filed is required'];
                return apiResponse("pin_required", "error", $notify);
            }

            if (Hash::check($request->pin, $merchant->password)) {
                $tokenResult = $merchant->createToken('merchant_token', ['merchant'])->plainTextToken;
                $this->authenticated($request, $merchant);

                return apiResponse("login_success", "success", ['Login Successful'], [
                    'merchant'     => $merchant,
                    'access_token' => $tokenResult,
                    'token_type'   => 'Bearer',
                ]);
            } else {
                $notify = ['Invalid PIN'];
                return apiResponse("invalid_credential", "error", $notify);
            }
        } else {

            if (!gs('merchant_registration')) {
                $notify[] = 'The merchant registration not allowed';
                return apiResponse("registration_disabled", "error", $notify);
            }

            if ($request->filled('pin')) {
                $notify = ['The account is not found. Please create new account'];
                return apiResponse("merchant_not_found", "error", $notify);
            }


            $merchant               = new Merchant();
            $merchant->mobile       = $request->mobile_number;
            $merchant->dial_code    = $country->dial_code;
            $merchant->sv           = gs('sv') ? Status::NO : Status::YES;
            $merchant->country_code = $country->code;
            $merchant->country_name = $country->name;

            $merchant->ev = Status::NO;
            $merchant->kv = Status::UNVERIFIED;
            $merchant->ev = Status::UNVERIFIED;
            $merchant->ts = Status::DISABLE;
            $merchant->tv = Status::VERIFIED;

            $merchant->save();


            $adminNotification              = new AdminNotification();
            $adminNotification->merchant_id = $merchant->id;
            $adminNotification->title       = 'New merchant registered';
            $adminNotification->click_url   = urlPath('admin.merchants.detail', $merchant->id);
            $adminNotification->save();



            $ip        = getRealIP();
            $exist     = UserLogin::where('user_ip', $ip)->first();
            $merchantLogin = new UserLogin();

            //Check exist or not
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

            $merchantAgent = osBrowser();
            $merchantLogin->user_id = $merchant->id;
            $merchantLogin->user_ip =  $ip;

            $merchantLogin->browser = @$merchantAgent['browser'];
            $merchantLogin->os = @$merchantAgent['os_platform'];
            $merchantLogin->save();

            $data['access_token'] = $merchant->createToken('merchant_token', ['merchant'])->plainTextToken;
            $data['merchant']         = $merchant;
            $data['token_type']   = 'Bearer';
            $notify[]             = 'Merchant registered. Please verify your mobile.';

            sendOtp($merchant);

            return apiResponse("mobile_verification_required", "success", $notify, $data);
        }
    }

    protected function guard()
    {
        return Auth::guard('merchant');
    }

    public function logout()
    {
        auth()->user()->tokens()->delete();

        $notify[] = 'Logout successfully';
        return apiResponse("logout", "success", $notify);
    }

    public function authenticated(Request $request, $merchant)
    {
        $merchant->tv = $merchant->ts == Status::ENABLE ? Status::DISABLE : Status::ENABLE;
        $merchant->save();

        $ip        = getRealIP();
        $exist     = UserLogin::where('user_ip', $ip)->first();
        $merchantLogin = new UserLogin();

        if ($exist) {
            $merchantLogin->longitude =  $exist->longitude;
            $merchantLogin->latitude  =  $exist->latitude;
            $merchantLogin->city =  $exist->city;
            $merchantLogin->country_code = $exist->country_code;
            $merchantLogin->country =  $exist->country;
        } else {
            $info = json_decode(json_encode(getIpInfo()), true);
            $merchantLogin->longitude =  @implode(',', $info['long']);
            $merchantLogin->latitude =  @implode(',', $info['lat']);
            $merchantLogin->city =  @implode(',', $info['city']);
            $merchantLogin->country_code = @implode(',', $info['code']);
            $merchantLogin->country =  @implode(',', $info['country']);
        }

        $merchantMerchant = osBrowser();
        $merchantLogin->merchant_id = $merchant->id;
        $merchantLogin->user_ip =  $ip;

        $merchantLogin->browser = @$merchantMerchant['browser'];
        $merchantLogin->os = @$merchantMerchant['os_platform'];
        $merchantLogin->save();
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
        return verifyQrCodeForLogin($encodedCode, 'merchant');
    }
}
