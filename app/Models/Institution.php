<?php

namespace App\Models;

use App\Traits\GlobalStatus;
use Illuminate\Database\Eloquent\Model;

class Institution extends Model
{
    use GlobalStatus;

    protected $casts = [
        'status' => 'integer'
    ];

    public function exportColumns(): array
    {
        return  [
            'Name' => [
                'callback' => function ($item) {
                    return $item->name;
                }
            ],
            'category' => [
                'name' => "Category",
                'callback' => function ($item) {
                    return $item->category->name;
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
            'Configured' => [
                'callback' => function ($item) {
                    return $item->form ? 'Yes' : 'No';
                }
            ],
            'Status' => [
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

    public function category()
    {
        return $this->belongsTo(Category::class);
    }
}
