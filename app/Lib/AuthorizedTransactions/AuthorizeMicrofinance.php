<?php

namespace App\Lib\AuthorizedTransactions;

use App\Constants\Status;
use App\Models\AdminNotification;
use App\Models\Microfinance;
use App\Models\Ngo;
use App\Models\Transaction;

class AuthorizeMicrofinance
{
    public function store($userAction)
    {
        $user = auth()->user();

        $details = $userAction->details;
        
        if (@$details->total_amount > $user->balance) {
            $notify[] = 'Sorry! Insufficient balance';
            return apiResponse("validation_error", "error", $notify);
        }

        $ngo = Ngo::active()->where('id', $details->ngo_id)->first();

        if (!$ngo) {
            $notify[] = 'Sorry! NGO not found';
            return apiResponse("validation_error", "error", $notify);
        }

        $userAction->is_used = Status::YES;
        $userAction->save();

        $user->balance -= $details->total_amount;
        $user->save();

        $microfinance               = new Microfinance();
        $microfinance->user_id      = $user->id;
        $microfinance->ngo_id       = $ngo->id;
        $microfinance->amount       = $details->amount;
        $microfinance->charge       = $details->total_charge;
        $microfinance->total        = $details->total_amount;
        $microfinance->trx          = generateUniqueTrxNumber();
        $microfinance->status       = 0;
        $microfinance->user_data    = $details->user_data;
        $microfinance->save();

        $transaction                = new Transaction();
        $transaction->user_id       = $user->id;
        $transaction->amount        = $details->amount;
        $transaction->post_balance  = $user->balance;
        $transaction->charge        = $details->total_charge;
        $transaction->trx_type      = '-';
        $transaction->remark        = 'microfinance';
        $transaction->details       = 'Microfinance Pay';
        $transaction->trx           = $microfinance->trx;
        $transaction->save();

        $adminNotification            = new AdminNotification();
        $adminNotification->user_id   = $user->id;
        $adminNotification->title     = 'New microfinance payment request from ' . $user->username;
        $adminNotification->click_url = urlPath('admin.microfinance.all');
        $adminNotification->save();

        $notify[] = 'Successfully complete the microfinance payment process';
        return apiResponse("microfinance_payment_done", "success", $notify, [
            'redirect_type' => "new_url",
            'redirect_url'  => route('user.microfinance.details', $microfinance->id),
            'microfinance'  => $microfinance
        ]);

        
    }
}
