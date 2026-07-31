<?php

namespace App\Traits;

use Carbon\Carbon;


trait UserPartials
{

    public function trxLimit($type)
    {

        $transactions = auth()->user()->transactions()->where('remark', $type)->selectRaw("SUM(amount) as totalAmount");
        return [
            'daily'   => $transactions->whereDate('transactions.created_at', Carbon::now())->get()->sum('totalAmount'),
            'monthly' => $transactions->whereMonth('transactions.created_at', Carbon::now())->whereYear('transactions.created_at', Carbon::now())->get()->sum('totalAmount'),
        ];
    }
}
