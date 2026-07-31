<?php

namespace App\Models;

use App\Constants\Status;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ModuleSetting extends Model
{
    use HasFactory;

    protected $casts = [
        'status' => 'integer'
    ];

    public function scopeActive($query)
    {
        return $query->where('status', Status::ENABLE);
    }
}
