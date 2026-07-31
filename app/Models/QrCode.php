<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;

class QrCode extends Model
{
    protected $casts = [
        'for_user_login'     => 'integer',
        'for_agent_login'    => 'integer',
        'for_merchant_login' => 'integer',
        'is_used'            => 'integer',
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

    public function getUser(): Attribute
    {
        return new Attribute(
            get: function () {
                if ($this->user_id) {
                    $user = $this->user;
                } elseif ($this->agent_id) {
                    $user = $this->agent;
                } elseif ($this->merchant_id) {
                    $user = $this->merchant;
                } else {
                    $user = null;
                }
                return $user;
            },
        );
    }

    public function getUserType()
    {
        if ($this->user_id) {
            return 'USER';
        } elseif ($this->agent_id) {
            return 'AGENT';
        } elseif ($this->merchant_id) {
            return 'MERCHANT';
        }

        return null;
    }
}
