<?php

namespace App\Models;

use App\Constants\Status;
use App\Traits\AgentNotify;
use App\Traits\UserPartials;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Laravel\Sanctum\HasApiTokens;

class Agent extends Authenticatable
{
    use AgentNotify, HasApiTokens, UserPartials;

    protected $hidden = [
        'password',
        'remember_token',
        'ver_code',
        'balance',
        'kyc_data'
    ];

    protected $casts = [
        'email_verified_at'           => 'datetime',
        'kyc_data'                    => 'object',
        'ver_code_send_at'            => 'datetime',
        'balance'                     => 'double',
        'status'                      => 'integer',
        'kv'                          => 'integer',
        'ev'                          => 'integer',
        'sv'                          => 'integer',
        'profile_complete'            => 'integer',
        'ts'                          => 'integer',
        'tv'                          => 'integer',
        'en'                          => 'integer',
        'sn'                          => 'integer',
        'pn'                          => 'integer',
        'is_allow_promotional_notify' => 'integer'
    ];

    protected $appends = ['image_src'];

    public function exportColumns(): array
    {
        return  [
            'firstname',
            'lastname',
            'username',
            'email',
            'mobile',
            "country_name",
            "created_at" => [
                'name' => "Joined At",
                'callback' => function ($item) {
                    return showDateTime($item->created_at, lang: 'en');
                }
            ],
            "balance" => [
                'callback' => function ($item) {
                    return showAmount($item->balance);
                }
            ]
        ];
    }

    public function mobileNumber(): Attribute
    {
        return new Attribute(
            get: fn() => '+' . $this->dial_code . $this->mobile,
        );
    }

    public function fullAddress(): Attribute
    {
        return new Attribute(
            get: fn() => $this->address . ', ' . $this->city . ', ' . $this->state . ', ' . $this->country_name
        );
    }

    public function getFullnameAttribute()
    {
        return $this->firstname . ' ' . $this->lastname;
    }

    public function qrCode()
    {
        return $this->hasOne(QrCode::class, 'agent_id');
    }

    public function imageSrc(): Attribute
    {
        return new Attribute(
            get: fn() => getImage(getFilePath('agentProfile') . '/' . $this->image, getFilePath('agentProfile'), isAvatar: true),
        );
    }

    public function fullNameShortForm(): Attribute
    {
        return new Attribute(
            get: fn() => strtoupper(substr($this->firstname, 0, 1)) . strtoupper(substr($this->lastname, 0, 1)),
        );
    }

    public function loginLogs()
    {
        return $this->hasMany(UserLogin::class, 'agent_id');
    }

    public function transactions()
    {
        return $this->hasMany(Transaction::class, 'agent_id')->orderBy('id', 'desc');
    }

    public function deposits()
    {
        return $this->hasMany(Deposit::class, 'agent_id')->where('status', '!=', Status::PAYMENT_INITIATE);
    }

    public function withdrawals()
    {
        return $this->hasMany(Withdrawal::class, 'agent_id')->where('status', '!=', Status::PAYMENT_INITIATE);
    }

    public function tickets()
    {
        return $this->hasMany(SupportTicket::class, 'agent_id');
    }

    public function deviceTokens()
    {
        return $this->hasMany(DeviceToken::class, 'agent_id');
    }

    public function scopeActive($query)
    {
        return $query->where('status', Status::USER_ACTIVE)->where('ev', Status::VERIFIED)->where('sv', Status::VERIFIED)->where('kv', Status::VERIFIED);
    }


    public function scopeBanned($query)
    {
        return $query->where('status', Status::USER_BAN);
    }

    public function scopeDeletedAgent($query)
    {
        return $query->where('status', Status::USER_DELETE);
    }

    public function scopeEmailUnverified($query)
    {
        return $query->where('ev', Status::UNVERIFIED);
    }

    public function scopeMobileUnverified($query)
    {
        return $query->where('sv', Status::UNVERIFIED);
    }

    public function scopeEmailVerified($query)
    {
        return $query->where('ev', Status::VERIFIED);
    }

    public function scopeSmsVerified($query)
    {
        return $query->where('sv', Status::VERIFIED);
    }

    public function scopeKycUnverified($query)
    {
        return $query->where('kv', Status::KYC_UNVERIFIED);
    }

    public function scopeKycPending($query)
    {
        return $query->where('kv', Status::KYC_PENDING);
    }

    public function scopeWithBalance($query)
    {
        return $query->where('balance', '>', 0);
    }
}
