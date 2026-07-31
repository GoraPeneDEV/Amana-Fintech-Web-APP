<?php

namespace App\Models;

use App\Traits\ApiQuery;
use Illuminate\Database\Eloquent\Model;

class SendMoney extends Model
{

    use ApiQuery;

    protected $casts = [
        'amount'                 => 'double',
        'charge'                 => 'double',
        'total_amount'           => 'double',
        'sender_post_balance'    => 'double',
        'receiver_post_balance'  => 'double',
    ];

    public function exportColumns(): array
    {
        return  [
            'sender_id' => [
                'name' => "Sender",
                'callback' => function ($item) {
                    if ($item->sender_id != 0 && $item->user) {
                        return $item->user->username;
                    } else {
                        return 'N/A';
                    }
                }
            ],
            'receiver_id' => [
                'name' => "Receiver",
                'callback' => function ($item) {
                    if ($item->receiver_id != 0 && $item->receiverUser) {
                        return $item->receiverUser->username;
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
                'name' =>  "Amount",
                'callback' => function ($item) {
                    return showAmount($item->amount);
                }
            ],
            'charge' => [
                'name' =>  "Charge",
                'callback' => function ($item) {
                    return showAmount($item->charge);
                }
            ],
            'total' => [
                'name' =>  "Total",
                'callback' => function ($item) {
                    return showAmount($item->total_amount);
                }
            ],
            'sender_post_balance' => [
                'name' =>  "Sender Post Balance",
                'callback' => function ($item) {
                    return showAmount($item->sender_post_balance);
                }
            ],
            'receiver_post_balance' => [
                'name' =>  "Receiver Post Balance",
                'callback' => function ($item) {
                    return showAmount($item->receiver_post_balance);
                }
            ],
        ];
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'sender_id');
    }

    public function receiverUser()
    {
        return $this->belongsTo(User::class, 'receiver_id');
    }
}
