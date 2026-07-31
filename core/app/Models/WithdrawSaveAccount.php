<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class WithdrawSaveAccount extends Model
{
    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'data' => 'object',
    ];

    public function agent()
    {
        return $this->belongsTo(Agent::class);
    }

    public function merchant()
    {
        return $this->belongsTo(Merchant::class);
    }

    public function withdrawMethod()
    {
        return $this->belongsTo(WithdrawMethod::class);
    }
}
