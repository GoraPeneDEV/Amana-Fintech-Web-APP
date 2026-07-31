<?php

namespace App\Lib\AuthorizedTransactions;

use App\Constants\Status;
use App\Models\SendMoney;
use App\Models\Transaction;
use App\Models\User;

class AuthorizeSendMoney
{
    public function store($userAction)
    {
        $sendUser = auth()->user();
        $details = $userAction->details;

        $receivedUser = User::active()->where('id', $details->receiver_id)->first();

        if (!$receivedUser) {
            $notify[] = 'Sorry! User not found';
            return apiResponse("user_not_found", "error", $notify);
        }

        if (@$userAction->details->total_amount > $sendUser->balance) {
            $notify[] = 'Sorry! Insufficient balance';
            return apiResponse("insufficient_balance", "error", $notify);
        }

        $userAction->is_used = Status::YES;
        $userAction->save();

        $amount      = $details->amount;
        $totalAmount = $details->total_amount;
        $totalCharge = $details->total_charge;

        $sendUser->balance -= $totalAmount;
        $sendUser->save();

        $senderTrx                = new Transaction();
        $senderTrx->user_id       = $sendUser->id;
        $senderTrx->amount        = $amount;
        $senderTrx->post_balance  = $sendUser->balance;
        $senderTrx->charge        = $totalCharge;
        $senderTrx->trx_type      = '-';
        $senderTrx->remark        = 'send_money';
        $senderTrx->details       = 'Send Money to ' . $receivedUser->fullname;
        $senderTrx->trx           = generateUniqueTrxNumber();
        $senderTrx->save();

        $receivedUser->balance += $amount;
        $receivedUser->save();

        $receivedUserTrx                = new Transaction();
        $receivedUserTrx->user_id       = $receivedUser->id;
        $receivedUserTrx->amount        = $amount;
        $receivedUserTrx->post_balance  = $receivedUser->balance;
        $receivedUserTrx->charge        = 0;
        $receivedUserTrx->trx_type      = '+';
        $receivedUserTrx->remark        = 'receive_money';
        $receivedUserTrx->details       = 'Received Money From ' . $sendUser->fullname;
        $receivedUserTrx->trx           = $senderTrx->trx;
        $receivedUserTrx->save();

        $sendMoney                          = new SendMoney();
        $sendMoney->sender_id               = $sendUser->id;
        $sendMoney->receiver_id             = $receivedUser->id;
        $sendMoney->amount                  = $amount;
        $sendMoney->charge                  = $totalCharge;
        $sendMoney->total_amount            = $totalAmount;
        $sendMoney->sender_post_balance     = $sendUser->balance;
        $sendMoney->receiver_post_balance   = $receivedUser->balance;
        $sendMoney->sender_details          = 'Send Money to ' . $receivedUser->fullname;
        $sendMoney->receiver_details        = 'Received Money From ' . $sendUser->fullname;
        $sendMoney->trx                     = $senderTrx->trx;
        $sendMoney->save();

        notify($receivedUser, 'RECEIVED_MONEY', [
            'to_user'   => $receivedUser->fullname,
            'amount'    => showAmount($amount, currencyFormat: false),
            'from_user' => $sendUser->fullname . ' ( ' . $sendUser->username . ' )',
            'trx'       => $senderTrx->trx,
            'time'      => showDateTime($senderTrx->created_at),
            'balance'   => showAmount($receivedUser->balance, currencyFormat: false),
        ]);

        $notify[] = 'Send money successful';
        return apiResponse('send_money', 'success', $notify, [
            'redirect_type' => "new_url",
            'redirect_url'  => route('user.send.money.details', $sendMoney->id),
            'send_money' => $sendMoney
        ]);
    }
}
