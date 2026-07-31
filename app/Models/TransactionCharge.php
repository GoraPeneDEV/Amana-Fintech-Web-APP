<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TransactionCharge extends Model
{
    protected $casts = [
        'fixed_charge'                 => 'double',
        'percent_charge'               => 'double',
        'min_limit'                    => 'double',
        'max_limit'                    => 'double',
        'agent_commission_fixed'       => 'double',
        'agent_commission_percent'     => 'double',
        'merchant_fixed_charge'        => 'double',
        'merchant_percent_charge'      => 'double',
        'monthly_limit'                => 'double',
        'daily_limit'                  => 'double',
        'daily_request_accept_limit'   => 'double',
        'monthly_request_accept_limit' => 'double',
        'cap'                          => 'double',
        'maximum_card_generate'        => 'integer'
    ];
}
