<?php

namespace App\Models;

use App\Traits\GlobalStatus;
use Illuminate\Database\Eloquent\Model;

class Category extends Model
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
            'status' => [
                'name' => "Status",
                'callback' => function ($item) {
                    return $item->status ? 'Enable' : 'Disable';
                }
            ],
        ];
    }

    public function institute()
    {
        return $this->hasMany(Institution::class, 'category_id');
    }
}
