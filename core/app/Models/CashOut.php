<?php

namespace App\Models;

use App\Traits\ApiQuery;
use Illuminate\Database\Eloquent\Model;

class CashOut extends Model
{
    use ApiQuery;

    protected $casts = [
        'amount'             => 'double',
        'charge'             => 'double',
        'total_amount'       => 'double',
        'commission'         => 'double',
        'user_post_balance'  => 'double',
        'agent_post_balance' => 'double',
    ];

    public function exportColumns(): array
    {
        return  [
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
            'agent_id' => [
                'name' => "Agent",
                'callback' => function ($item) {
                    if ($item->agent_id != 0 && $item->receiverAgent) {
                        return $item->receiverAgent->username;
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
            'charge' => [
                'name' => "Charge",
                'callback' => function ($item) {
                    return showAmount($item->charge);
                }
            ],
            'total' => [
                'name' => "Total",
                'callback' => function ($item) {
                    return showAmount($item->total_amount);
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

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function receiverAgent()
    {
        return $this->belongsTo(Agent::class, 'agent_id');
    }
}
