<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class InvestmentInterest extends Model
{
    public function exportColumns(): array
    {
        return [
            'trx'            => [
                'name'     => "Trx",
                'callback' => function ($item) {
                    return $item->trx;
                },
            ],
            'created_at'     => [
                'name'     => "Transacted",
                'callback' => function ($item) {
                    return showDateTime($item->created_at, lang: 'en');
                },
            ],
            'amount'         => [
                'name'     => "Amount",
                'callback' => function ($item) {
                    return $item->invest_amount;
                },
            ],
            'total_interest' => [
                'name'     => "Post Balance",
                'callback' => function ($item) {
                    return $item->transactions->post_balance;
                },
            ],
            'details' => [
                'name'     => "Details",
                'callback' => function ($item) {
                    return $item->transactions->details;
                },
            ],

        ];
    }
    public function transactions()
    {
        return $this->belongsTo(Transaction::class, 'trx', 'trx');
    }
    public function investment()
    {
        return $this->belongsTo(Investment::class, 'investment_id');
    }
}
