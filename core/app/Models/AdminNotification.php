<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AdminNotification extends Model
{
    protected $casts = [
        'is_read' => 'integer'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
