<?php

namespace App\Models;

use App\Traits\ApiQuery;
use Illuminate\Database\Eloquent\Model;

class MakePayment extends Model
{
    use ApiQuery;

    protected $casts = [
        'amount'                => 'double',
        'charge'                => 'double',
        'merchant_amount'       => 'double',
        'user_post_balance'     => 'double',
        'merchant_post_balance' => 'double',
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
            'merchant_id' => [
                'name' => "Merchant",
                'callback' => function ($item) {
                    if ($item->merchant_id != 0 && $item->merchant) {
                        return $item->merchant->username;
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
            'merchant_amount' => [
                'name' => "Merchant Amount",
                'callback' => function ($item) {
                    return showAmount($item->merchant_amount);
                }
            ],
            'user_post_balance' => [
                'name' => "User Post Balance",
                'callback' => function ($item) {
                    return showAmount($item->user_post_balance);
                }
            ],
            'merchant_post_balance' => [
                'name' => "Merchant Post Balance",
                'callback' => function ($item) {
                    return showAmount($item->merchant_post_balance);
                }
            ],
        ];
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function merchant()
    {
        return $this->belongsTo(Merchant::class, 'merchant_id');
    }
}
