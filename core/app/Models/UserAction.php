<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UserAction extends Model
{
    protected $casts = [
        'details'  => 'object',
        'is_used'  => 'integer',
        'platform' => 'integer',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
    public function agent()
    {
        return $this->belongsTo(Agent::class, 'agent_id');
    }
    public function merchant()
    {
        return $this->belongsTo(Merchant::class, 'merchant_id');
    }

    public function scopeForAgent($query)
    {
        return $query->where('agent_id', agent()->id);
    }
}
