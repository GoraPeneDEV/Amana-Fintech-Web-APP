<?php

namespace App\Http\Controllers\Api;

use App\Constants\Status;
use App\Http\Controllers\Controller;
use App\Lib\AuthorizedTransactions\AuthorizedTransactionManager;
use App\Models\UserAction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class VerificationProcessController extends Controller
{

    public function verifyOtp(Request $request)
    {
        if (gs('otp_verification') == Status::NO) {
            return apiResponse('error', 'error', ['OTP verification is disabled']);
        }

        $validator = Validator::make($request->all(), [
            'otp'    => 'required|integer',
            'remark' => 'required|in:' . implode(",", getOtpRemark())
        ]);

        if ($validator->fails()) {
            return apiResponse('validation_error', 'error', $validator->errors()->all());
        }
        $userAction = UserAction::where('otp', $request->otp)
            ->where('user_id', auth()->id())
            ->where('is_used', Status::NO)
            ->orderBy('id', 'desc')
            ->where('remark', $request->remark)
            ->where('platform', Status::PLATFORM_APP)
            ->first();

        if (!$userAction) {
            return apiResponse("error", "error", ["The verification code dose not match"]);
        }
        
        if ($userAction->expired_at < now()) {
            return apiResponse("error", "error", ["The verification code is expired."]);
        }

        return apiResponse('success', 'success', ['Verification successful']);
    }

    public function verifyPin(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'remark' => 'required|in:' . implode(",", getOtpRemark()),
            ...pinValidationRule(),
        ]);

        if ($validator->fails()) {
            return apiResponse('validation_error', 'error', $validator->errors()->all());
        }
        $user = auth()->user();
        if (!Hash::check($request->pin, $user->password)) {
            return apiResponse("error", "error", ["'The PIN doesn\'t match!'"]);
        }
        return (new AuthorizedTransactionManager())->process($request->remark);
    }

    public function resendCode()
    {
        $user = auth()->user();

        $userAction = UserAction::where('user_id', $user->id)
            ->where('is_used', Status::NO)
            ->where('platform', Status::PLATFORM_APP)
            ->orderBy('id', 'desc')
            ->first();

        if (!$userAction) {
            return apiResponse("error", "error", ["The verification code does not exist."]);
        }

        $expiredAt = now()->parse($userAction->expired_at);

        if ($expiredAt->isFuture()) {
            $dueSeconds = (int)now()->diffInSeconds($expiredAt);
            return apiResponse("error", "error", ["The verification code is not expired yet. Please wait {$dueSeconds} seconds"]);
        }

        $userAction->otp        = verificationCode(6);
        $userAction->expired_at = now()->addSeconds((float)gs('otp_expiration'));
        $userAction->save();

        notify(
            $user,
            'SEND_OTP',
            ['code' => $userAction->otp],
            [$userAction->sent_via]
        );

        return apiResponse('success', 'success', ['Verification code has been sent successfully.']);
    }
}
