<?php

namespace App\Traits;

use App\Constants\Status;

trait MerchantNotify
{
    public static function notifyToMerchant()
    {
        return [
            'allMerchants'               => 'All Merchants',
            'selectedMerchants'          => 'Selected Merchants',

            'cashInMerchants'            => 'Merchants with Cash In Transactions',
            'notCashInMerchants'         => 'Merchants without Cash In Transactions',

            'cashOutReceiveMerchants'    => 'Merchants with Cash Out Transactions',
            'cashOutNotReceiveMerchants' => 'Merchants without Cash Out Transactions',

            'kycUnverified'           => 'KYC Unverified Merchants',
            'kycVerified'             => 'KYC Verified Merchants',
            'kycPending'              => 'KYC Pending Merchants',

            'withBalance'             => 'Merchants with Account Balance',
            'emptyBalanceMerchants'      => 'Merchants with Zero Balance',

            'twoFaDisableMerchants'      => 'Merchants with 2FA Disabled',
            'twoFaEnableMerchants'       => 'Merchants with 2FA Enabled',

            'hasDepositedMerchants'      => 'Merchants with Successful Deposits',
            'notDepositedMerchants'      => 'Merchants without Successful Deposits',
            'pendingDepositedMerchants'  => 'Merchants with Pending Deposits',
            'rejectedDepositedMerchants' => 'Merchants with Rejected Deposits',
            'topDepositedMerchants'      => 'Top Depositing Merchants',

            'hasWithdrawMerchants'       => 'Merchants with Approved Withdrawals',
            'pendingWithdrawMerchants'   => 'Merchants with Pending Withdrawals',
            'rejectedWithdrawMerchants'  => 'Merchants with Rejected Withdrawals',

            'pendingTicketUser'       => 'Merchants with Pending Support Tickets',
            'answerTicketUser'        => 'Merchants with Replied Support Tickets',
            'closedTicketUser'        => 'Merchants with Closed Support Tickets',

            'notLoginMerchants'          => 'Merchants Not Logged In Recently',
        ];
    }

    public function scopeSelectedMerchants($query)
    {
        return $query->whereIn('id', request()->merchant ?? []);
    }

    public function scopeAllMerchants($query)
    {
        return $query;
    }

    public function scopeCashInMerchants($query)
    {
        return $query->whereHas('transactions', function ($transaction) {
            $transaction->where('remark', 'cash_in');
        });
    }

    public function scopeNotCashInMerchants($query)
    {
        return $query->whereDoesntHave('transactions', function ($transaction) {
            $transaction->where('remark', 'cash_in');
        });
    }

    public function scopeCashOutReceiveMerchants($query)
    {
        return $query->whereHas('transactions', function ($transaction) {
            $transaction->where('remark', 'cash_out');
        });
    }

    public function scopeCashOutNotReceiveMerchants($query)
    {
        return $query->whereDoesntHave('transactions', function ($transaction) {
            $transaction->where('remark', 'cash_out');
        });
    }

    public function scopeEmptyBalanceMerchants($query)
    {
        return $query->where('balance', '<=', 0);
    }

    public function scopeTwoFaDisableMerchants($query)
    {
        return $query->where('ts', Status::DISABLE);
    }

    public function scopeTwoFaEnableMerchants($query)
    {
        return $query->where('ts', Status::ENABLE);
    }

    public function scopeHasDepositedMerchants($query)
    {
        return $query->whereHas('deposits', function ($deposit) {
            $deposit->successful();
        });
    }

    public function scopeNotDepositedMerchants($query)
    {
        return $query->whereDoesntHave('deposits', function ($q) {
            $q->successful();
        });
    }

    public function scopePendingDepositedMerchants($query)
    {
        return $query->whereHas('deposits', function ($deposit) {
            $deposit->pending();
        });
    }

    public function scopeRejectedDepositedMerchants($query)
    {
        return $query->whereHas('deposits', function ($deposit) {
            $deposit->rejected();
        });
    }

    public function scopeTopDepositedMerchants($query)
    {
        return $query->whereHas('deposits', function ($deposit) {
            $deposit->successful();
        })->withSum(['deposits' => function ($q) {
            $q->successful();
        }], 'amount')->orderBy('deposits_sum_amount', 'desc')->take(request()->number_of_top_deposited_merchant ?? 10);
    }

    public function scopeHasWithdrawMerchants($query)
    {
        return $query->whereHas('withdrawals', function ($q) {
            $q->approved();
        });
    }

    public function scopePendingWithdrawMerchants($query)
    {
        return $query->whereHas('withdrawals', function ($q) {
            $q->pending();
        });
    }

    public function scopeRejectedWithdrawMerchants($query)
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

    public function scopeNotLoginMerchants($query)
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
