<?php

namespace App\Models;

use App\Traits\GlobalStatus;
use Illuminate\Database\Eloquent\Model;

class Bank extends Model
{
    use GlobalStatus;

    protected $casts = [
        'status' => 'integer'
    ];

    public function exportColumns(): array
    {
        return  [
            'name' => [
                'name' => "Name",
                'callback' => function ($item) {
                    return $item->name;
                }
            ],
            'configured' => [
                'name' => "Configured",
                'callback' => function ($item) {
                    return $item->form ? 'Yes' : 'No';
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

    public function form()
    {
        return $this->belongsTo(Form::class);
    }

    public function userBanks()
    {
        return $this->hasMany(UserBank::class);
    }
}
