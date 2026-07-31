<?php

namespace App\Models;

use App\Traits\GlobalStatus;
use Illuminate\Database\Eloquent\Model;

class WithdrawMethod extends Model
{
    use GlobalStatus;

    protected $casts = [
        'user_data'               => 'object',
        'min_limit'               => 'double',
        'max_limit'               => 'double',
        'fixed_charge'            => 'double',
        'rate'                    => 'double',
        'percent_charge'          => 'double',
        'merchant_fixed_charge'   => 'double',
        'merchant_percent_charge' => 'double',
        'merchant_min_limit'      => 'double',
        'merchant_max_limit'      => 'double',
        'status'                  => 'integer',
    ];

    public function form()
    {
        return $this->belongsTo(Form::class);
    }

    public function saveAccounts()
    {
        return $this->hasMany(WithdrawSaveAccount::class);
    }
}
