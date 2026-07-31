<?php

namespace App\Traits;

use App\Constants\Status;

trait AgentNotify
{
    public static function notifyToAgent()
    {
        return [
            'allAgents'               => 'All Agents',
            'selectedAgents'          => 'Selected Agents',

            'cashInAgents'            => 'Agents with Cash In Transactions',
            'notCashInAgents'         => 'Agents without Cash In Transactions',

            'cashOutReceiveAgents'    => 'Agents with Cash Out Transactions',
            'cashOutNotReceiveAgents' => 'Agents without Cash Out Transactions',

            'kycUnverified'           => 'KYC Unverified Agents',
            'kycVerified'             => 'KYC Verified Agents',
            'kycPending'              => 'KYC Pending Agents',


            'withBalance'             => 'Agents with Account Balance',
            'emptyBalanceAgents'      => 'Agents with Zero Balance',

            'twoFaDisableAgents'      => 'Agents with 2FA Disabled',
            'twoFaEnableAgents'       => 'Agents with 2FA Enabled',

            'hasDepositedAgents'      => 'Agents with Successful Add Money',
            'notDepositedAgents'      => 'Agents without Successful Add Money',
            'pendingDepositedAgents'  => 'Agents with Pending Add Money',
            'rejectedDepositedAgents' => 'Agents with Rejected Add Money',
            'topDepositedAgents'      => 'Top Add Money Agents',

            'hasWithdrawAgents'       => 'Agents with Approved Withdrawals',
            'pendingWithdrawAgents'   => 'Agents with Pending Withdrawals',
            'rejectedWithdrawAgents'  => 'Agents with Rejected Withdrawals',

            'pendingTicketUser'       => 'Agents with Pending Support Tickets',
            'answerTicketUser'        => 'Agents with Replied Support Tickets',
            'closedTicketUser'        => 'Agents with Closed Support Tickets',

            'notLoginAgents'          => 'Agents Not Logged In Recently',
        ];
    }

    public function scopeSelectedAgents($query)
    {
        return $query->whereIn('id', request()->agent ?? []);
    }

    public function scopeAllAgents($query)
    {
        return $query;
    }

    public function scopeCashInAgents($query)
    {
        return $query->whereHas('transactions', function ($transaction) {
            $transaction->where('remark', 'cash_in');
        });
    }

    public function scopeNotCashInAgents($query)
    {
        return $query->whereDoesntHave('transactions', function ($transaction) {
            $transaction->where('remark', 'cash_in');
        });
    }

    public function scopeCashOutReceiveAgents($query)
    {
        return $query->whereHas('transactions', function ($transaction) {
            $transaction->where('remark', 'cash_out');
        });
    }

    public function scopeCashOutNotReceiveAgents($query)
    {
        return $query->whereDoesntHave('transactions', function ($transaction) {
            $transaction->where('remark', 'cash_out');
        });
    }

    public function scopeEmptyBalanceAgents($query)
    {
        return $query->where('balance', '<=', 0);
    }

    public function scopeTwoFaDisableAgents($query)
    {
        return $query->where('ts', Status::DISABLE);
    }

    public function scopeTwoFaEnableAgents($query)
    {
        return $query->where('ts', Status::ENABLE);
    }

    public function scopeHasDepositedAgents($query)
    {
        return $query->whereHas('deposits', function ($deposit) {
            $deposit->successful();
        });
    }

    public function scopeNotDepositedAgents($query)
    {
        return $query->whereDoesntHave('deposits', function ($q) {
            $q->successful();
        });
    }

    public function scopePendingDepositedAgents($query)
    {
        return $query->whereHas('deposits', function ($deposit) {
            $deposit->pending();
        });
    }

    public function scopeRejectedDepositedAgents($query)
    {
        return $query->whereHas('deposits', function ($deposit) {
            $deposit->rejected();
        });
    }

    public function scopeTopDepositedAgents($query)
    {
        return $query->whereHas('deposits', function ($deposit) {
            $deposit->successful();
        })->withSum(['deposits' => function ($q) {
            $q->successful();
        }], 'amount')->orderBy('deposits_sum_amount', 'desc')->take(request()->number_of_top_deposited_agent ?? 10);
    }

    public function scopeHasWithdrawAgents($query)
    {
        return $query->whereHas('withdrawals', function ($q) {
            $q->approved();
        });
    }

    public function scopePendingWithdrawAgents($query)
    {
        return $query->whereHas('withdrawals', function ($q) {
            $q->pending();
        });
    }

    public function scopeRejectedWithdrawAgents($query)
    {
        return $query->whereHas('withdrawals', function ($q) {
            $q->rejected();
        });
    }

    public function scopePendingTicketUser($query)
    {
        return $query->whereHas('tickets', function ($q) {
            $q->whereIn('status', [Status::TICKET_OPEN, Status::TICKET_REPLY]);
        });
    }

    public function scopeClosedTicketUser($query)
    {
        return $query->whereHas('tickets', function ($q) {
            $q->where('status', Status::TICKET_CLOSE);
        });
    }

    public function scopeAnswerTicketUser($query)
    {
        return $query->whereHas('tickets', function ($q) {
            $q->where('status', Status::TICKET_ANSWER);
        });
    }

    public function scopeNotLoginAgents($query)
    {
        return $query->whereDoesntHave('loginLogs', function ($q) {
            $q->whereDate('created_at', '>=', now()->subDays(request()->number_of_days ?? 10));
        });
    }

    public function scopeKycVerified($query)
    {
        return $query->where('kv', Status::KYC_VERIFIED);
    }


}
