<?php

namespace App\Lib\AuthorizedTransactions;

use App\Lib\Reloadly;
use App\Models\Topup;
use App\Models\Transaction;
use Exception;

class AirTime
{
    public function store($userAction)
    {

        $user    = auth()->user();
        $details = $userAction->details;

        if (@$details->amount > $user->balance) {
            $notify[] = 'Sorry! Insufficient balance';
            return apiResponse("validation_error", "error", $notify);
        }

        $recipient['number']      = $details->mobile_number;
        $recipient['countryCode'] = $details->country_iso_name;

        try {
            $reloadly             = new Reloadly();
            $reloadly->operatorId = $details->operator_unique_id;
            $response             = $reloadly->topUp($details->amount, $recipient);

            if (!@$response['status']) {
                $notify[] = @$response['message'] ?? "Something went wrong";
                return apiResponse("something_wrong", "error", $notify);
            }
        } catch (Exception $ex) {
            $notify[] = $ex->getMessage() ?? "The airtime top-up processing failed. Please try again later.";
            return apiResponse("something_wrong", "error", $notify);
        }

        $trx = generateUniqueTrxNumber();

        $topup                = new Topup();
        $topup->user_id       = $user->id;
        $topup->operator_id   = $details->operator_id;
        $topup->amount        = $details->amount;
        $topup->charge        = 0;
        $topup->post_balance  = $user->balance;
        $topup->trx           = $trx;
        $topup->details       = 'Top-up ' . $details->amount . ' ' . gs('cur_text') . ' to ' . $details->dial_code . $details->mobile_number;
        $topup->mobile_number = $details->mobile_number;
        $topup->dial_code     = $details->dial_code;
        $topup->save();

        $user->balance -= $details->amount;
        $user->save();

        $transaction               = new Transaction();
        $transaction->user_id      = $user->id;
        $transaction->amount       = $details->amount;
        $transaction->charge       = 0;
        $transaction->post_balance = $user->balance;
        $transaction->trx_type     = '-';
        $transaction->trx          = $trx;
        $transaction->details      = 'Top-up ' . $details->amount . ' ' . gs('cur_text') . ' to ' . $details->dial_code . $details->mobile_number;
        $transaction->remark       = 'top_up';
        $transaction->save();

        notify($user, 'TOP_UP', [
            'amount'        => showAmount($details->amount, currencyFormat: false),
            'mobile_number' => $details->dial_code . $details->mobile_number,
            'post_balance'  => showAmount($user->balance, currencyFormat: false),
        ]);

        $notify[] = 'Top-Up completed successfully';

        return apiResponse("top_up_success", "success", $notify, [
            'redirect_type' => 'same_url',
            'airtime'       => $topup,
            'transaction'   => $transaction
        ]);
    }
}
