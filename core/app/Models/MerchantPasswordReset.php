<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MerchantPasswordReset extends Model
{
    protected $casts = [
        'status' => 'integer'
    ];

    protected $dates = ['created_at', 'updated_at'];
}
