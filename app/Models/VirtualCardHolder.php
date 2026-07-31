<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class VirtualCardHolder extends Model
{
    protected $casts = [
        'dob' => 'object'
    ];
}
