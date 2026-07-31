<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AgentPasswordReset extends Model
{
    protected $dates = ['created_at', 'updated_at'];

    protected $casts = [
        'status'                      => 'integer'
    ];
}
