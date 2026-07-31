<?php

namespace App\Traits;

use App\Constants\Status;

trait AdminNotify
{
    public static function notifyToAdmin()
    {
        return [
            'allAdmins'               => 'All Admins',
            'selectedAdmins'          => 'Selected Admins',

            'cashInAdmins'            => 'Admins with Cash In Transactions',
            'notCashInAdmins'         => 'Admins without Cash In Transactions',

            'cashOutReceiveAdmins'    => 'Admins with Cash Out Transactions',
            'cashOutNotReceiveAdmins' => 'Admins without Cash Out Transactions',

            'kycUnverified'           => 'KYC Unverified Admins',
            'kycVerified'             => 'KYC Verified Admins',
            'kycPending'              => 'KYC Pending Admins',

            'withBalance'             => 'Admins with Account Balance',
            'emptyBalanceAdmins'      => 'Admins with Zero Balance',

            'twoFaDisableAdmins'      => 'Admins with 2FA Disabled',
            'twoFaEnableAdmins'       => 'Admins with 2FA Enabled',

            'hasDepositedAdmins'      => 'Admins with Successful Add Money',
            'notDepositedAdmins'      => 'Admins without Successful Add Money',
            'pendingDepositedAdmins'  => 'Admins with Pending Add Money',
            'rejectedDepositedAdmins' => 'Admins with Rejected Add Money',
            'topDepositedAdmins'      => 'Top Add Money Admins',

            'hasWithdrawAdmins'       => 'Admins with Approved Withdrawals',
            'pendingWithdrawAdmins'   => 'Admins with Pending Withdrawals',
            'rejectedWithdrawAdmins'  => 'Admins with Rejected Withdrawals',

            'pendingTicketUser'       => 'Admins with Pending Support Tickets',
            'answerTicketUser'        => 'Admins with Replied Support Tickets',
            'closedTicketUser'        => 'Admins with Closed Support Tickets',

            'notLoginAdmins'          => 'Admins Not Logged In Recently',
        ];
    }

    public function scopeSelectedAdmins($query)
    {
        return $query->whereIn('id', request()->admin ?? []);
    }

    public function scopeAllAdmins($query)
    {
        return $query;
    }

    public function scopeCashInAdmins($query)
    {
        return $query->whereHas('transactions', function ($transaction) {
            $transaction->where('remark', 'cash_in');
        });
    }

    public function scopeNotCashInAdmins($query)
    {
        return $query->whereDoesntHave('transactions', function ($transaction) {
            $transaction->where('remark', 'cash_in');
        });
    }

    public function scopeCashOutReceiveAdmins($query)
    {
        return $query->whereHas('transactions', function ($transaction) {
            $transaction->where('remark', 'cash_out');
        });
    }

    public function scopeCashOutNotReceiveAdmins($query)
    {
        return $query->whereDoesntHave('transactions', function ($transaction) {
            $transaction->where('remark', 'cash_out');
        });
    }

    public function scopeEmptyBalanceAdmins($query)
    {
        return $query->where('balance', '<=', 0);
    }

    public function scopeTwoFaDisableAdmins($query)
    {
        return $query->where('ts', Status::DISABLE);
    }

    public function scopeTwoFaEnableAdmins($query)
    {
        return $query->where('ts', Status::ENABLE);
    }

    public function scopeHasDepositedAdmins($query)
    {
        return $query->whereHas('deposits', function ($deposit) {
            $deposit->successful();
        });
    }

    public function scopeNotDepositedAdmins($query)
    {
        return $query->whereDoesntHave('deposits', function ($q) {
            $q->successful();
        });
    }

    public function scopePendingDepositedAdmins($query)
    {
        return $query->whereHas('deposits', function ($deposit) {
            $deposit->pending();
        });
    }

    public function scopeRejectedDepositedAdmins($query)
    {
        return $query->whereHas('deposits', function ($deposit) {
            $deposit->rejected();
        });
    }

    public function scopeTopDepositedAdmins($query)
    {
        return $query->whereHas('deposits', function ($deposit) {
            $deposit->successful();
        })->withSum(['deposits' => function ($q) {
            $q->successful();
        }], 'amount')->orderBy('deposits_sum_amount', 'desc')->take(request()->number_of_top_deposited_admin ?? 10);
    }

    public function scopeHasWithdrawAdmins($query)
    {
        return $query->whereHas('withdrawals', function ($q) {
            $q->approved();
        });
    }

    public function scopePendingWithdrawAdmins($query)
    {
        return $query->whereHas('withdrawals', function ($q) {
            $q->pending();
        });
    }

    public function scopeRejectedWithdrawAdmins($query)
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

    public function scopeNotLoginAdmins($query)
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
