<?php

namespace App\Models;

use App\Constants\Status;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;

class VirtualCard extends Model
{
    protected $casts = [
        'address'                   => 'object',
        'card_holder_original_data' => 'object',
        'spending_limit'            => 'object',
        'balance'                   => 'double',
        'status'                    => 'integer',
        'usability_type'            => 'integer'
    ];

    protected $append = [
        'brand_image_src'
    ];

    public function cardHolder()
    {
        return $this->belongsTo(VirtualCardHolder::class, 'cardholder_id');
    }
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function brandImageSrc(): Attribute
    {
        return new Attribute(
            get: fn() => getImage(activeTemplate(true) . "images/brand/" . strtolower($this->brand) . ".png")
        );
    }

    public function statusBadge(): Attribute
    {
        return new Attribute(
            get: fn() => $this->badgeData()
        );
    }

    public function badgeData()
    {
        $html = '';
        if ($this->status == Status::VIRTUAL_CARD_ACTIVE) {
            $html = '<span class="badge   badge--success">' . trans('Active') . '</span>';
        } else if ($this->status == Status::VIRTUAL_CARD_INACTIVE) {
            $html = '<span class="badge   badge--warning">' . trans('Inactive') . '</span>';
        } else {
            $html = '<span class="badge  badge--danger">' . trans('Closed') . '</span>';
        }
        return $html;
    }
}
