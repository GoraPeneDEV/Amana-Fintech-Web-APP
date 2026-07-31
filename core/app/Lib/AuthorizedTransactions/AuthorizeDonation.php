<?php

namespace App\Lib\AuthorizedTransactions;

use App\Constants\Status;
use App\Models\Charity;
use App\Models\Donation;
use App\Models\Transaction;

class AuthorizeDonation
{
    public function store($userAction)
    {

        $user = auth()->user();

        $details = $userAction->details;

        if (@$details->amount > $user->balance) {
            $notify[] = "Sorry! Insufficient balance";
            return apiResponse("validation_error", "error", $notify);
        }

        $charity = Charity::active()->where('id', $details->charity_id)->first();
        if (!$charity) {
            $notify[] = "Sorry! Charity not found";
            return apiResponse("not_found", "error", $notify);
        }

        $userAction->is_used = Status::YES;
        $userAction->save();

        $user->balance -= $details->amount;
        $user->save();

        $trx = generateUniqueTrxNumber();

        $transaction                    = new Transaction();
        $transaction->user_id           = $user->id;
        $transaction->amount            = $details->amount;
        $transaction->post_balance      = $user->balance;
        $transaction->charge            = 0;
        $transaction->trx_type          = '-';
        $transaction->remark            = 'donation';
        $transaction->details           = 'Donation';
        $transaction->trx               = $trx;
        $transaction->save();

        $donation                = new Donation();
        $donation->charity_id    = $charity->id;
        $donation->user_id       = $user->id;
        $donation->amount        = $details->amount;
        $donation->post_balance  = $user->balance;
        $donation->trx           = $trx;
        $donation->reference     = $details->reference ?? request()->reference;
        $donation->hide_identity = $details->hide_identity ?? 0;
        $donation->save();

        notify($user, 'DONATION', [
            'amount'       => showAmount($details->amount, currencyFormat: false),
            'name'         => $details->name,
            'email'        => $details->email,
            'charge'       => 0,
            'donation_for' => $charity->name,
            'trx'          => $transaction->trx,
            'time'         => showDateTime($transaction->created_at),
            'balance'      => showAmount($user->balance, currencyFormat: false),
        ]);

        $notify[] = "Donation successful";
        return apiResponse("donation_done", "success", $notify, [
            'redirect_type' => "new_url",
            'redirect_url'  => route('user.donation.details', $donation->id),
            'donation' => $donation
        ]);
    }
}
