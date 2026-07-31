<?php

namespace App\Models;

use App\Traits\ApiQuery;
use App\Traits\GlobalStatus;
use Illuminate\Database\Eloquent\Model;

class Banner extends Model
{
    use GlobalStatus, ApiQuery;

    protected $casts = [
        'status' => 'integer'
    ];

    public function exportColumns(): array
    {
        return  [
            'name' => [
                'name' => "Link",
                'callback' => function ($item) {
                    return $item->link;
                }
            ],
            'fixed_charge' => [
                'name' => "Fixed Charge",
                'callback' => function ($item) {
                    return showAmount($item->fixed_charge);
                }
            ],
            'percent_charge' => [
                'name' => "Percent Charge",
                'callback' => function ($item) {
                    return getAmount($item->percent_charge);
                }
            ],
            'status' => [
                'name' => "Status",
                'callback' => function ($item) {
                    return $item->status ? 'Enable' : 'Disable';
                }
            ],
        ];
    }
}
