<?php

namespace App\Lib\AuthorizedTransactions;

use App\Constants\Status;
use App\Models\MoneyRequest;
use App\Models\Transaction;
use App\Models\User;

class AuthorizeRequestMoneyReceived
{
    public function store($userAction)
    {
        
        $receiver     = auth()->user();
        $details      = $userAction->details;
        $moneyRequest = MoneyRequest::where('id', $details->money_request_id)
            ->where('receiver_id', $receiver->id)
            ->where('status', Status::PENDING)
            ->first();

        if (!$moneyRequest) {
            $notify[] = 'The money request is not found';
            return apiResponse("not_found", "error", $notify);
        }

        $sender = User::active()->where('id', $moneyRequest->sender_id)->first();

        if (!$sender) {
            $notify[] = 'The request money send user currently does not exist';
            return apiResponse("not_found", "error", $notify);
        }

        $totalAmount = getAmount($moneyRequest->amount + $details->total_charge);

        if ($totalAmount > $receiver->balance) {
            $notify[] = 'Insufficient balance';
            return apiResponse("limitation", "error", $notify);
        }

        $moneyRequest->status = Status::APPROVED;
        $moneyRequest->amount = $moneyRequest->amount;
        $moneyRequest->charge = $details->total_charge;
        
        $moneyRequest->save();

        $receiver->balance -= $totalAmount;
        $receiver->save();

        $receiverTrx                = new Transaction();
        $receiverTrx->user_id       = $receiver->id;
        $receiverTrx->amount        = $moneyRequest->amount;
        $receiverTrx->post_balance  = $receiver->balance;
        $receiverTrx->charge        = $details->total_charge;
        $receiverTrx->trx_type      = '-';
        $receiverTrx->remark        = 'request_money_accept';
        $receiverTrx->details       = "The request money accept from the user " . $sender->username;
        $receiverTrx->trx           = $moneyRequest->trx;
        $receiverTrx->save();


        $sender->balance += $moneyRequest->amount;
        $sender->save();

        $senderTrx               = new Transaction();
        $senderTrx->user_id      = $sender->id;
        $senderTrx->amount       = $moneyRequest->amount;
        $senderTrx->post_balance = $sender->balance;
        $senderTrx->charge       = 0;
        $senderTrx->trx_type     = '+';
        $senderTrx->remark       = 'requested_money_fund_added';
        $senderTrx->details      = "The request money fund added  from the user " . $receiver->username;
        $senderTrx->trx          = $moneyRequest->trx;
        $senderTrx->save();

        notify($sender, 'REQUESTED_MONEY_RECEIVED', [
            'to_user'   => @$userAction->user->fullname,
            'amount'    => showAmount($moneyRequest->amount, currencyFormat: false),
            'from_user' => $receiver->fullname . ' (' . $receiver->username . ')',
            'time'      => showDateTime($senderTrx->created_at),
            'trx'       => $moneyRequest->trx,
        ]);

        $notify[] = 'Money request accept successfully';
        return apiResponse("request_approved", "success", $notify, [
            'redirect_type' => "new_url",
            'redirect_url'  => route('user.request.money.received.history'),
            'money_request' => $moneyRequest
        ]);
    }
}
