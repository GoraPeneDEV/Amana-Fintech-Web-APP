<?php

namespace App\Models;

use App\Constants\Status;
use App\Traits\GlobalStatus;
use Illuminate\Database\Eloquent\Model;

class Country extends Model
{
    use GlobalStatus;

    protected $casts = [
        'calling_codes' => 'array',
        'status'        => 'integer'
    ];

    public function scopeActive($query)
    {
        return $query->where('status', Status::ENABLE);
    }

    public function operators()
    {
        return $this->hasMany(Operator::class);
    }
    public function companies()
    {
        return $this->hasMany(Company::class);
    }

    public function products()
    {
        return $this->hasMany(GiftCard::class, 'country_id');
    }

    public function scopeAirtime($scope)
    {
        $scope->where('is_airtime', Status::YES);
    }

    public function scopeGiftCard($scope)
    {
        $scope->where('is_gift_card', Status::YES);
    }
    public function scopeUtilityBill($scope)
    {
        $scope->where('is_utility_bill', Status::YES);
    }
}
