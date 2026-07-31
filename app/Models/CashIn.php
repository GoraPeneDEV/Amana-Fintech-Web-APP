<?php

namespace App\Models;

use App\Traits\ApiQuery;
use Illuminate\Database\Eloquent\Model;

class CashIn extends Model
{

    use ApiQuery;

    protected $casts = [
        'amount'             => 'double',
        'commission'         => 'double',
        'user_post_balance'  => 'double',
        'agent_post_balance' => 'double',
    ];

    public function exportColumns(): array
    {
        return  [
            'agent_id' => [
                'name' => "Agent",
                'callback' => function ($item) {
                    if ($item->agent_id != 0 && $item->agent) {
                        return $item->agent->username;
                    } else {
                        return 'N/A';
                    }
                }
            ],
            'user_id' => [
                'name' => "User",
                'callback' => function ($item) {
                    if ($item->user_id != 0 && $item->user) {
                        return $item->user->username;
                    } else {
                        return 'N/A';
                    }
                }
            ],
            'trx',
            'created_at' => [
                'name' =>  "Transacted",
                'callback' => function ($item) {
                    return showDateTime($item->created_at, lang: 'en');
                }
            ],
            'amount' => [
                'name' => "Amount",
                'callback' => function ($item) {
                    return showAmount($item->amount);
                }
            ],
            'agent_commission' => [
                'name' => "Agent Commission",
                'callback' => function ($item) {
                    return showAmount($item->commission);
                }
            ],
            'user_post_balance' => [
                'name' => "User Post Balance",
                'callback' => function ($item) {
                    return showAmount($item->user_post_balance);
                }
            ],
            'agent_post_balance' => [
                'name' => "Agent Post Balance",
                'callback' => function ($item) {
                    return showAmount($item->agent_post_balance);
                }
            ],
        ];
    }

    public function agent()
    {
        return $this->belongsTo(Agent::class, 'agent_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
