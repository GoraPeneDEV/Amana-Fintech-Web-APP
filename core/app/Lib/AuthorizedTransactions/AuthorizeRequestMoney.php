<?php

namespace App\Lib\AuthorizedTransactions;

use App\Constants\Status;
use App\Models\MoneyRequest;
use App\Models\User;

class AuthorizeRequestMoney
{
    public function store($userAction)
    {

        $receiver = User::where('id', $userAction->details->receiver_id)->first();

        if (!$receiver) {
            $notify[] = 'Receiver not found';
            return apiResponse("receiver_not_found", "error", $notify);
        }

        $details = $userAction->details;
        $note    = request()->note ??  $details->note;

        $moneyRequest              = new MoneyRequest();
        $moneyRequest->sender_id   = $details->sender_id;
        $moneyRequest->receiver_id = $details->receiver_id;
        $moneyRequest->amount      = $details->amount;
        $moneyRequest->trx         = generateUniqueTrxNumber();
        $moneyRequest->note        = $note;
        $moneyRequest->status      = Status::PENDING;
        $moneyRequest->save();

        notify($receiver, 'MONEY_REQUESTED', [
            'to_user'   => @$userAction->user->fullname,
            'amount'    => showAmount($details->amount, currencyFormat: false),
            'from_user' => $receiver->fullname . ' (' . $receiver->username . ')',
            'note'      => $note,
            'time'      => showDateTime($moneyRequest->created_at),
        ]);

        $notify[] = 'Money request sent successfully';
        return apiResponse("request_sent", "success", $notify, [
            'redirect_type' => "new_url",
            'redirect_url'  => route('user.request.money.details', $moneyRequest->id),
            'money_request'  => $moneyRequest
        ]);
    }
}
