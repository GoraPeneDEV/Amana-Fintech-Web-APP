<?php

namespace App\Models;

use App\Constants\Status;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;

class Investment extends Model
{

    protected $casts = [
        'next_return_at'            => 'datetime',
        'last_return_at'            => 'datetime',
        'per_interest_amount'       => 'double',
        'invest_amount'             => 'double',
        'total_interest_amount'     => 'double',
        'total_interest_amount_get' => 'double',
    ];

    public function exportColumns(): array
    {
        return [
            'user'         => [
                'name'     => "User",
                'callback' => function ($item) {
                    return $item->user->username;
                },
            ],
            'plan_id'    => [
                'name'     => "Plan Name",
                'callback' => function ($item) {
                    return $item->plan->name;
                },
            ],
            'trx',
            'created_at'   => [
                'name'     => "transacted",
                'callback' => function ($item) {
                    return showDateTime($item->created_at, lang: 'en');
                },
            ],
            'capital_back' => [
                'name'     => "Capital Back",
                'callback' => function ($item) {
                    return $item->plan->capital_back ? 'Yes' : 'No';
                },
            ],
            'amount'       => [
                'name'     => "Amount",
                'callback' => function ($item) {
                    return $item->invest_amount;
                },
            ],
            'total_interest'       => [
                'name'     => "Total Interest",
                'callback' => function ($item) {
                    return $item->total_interest_amount;
                },
            ],
            'status'       => [
                'name'     => "Status",
                'callback' => function ($item) {
                    return $item->status ? 'Completed' : 'Running';
                },
            ],

        ];
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function plan()
    {
        return $this->belongsTo(InvestPlan::class, 'plan_id');
    }

    public function investmentInterests()
    {
        return $this->hasMany(InvestmentInterest::class, 'investment_id');
    }

    public function scopeRunning($query)
    {
        return $query->where('status', Status::INVESTMENT_RUNNING);
    }

    public function scopeCompleted($query)
    {
        return $query->where('status', Status::INVESTMENT_COMPLETED);
    }

    public function statusBadge(): Attribute
    {
        return new Attribute(
            get: function () {
                if ($this->status == Status::INVESTMENT_RUNNING) {
                    return '<span class="badge badge--success">' . trans('Running') . '</span>';
                } elseif ($this->status == Status::INVESTMENT_COMPLETED) {
                    return '<span class="badge badge--dark">' . trans('Completed') . '</span>';
                }
                return '';
            }
        );
    }
}
