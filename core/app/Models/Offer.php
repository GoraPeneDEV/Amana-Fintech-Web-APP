<?php

namespace App\Models;

use App\Constants\Status;
use App\Traits\ApiQuery;
use App\Traits\GlobalStatus;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Casts\Attribute;

class Offer extends Model
{

    use GlobalStatus, ApiQuery;

    protected $casts = [
        'discount_type'    => 'integer',
        'amount'           => 'double',
        'min_payment'      => 'double',
        'maximum_discount' => 'double',
        'status'           => 'integer',
    ];

    public function imageSrc(): Attribute
    {
        return new Attribute(
            get: fn() => getImage(getFilePath('offer') . '/' . $this->image, getFileSize('offer'))
        );
    }

    public function merchant()
    {
        return $this->belongsTo(Merchant::class);
    }

    public static function scopeActive($query)
    {
        return $query->whereDate('start_date', '<=', today())
            ->whereDate('end_date', '>=', today())
            ->where('status', Status::ENABLE);
    }

    public function getOfferTypeAttribute()
    {
        if ($this->discount_type == 1) {
            return 'Fixed';
        } else {
            return 'Percentage';
        }
    }
}
