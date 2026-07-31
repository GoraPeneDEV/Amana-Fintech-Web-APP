<?php

namespace App\Lib\AuthorizedTransactions;

use App\Constants\Status;
use App\Models\AdminNotification;
use App\Models\Investment;
use App\Models\InvestPlan;
use App\Models\Transaction;
use Carbon\Carbon;

class AuthorizeInvestment
{

    public function store($userAction)
    {

        $user             = auth()->user();
        $details          = $userAction->details;
        $planId           = $details->plan_id;
        $investmentAmount = $details->invest_amount;

        if ($investmentAmount > $user->balance) {
            $notify[] = "Sorry! Insufficient balance";
            return apiResponse("insufficient", 'error', $notify);
        }

        $plan = InvestPlan::active()->find($planId);

        $investment                = new Investment();
        $investment->user_id       = $user->id;
        $investment->plan_id       = $planId;
        $investment->invest_amount = $investmentAmount;

        $amountPerInterest   = 0;
        $totalInterestAmount = 0;
        $totalRepeat         = 0;

        if ($plan->interest_type == Status::INTEREST_TYPE_FIXED) {
            $amountPerInterest = $plan->interest_amount;
        } else {
            $amountPerInterest = $investment->invest_amount * ($plan->interest_amount / 100);
        }

        if ($plan->return_type == Status::INVEST_LIFETIME) {
            $totalInterestAmount = Status::LIFETIME;
            $totalRepeat         = Status::LIFETIME;
        } else {
            $totalRepeat         = $plan->repeat_times;
            $totalInterestAmount = $amountPerInterest * $totalRepeat;
        }

        $investment->per_interest_amount   = $amountPerInterest;
        $investment->total_interest_amount = $totalInterestAmount;
        $investment->capital_back          = $plan->capital_back;
        $investment->total_repeat          = $totalRepeat;
        $investment->trx                   = getTrx();
        $investment->status                = Status::INVESTMENT_RUNNING;
        $investment->next_return_at        = Carbon::parse(now()->addHours((int) $plan->investTime->time));
        $investment->save();

        $user->balance -= $investment->invest_amount;
        $user->save();

        $transaction               = new Transaction();
        $transaction->user_id      = $user->id;
        $transaction->amount       = $investment->invest_amount;
        $transaction->post_balance = $user->balance;
        $transaction->trx_type     = '-';
        $transaction->details      = 'Investment to ' . $plan->name . ' Plan';
        $transaction->trx          = $investment->trx;
        $transaction->remark       = 'investment';
        $transaction->save();

        $adminNotification          = new AdminNotification();
        $adminNotification->user_id = $user->id;
        $adminNotification->title   = 'New investment from ' . $user->username;
        $adminNotification->save();

        notify($user, 'INVESTMENT_SUCCESS', [
            'user'                  => $investment->user->username,
            'trx'                   => $investment->trx,
            'amount'                => showAmount($investment->invest_amount),
            'plan_name'             => $plan->name,
            'time'                  => showDateTime($investment->created_at),
            'remain_balance'        => showAmount($user->balance),
            'amount_per_interest'   => showAmount($amountPerInterest),
            'total_interest_amount' => $totalInterestAmount == Status::LIFETIME ? 'Unlimited' : showAmount($totalInterestAmount),
            'capital_back'          => $plan->capital_back ? "Yes" : "No",
            'next_return_at'        => showDateTime($investment->next_return_at),
        ]);

        $notify[] = 'Investment Successful';

        return apiResponse('investment', 'success', $notify, [
            'redirect_type' => "new_url",
            'redirect_url'  => route('user.investment.history'),
            'investment'    => $investment,
        ]);
    }
}
