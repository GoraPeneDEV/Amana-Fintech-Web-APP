<?php

namespace App\Lib\AuthorizedTransactions;

use App\Constants\Status;
use App\Models\AdminNotification;
use App\Models\Bank;
use App\Models\BankTransfer;
use App\Models\Transaction;

class AuthorizeBankTransfer
{
    public function store($userAction)
    {
        $user = auth()->user();
        $details = $userAction->details;
        $bank    = Bank::active()->where('id', $details->bank_id)->first();

        if (!$bank) {
            $notify[] = 'Sorry! Bank not found';
            return apiResponse("validation_error", "error", $notify);
        }

        if (@$details->total_amount > $user->balance) {
            $notify[] = 'Sorry! Insufficient balance';
            return apiResponse("validation_error", "error", $notify);
        }

        $userAction->is_used = Status::YES;
        $userAction->save();

        $user->balance -= $details->total_amount;
        $user->save();

        $trx = generateUniqueTrxNumber();

        $transfer                   = new BankTransfer();
        $transfer->account_number   = $details->account_number;
        $transfer->account_holder   = $details->account_holder;
        $transfer->user_id          = $user->id;
        $transfer->bank_id          = $bank->id;
        $transfer->amount           = $details->amount;
        $transfer->charge           = $details->total_charge;
        $transfer->total            = $details->total_amount;
        $transfer->trx              = $trx;
        $transfer->status           = 0;
        $transfer->user_data        = $details->user_data;
        $transfer->save();

        $transaction                = new Transaction();
        $transaction->user_id       = $user->id;
        $transaction->amount        = $details->amount;
        $transaction->post_balance  = $user->balance;
        $transaction->charge        = $details->total_charge;
        $transaction->trx_type      = '-';
        $transaction->remark        = 'bank_transfer';
        $transaction->details       = 'Bank transfer';
        $transaction->trx           = $trx;
        $transaction->save();

        $adminNotification            = new AdminNotification();
        $adminNotification->user_id   = $user->id;
        $adminNotification->title     = 'New bank transfer request from ' . $user->username;
        $adminNotification->click_url = urlPath('admin.bank.transfer.all');
        $adminNotification->save();

        $notify[] = 'Bank transfer completed successfully';
        return apiResponse("bank_transfer_done", "success", $notify, [
            'redirect_type' => "new_url",
            'redirect_url'  => route('user.bank.transfer.details', $transfer->id),
            'bank_transfer' => $transfer
        ]);
    }
}
