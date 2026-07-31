<?php

namespace App\Models;

use App\Constants\Status;
use App\Traits\GlobalStatus;
use Illuminate\Database\Eloquent\Model;

class BillCategory extends Model
{
    use GlobalStatus;
    
    protected $casts = [
        'status' => 'integer'
    ];

    protected $appends = [
        'formatted_name'
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

    public function company()
    {
        return $this->hasMany(Company::class, 'category_id');
    }

    public function scopeActive($query)
    {
        return $query->where('status', Status::ENABLE);
    }

    public function getFormattedNameAttribute()
    {
        return ucwords(strtolower(keyToTitle($this->name)));
    }
}
