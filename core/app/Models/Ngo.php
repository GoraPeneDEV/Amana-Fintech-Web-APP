<?php

namespace App\Models;

use App\Traits\GlobalStatus;
use Illuminate\Database\Eloquent\Model;

class Ngo extends Model
{
    use GlobalStatus;

    protected $casts = [
        'fixed_charge'   => 'double',
        'percent_charge' => 'double',
        'status'         => 'integer',
    ];

    public function exportColumns(): array
    {
        return  [
            'name' => [
                'name' =>  "Name",
                'callback' => function ($item) {
                    return $item->name;
                }
            ],
            'fixed_charge' => [
                'name'     => "Fixed Charge",
                'callback' => function ($item) {
                    return showAmount($item->fixed_charge);
                }
            ],
            'percent_charge' => [
                'name'     => "Percent Charge",
                'callback' => function ($item) {
                    return getAmount($item->percent_charge);
                }
            ],
            'configured' => [
                'name' =>  "Configured",
                'callback' => function ($item) {
                    return $item->form ? 'Yes' : 'No';
                }
            ],
            'status' => [
                'name' =>  "Status",
                'callback' => function ($item) {
                    return $item->status ? 'Enable' : 'Disable';
                }
            ],

        ];
    }

    public function form()
    {
        return $this->belongsTo(Form::class);
    }
}
