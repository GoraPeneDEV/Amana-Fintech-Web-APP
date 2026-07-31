<?php

namespace App\Models;

use App\Traits\ApiQuery;
use Illuminate\Database\Eloquent\Model;

class Donation extends Model
{
    use ApiQuery;

    protected $casts = [
        'amount'        => 'double',
        'post_balance'  => 'double',
        'hide_identity' => 'integer',
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
            'charity' => [
                'name' => "Charity",
                'callback' => function ($item) {
                    return $item->donationFor->name;
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
                'name' => "Post Balance",
                'callback' => function ($item) {
                    return showAmount($item->post_balance);
                }
            ],
            'reference' => [
                'name' => "Reference",
                'callback' => function ($item) {
                    return $item->reference;
                }
            ],
        ];
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function donationFor()
    {
        return $this->belongsTo(Charity::class, 'charity_id', 'id');
    }
}
