<?php

namespace App\Models;

use App\Constants\Status;
use App\Traits\GlobalStatus;
use Illuminate\Database\Eloquent\Model;

class GiftCardCategory extends Model
{
    use GlobalStatus;

    public function scopeActive($query)
    {
        return $query->where('status', Status::ENABLE);
    }

    public function products()
    {
        return $this->hasMany(GiftCard::class, 'category_id', 'unique_id');
    }

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
}
