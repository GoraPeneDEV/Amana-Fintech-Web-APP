<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Constants\Status;
use App\Traits\GlobalStatus;

class GiftCard extends Model
{
    use GlobalStatus;

    protected $casts = [
        'fixed_recipient_denominations' => 'array',
        'fixed_sender_denominations'    => 'array',
        'fixed_recipient_to_sender_map' => 'object',
        'logo_urls'                     => 'array',
        'metadata'                      => 'array',
    ];

    public function scopeActive($query)
    {
        return $query->where('status', Status::ENABLE);
    }

    public function exportColumns(): array
    {
        return  [
            'product_name' => [
                'name' => "Name/Id",
                'callback' => function ($item) {
                    return $item->product_name . " / " . $item->product_id;
                }
            ],
            'brand_name' => [
                'name' =>  "Brand",
                'callback' => function ($item) {
                    return $item->brand_name;
                }
            ],
            'country_iso_name' => [
                'name' =>  "Country",
                'callback' => function ($item) {
                    return $item->country_iso_name;
                }
            ],
            'denomination_type' => [
                'name' =>  "Denomination Type",
                'callback' => function ($item) {
                    return $item->denomination_type;
                }
            ],
            'min_recipient_denomination' => [
                'name' =>  "Minimum Amount",
                'callback' => function ($item) {
                    if ($item->denomination_type == 'FIXED') {
                        return $item->fixed_sender_denominations;
                    }
                    return showAmount($item->min_recipient_denomination);
                }
            ],
            'max_recipient_denomination' => [
                'name' =>  "Maximum Amount",
                'callback' => function ($item) {
                    if ($item->denomination_type == 'FIXED') {
                        return $item->fixed_sender_denominations;
                    }
                    return showAmount($item->max_recipient_denomination);
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
