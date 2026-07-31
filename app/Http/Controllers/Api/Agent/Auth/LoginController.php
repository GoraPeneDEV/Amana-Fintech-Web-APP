<?php

namespace App\Http\Controllers\Api\Agent\Auth;

use App\Models\UserLogin;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Support\Facades\Validator;
use App\Constants\Status;
use App\Models\AdminNotification;
use App\Models\Agent;
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

        $country = getUserSelectCountry();
        $agent   = Agent::where('mobile', $request->mobile_number)->where('dial_code', $country->dial_code)->first();

        if ($agent) {

            if (!$agent->password && $request->pin) {
                $notify = ['Please setup the the PIN'];
                return apiResponse("pin_not_exists", "error", $notify);
            }

            if (($agent->sv == Status::UNVERIFIED) || ($agent->sv == Status::VERIFIED && !$agent->password)) {
                if ($agent->sv == Status::VERIFIED && !$agent->password) {
                    $agent->sv = Status::UNVERIFIED;
                    $agent->save();
                }
                sendOtp($agent);
                $data['access_token'] = $agent->createToken('agent_token', ['agent'])->plainTextToken;
                $data['agent']         = $agent;
                $data['token_type']   = 'Bearer';
                $notify[]             = 'Please verify your mobile.';
                return apiResponse("mobile_verification_required", "success", $notify, $data);
            }

            if ($agent->status == Status::USER_DELETE) {
                $notify = ['This account has been deleted'];
                return apiResponse("account_deleted", "error", $notify);
            }
            if ($agent->status == Status::USER_BAN) {
                $notify = ['This account has been banned'];
                return apiResponse("account_ban", "error", $notify);
            }
            if (!$agent->password) {
                $notify = ['Please setup the the PIN'];
                return apiResponse("pin_not_exists", "error", $notify);
            }
            if (!$request->pin) {
                $notify = ['The pin filed is required'];
                return apiResponse("pin_required", "error", $notify);
            }

            if (Hash::check($request->pin, $agent->password)) {
                $tokenResult = $agent->createToken('agent_token', ['agent'])->plainTextToken;
                $this->authenticated($request, $agent);
                return apiResponse("login_success", "success", ['Login Successful'], [
                    'agent'         => $agent,
                    'access_token' => $tokenResult,
                    'token_type'   => 'Bearer',
                ]);
            } else {
                $notify = ['Invalid PIN'];
                return apiResponse("invalid_credential", "error", $notify);
            }
        } else {

            if (!gs('agent_registration')) {
                $notify[] = 'The agent registration not allowed';
                return apiResponse("registration_disabled", "error", $notify);
            }

            if ($request->filled('pin')) {
                $notify = ['The account is not found. Please create new account'];
                return apiResponse("agent_not_found", "error", $notify);
            }


            $agent               = new Agent();
            $agent->mobile       = $request->mobile_number;
            $agent->dial_code    = $country->dial_code;
            $agent->sv           = gs('sv') ? Status::NO : Status::YES;
            $agent->kv           = Status::NO;
            $agent->ev           = Status::NO;
            $agent->country_code = $country->code;
            $agent->country_name = $country->name;

            $agent->kv = Status::UNVERIFIED;
            $agent->ev = Status::UNVERIFIED;
            $agent->ts = Status::DISABLE;
            $agent->tv = Status::VERIFIED;

            $agent->save();


            $adminNotification = new AdminNotification();
            $adminNotification->agent_id = $agent->id;
            $adminNotification->title   = 'New agent registered';
            $adminNotification->click_url = urlPath('admin.agents.detail', $agent->id);
            $adminNotification->save();;

            //Login Log Create
            $ip        = getRealIP();
            $exist     = UserLogin::where('user_ip', $ip)->first();
            $agentLogin = new UserLogin();

            //Check exist or not
            if ($exist) {
                $agentLogin->longitude    = $exist->longitude;
                $agentLogin->latitude     = $exist->latitude;
                $agentLogin->city         = $exist->city;
                $agentLogin->country_code = $exist->country_code;
                $agentLogin->country      = $exist->country;
            } else {
                $info                     = json_decode(json_encode(getIpInfo()), true);
                $agentLogin->longitude    = @implode(',', $info['long']);
                $agentLogin->latitude     = @implode(',', $info['lat']);
                $agentLogin->city         = @implode(',', $info['city']);
                $agentLogin->country_code = @implode(',', $info['code']);
                $agentLogin->country      = @implode(',', $info['country']);
            }

            $agentAgent          = osBrowser();
            $agentLogin->user_id = $agent->id;
            $agentLogin->user_ip = $ip;

            $agentLogin->browser = @$agentAgent['browser'];
            $agentLogin->os      = @$agentAgent['os_platform'];
            $agentLogin->save();

            $data['access_token'] = $agent->createToken('agent_token', ['agent'])->plainTextToken;
            $data['agent']         = $agent;
            $data['token_type']   = 'Bearer';
            $notify[]             = 'Agent registered. Please verify your mobile.';

            sendOtp($agent);

            return apiResponse("mobile_verification_required", "success", $notify, $data);
        }
    }

    protected function guard()
    {
        return Auth::guard('agent');
    }

    public function logout()
    {
        auth()->user()->tokens()->delete();

        $notify[] = 'Logout successfully';
        return apiResponse("logout", "success", $notify);
    }


    public function authenticated(Request $request, $agent)
    {
        $agent->tv = $agent->ts == Status::ENABLE ? Status::DISABLE : Status::ENABLE;
        $agent->save();

        $ip        = getRealIP();
        $exist     = UserLogin::where('user_ip', $ip)->first();
        $agentLogin = new UserLogin();

        if ($exist) {
            $agentLogin->longitude =  $exist->longitude;
            $agentLogin->latitude =  $exist->latitude;
            $agentLogin->city =  $exist->city;
            $agentLogin->country_code = $exist->country_code;
            $agentLogin->country =  $exist->country;
        } else {
            $info = json_decode(json_encode(getIpInfo()), true);
            $agentLogin->longitude =  @implode(',', $info['long']);
            $agentLogin->latitude =  @implode(',', $info['lat']);
            $agentLogin->city =  @implode(',', $info['city']);
            $agentLogin->country_code = @implode(',', $info['code']);
            $agentLogin->country =  @implode(',', $info['country']);
        }

        $agentAgent = osBrowser();
        $agentLogin->agent_id = $agent->id;
        $agentLogin->user_ip =  $ip;

        $agentLogin->browser = @$agentAgent['browser'];
        $agentLogin->os = @$agentAgent['os_platform'];
        $agentLogin->save();
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
        return verifyQrCodeForLogin($encodedCode, 'agent');
    }

}
