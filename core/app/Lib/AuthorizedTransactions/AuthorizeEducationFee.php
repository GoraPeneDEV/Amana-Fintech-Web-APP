<?php

namespace App\Lib\AuthorizedTransactions;

use App\Constants\Status;
use App\Models\AdminNotification;
use App\Models\EducationFee;
use App\Models\Institution;
use App\Models\Transaction;

class AuthorizeEducationFee
{
    public function store($userAction)
    {
        $user = auth()->user();

        $details = $userAction->details;
        if (@$details->total_amount > $user->balance) {
            $notify[] = 'Sorry! Insufficient balance';
            return apiResponse("validation_error", "error", $notify);
        }

        $institute = Institution::active()->where('id', $details->institution_id)->first();
        if (!$institute) {
            $notify[] = 'Sorry! Institute not found';
            return apiResponse("validation_error", "error", $notify);
        }

        $userAction->is_used = Status::YES;
        $userAction->save();

        $user->balance -= $details->total_amount;
        $user->save();

        $educationFee                  = new EducationFee();
        $educationFee->user_id         = $user->id;
        $educationFee->institution_id  = $institute->id;
        $educationFee->amount          = $details->amount;
        $educationFee->charge          = $details->total_charge;
        $educationFee->total           = $details->total_amount;
        $educationFee->trx             = generateUniqueTrxNumber();
        $educationFee->status          = 0;
        $educationFee->user_data       = $details->user_data;
        $educationFee->save();

        $transaction                = new Transaction();
        $transaction->user_id       = $user->id;
        $transaction->amount        = $details->amount;
        $transaction->post_balance  = $user->balance;
        $transaction->charge        = $details->total_charge;
        $transaction->trx_type      = '-';
        $transaction->remark        = 'education_fee';
        $transaction->details       = 'Education fee';
        $transaction->trx           = $educationFee->trx;
        $transaction->save();

        $adminNotification            = new AdminNotification();
        $adminNotification->user_id   = $user->id;
        $adminNotification->title     = 'New education fee request from ' . $user->username;
        $adminNotification->click_url = urlPath('admin.education.fee.all');
        $adminNotification->save();

        $notify[] = 'Successfully complete the education fee process';
        return apiResponse("education_fee_done", "success", $notify, [
            'redirect_type' => "new_url",
            'redirect_url'  => route('user.education.fee.details', $educationFee->id),
            'education_fee' => $educationFee
        ]);
    }
}
