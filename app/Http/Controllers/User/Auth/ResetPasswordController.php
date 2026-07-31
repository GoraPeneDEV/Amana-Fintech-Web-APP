<?php

namespace App\Http\Controllers\User\Auth;

use App\Http\Controllers\Controller;
use App\Models\PasswordReset;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class ResetPasswordController extends Controller
{
    public function showResetForm($token = null)
    {

        $mobile        = session('fpass_mobile');
        $token         = session()->has('token') ? session('token') : $token;

        $resetPassword = PasswordReset::where('token', $token)?->where('mobile', $mobile)->orderBy('id', 'desc')->first();

        if (!$resetPassword || @$resetPassword->token != $token || !$token) {
            $notify[] = ['error', 'Invalid token'];
            return to_route('user.password.request')->withNotify($notify);
        }

        return view('Template::user.auth.passwords.reset')->with(
            ['token' => $token, 'mobile' => $mobile, 'pageTitle' => 'Reset Password']
        );
    }

    public function reset(Request $request)
    {
        $request->validate([
            ...pinValidationRule()
        ]);

        $reset = PasswordReset::where('token', $request->token)->orderBy('created_at', 'desc')->first();

        if (!$reset) {
            $notify[] = ['error', 'Invalid verification code'];
            return to_route('user.login')->withNotify($notify);
        }

        $user           = User::where('mobile', $reset->mobile)->first();
        $user->password = Hash::make($request->pin);
        $user->save();


        $userIpInfo = getIpInfo();
        $userBrowser = osBrowser();


        notify($user, 'PASS_RESET_DONE', [
            'operating_system' => @$userBrowser['os_platform'],
            'browser'          => @$userBrowser['browser'],
            'ip'               => @$userIpInfo['ip'],
            'time'             => @$userIpInfo['time']
        ]);

        $notify[] = ['success', 'PIN changed successfully'];
        return to_route('user.login')->withNotify($notify);
    }
}
