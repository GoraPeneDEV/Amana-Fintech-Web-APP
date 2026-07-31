<?php

namespace App\Models;

use App\Constants\Status;
use App\Traits\AdminNotify;
use App\Traits\GlobalStatus;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Spatie\Permission\Traits\HasRoles;

class Admin extends Authenticatable
{
    use GlobalStatus, AdminNotify, HasRoles;
    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */

    protected $casts = [
        'status' => 'integer',
        'ev' => 'integer',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    public function imageSrc(): Attribute
    {
        return new Attribute(
            get: fn() => $this->image  ? getImage(getFilePath('adminProfile') . '/' . $this->image, getFileSize('adminProfile')) : siteFavicon(),
        );
    }

    public function scopeActive($query)
    {
        return $query->where('status', Status::USER_ACTIVE)->where('ev', Status::VERIFIED);
    }

    public function scopeBanned($query)
    {
        return $query->where('status', Status::USER_BAN);
    }

    public function scopeEmailUnverified($query)
    {
        return $query->where('ev', Status::UNVERIFIED);
    }
}
