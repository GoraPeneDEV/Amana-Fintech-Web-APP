<?php

namespace App\Models;

use App\Traits\GlobalStatus;
use Illuminate\Database\Eloquent\Model;

class InvestPlan extends Model
{
    use GlobalStatus;

    public function exportColumns(): array
    {
        return [
            'name'            => [
                'name'     => "Name",
                'callback' => function ($item) {
                    return $item->name;
                },
            ],
            'fixed_amount'    => [
                'name'     => "Fixed Amount",
                'callback' => function ($item) {
                    return $item->fixed_amount;
                },
            ],
            'min_invest'      => [
                'name'     => "Min Amount",
                'callback' => function ($item) {
                    return $item->min_invest;
                },
            ],
            'max_invest'      => [
                'name'     => "Max Amount",
                'callback' => function ($item) {
                    return $item->max_invest;
                },
            ],
            'interest_amount' => [
                'name'     => "Interest",
                'callback' => function ($item) {
                    return $item->interest_amount;
                },
            ],
            'interest_type'   => [
                'name'     => "Post Balance",
                'callback' => function ($item) {
                    return $item->interest_type ? 'Percent' : 'Fixed';
                },
            ],
            'time_id'         => [
                'name'     => "Time",
                'callback' => function ($item) {
                    return $item->investTime->time;
                },
            ],
            'status'          => [
                'name'     => "Status",
                'callback' => function ($item) {
                    return $item->status ? 'Enable' : 'Disable';
                },
            ],

        ];
    }

    public function investTime()
    {
        return $this->belongsTo(InvestTime::class, 'time_id');
    }
}
