<?php

namespace App\Models;

use App\Traits\GlobalStatus;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;

class OperatingCountry extends Model
{
    use GlobalStatus;

    protected $casts = [
        'status' => 'integer'
    ];

    protected $appends = ['image_src'];
    public function imageSrc(): Attribute
    {
        return new Attribute(
            get: fn() => asset('assets/images/country/' . strtolower($this->code) . '.svg'),
        );
    }
}
