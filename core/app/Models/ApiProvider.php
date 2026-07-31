<?php

namespace App\Models;

use App\Constants\Status;
use Illuminate\Database\Eloquent\Model;

class ApiProvider extends Model
{
    protected $casts = [
        'config'       => 'object',
        'module'       => 'array',
        'status'       => 'integer',
        'access_token' => 'object',
        'status'       => 'object'
    ];

    public function scopeActive($query)
    {
        return $query->where('status', Status::ENABLE);
    }
}


