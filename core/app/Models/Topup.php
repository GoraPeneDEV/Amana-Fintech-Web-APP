<?php

namespace App\Models;

use App\Traits\ApiQuery;
use Illuminate\Database\Eloquent\Model;

class Topup extends Model
{
    use ApiQuery;

    protected $casts = [
        'amount'        => 'double',
        'charge'        => 'double',
        'post_balance'  => 'double',
        'mobile_number' => 'integer',
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
            'operator' => [
                'name' => "Operator",
                'callback' => function ($item) {
                    return $item->operator->name;
                }
            ],
            'mobile_number' => [
                'name' => "Mobile Number",
                'callback' => function ($item) {
                    return $item->mobile_number;
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
            'post_balance' => [
                'callback' => function ($item) {
                    return showAmount($item->post_balance);
                }
            ],
        ];
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function operator()
    {
        return $this->belongsTo(Operator::class);
    }
}
