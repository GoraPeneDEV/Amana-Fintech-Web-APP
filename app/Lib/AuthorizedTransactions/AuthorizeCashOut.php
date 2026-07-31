<?php

namespace App\Lib\AuthorizedTransactions;

use App\Constants\Status;
use App\Models\Agent;
use App\Models\CashOut;
use App\Models\Transaction;

class AuthorizeCashOut
{
    public function store($userAction)
    {
        $user = auth()->user();

        $details = $userAction->details;
        $agent   = Agent::active()->where('id', $details->agent_id)->first();

        if (!$agent) {
            $notify[] = 'Sorry! agent not found';
            return apiResponse("agent_not_found", "error", $notify);
        }

        if (@$userAction->details->totalAmount > $user->balance) {
            $notify[] = 'Sorry! Insufficient balance';
            return apiResponse("insufficient_balance", "error", $notify);
        }

        $userAction->is_used = Status::YES;
        $userAction->save();

        $user->balance -= $details->total_amount;
        $user->save();

        $senderTrx                = new Transaction();
        $senderTrx->user_id       = $user->id;
        $senderTrx->amount        = $details->amount;
        $senderTrx->post_balance  = $user->balance;
        $senderTrx->charge        = $details->total_charge;
        $senderTrx->trx_type      = '-';
        $senderTrx->remark        = 'cash_out';
        $senderTrx->details       = 'Cash out to ' . $agent->fullname;
        $senderTrx->trx           = getTrx();
        $senderTrx->save();

        $agent->balance += $details->amount;
        $agent->save();

        $agentTrx                = new Transaction();
        $agentTrx->agent_id      = $agent->id;
        $agentTrx->amount        = $details->amount;
        $agentTrx->post_balance  = $agent->balance;
        $agentTrx->charge        = 0;
        $agentTrx->trx_type      = '+';
        $agentTrx->remark        = 'cash_out';
        $agentTrx->details       = 'Cash out from ' . $user->fullname;
        $agentTrx->trx           = $senderTrx->trx;
        $agentTrx->save();

        if ($details->total_commission) {
            //Agent commission
            $agent->balance += $details->total_commission;
            $agent->save();

            $agentCommissionTrx               = new Transaction();
            $agentCommissionTrx->agent_id     = $agent->id;
            $agentCommissionTrx->amount       = $details->total_commission;
            $agentCommissionTrx->post_balance = $agent->balance;
            $agentCommissionTrx->charge       = 0;
            $agentCommissionTrx->trx_type     = '+';
            $agentCommissionTrx->remark       = 'cash_out_commission';
            $agentCommissionTrx->details      = 'Cash out commission for ' . $user->fullname;
            $agentCommissionTrx->trx          = $senderTrx->trx;
            $agentCommissionTrx->save();

            //Agent commission
            notify($agent, 'CASH_OUT_COMMISSION_AGENT', [
                'agent'      => $agent->fullname,
                'amount'     => showAmount($details->amount, currencyFormat: false),
                'commission' => showAmount($details->total_commission, currencyFormat: false),
                'trx'        => $senderTrx->trx,
                'time'       => showDateTime($senderTrx->created_at),
                'balance'    => showAmount($agent->balance, currencyFormat: false),
            ]);
        }

        $cashOut                      = new CashOut();
        $cashOut->user_id             = $user->id;
        $cashOut->agent_id            = $agent->id;
        $cashOut->amount              = $details->amount;
        $cashOut->charge              = $details->total_charge;
        $cashOut->total_amount        = $details->total_amount;
        $cashOut->commission          = $details->total_commission;
        $cashOut->user_post_balance   = $user->balance;
        $cashOut->agent_post_balance  = $agent->balance;
        $cashOut->user_details        = 'Cash out to ' . $agent->fullname;
        $cashOut->agent_details       = 'Cash out from ' . $user->fullname;
        $cashOut->trx                 = $senderTrx->trx;
        $cashOut->save();

        //To agent
        notify($agent, 'CASH_OUT_TO_AGENT', [
            'agent'   => $agent->fullname,
            'amount'  => showAmount($details->amount, currencyFormat: false),
            'user'    => $user->fullname . ' ( ' . $user->username . ' )',
            'trx'     => $senderTrx->trx,
            'time'    => showDateTime($senderTrx->created_at),
            'balance' => showAmount($agent->balance, currencyFormat: false),
        ]);

        $notify[] = 'Cash out successful';
        return apiResponse("cash_out_done", "success", $notify, [
            'redirect_type' => "new_url",
            'redirect_url'  => route('user.cash.out.details', $cashOut->id),
            'cash_out' => $cashOut
        ]);
    }
}
