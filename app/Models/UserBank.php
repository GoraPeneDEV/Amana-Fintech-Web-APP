<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserBank extends Model
{
    use HasFactory;

    protected $casts = [
        'user_data' => 'object',
    ];

    public function bank(){
        return $this->belongsTo(Bank::class);
    }
}
