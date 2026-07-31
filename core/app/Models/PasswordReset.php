<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PasswordReset extends Model
{

    protected $hidden = [
        'token'
    ];

    protected $dates = ['created_at', 'updated_at'];
}
