<?php
namespace App\Models;

use App\Constants\Status;
use App\Traits\UserNotify;
use App\Traits\UserPartials;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable {
    use UserNotify, HasApiTokens, UserPartials;
    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password',
        'remember_token',
        'ver_code',
        'balance',
        'kyc_data',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at'           => 'datetime',
        'kyc_data'                    => 'object',
        'ver_code_send_at'            => 'datetime',
        'balance'                     => 'double',
        'status'                      => 'integer',
        'kv'                          => 'integer',
        'ev'                          => 'integer',
        'sv'                          => 'integer',
        'en'                          => 'integer',
        'sn'                          => 'integer',
        'pn'                          => 'integer',
        'is_allow_promotional_notify' => 'integer',
        'profile_complete'            => 'integer',
        'ts'                          => 'integer',
        'tv'                          => 'integer',
    ];

    protected $appends = ['image_src'];

    /**
     * specified column for export with column manipulation
     *
     * @var array
     */
    public function exportColumns(): array {
        return [
            'firstname',
            'lastname',
            'username',
            'email',
            'mobile',
            "country_name",
            "created_at" => [
                'name'     => "Joined At",
                'callback' => function ($item) {
                    return showDateTime($item->created_at, lang: 'en');
                },
            ],
            "balance"    => [
                'callback' => function ($item) {
                    return showAmount($item->balance);
                },
            ],
        ];
    }

    public function qrCode() {
        return $this->hasOne(QrCode::class, 'user_id');
    }

    public function loginLogs() {
        return $this->hasMany(UserLogin::class);
    }

    public function transactions() {
        return $this->hasMany(Transaction::class)->orderBy('id', 'desc');
    }

    public function deposits() {
        return $this->hasMany(Deposit::class)->where('status', '!=', Status::PAYMENT_INITIATE);
    }

    public function withdrawals() {
        return $this->hasMany(Withdrawal::class)->where('status', '!=', Status::PAYMENT_INITIATE);
    }
    public function investments() {
        return $this->hasMany(Investment::class)->orderBy('id', 'desc');
    }
    public function tickets() {
        return $this->hasMany(SupportTicket::class);
    }

    public function fullname(): Attribute {
        return new Attribute(
            get: fn() => $this->firstname . ' ' . $this->lastname,
        );
    }
    public function fullAddress(): Attribute {
        return new Attribute(
            get: fn() => $this->address . ', ' . $this->city . ', ' . $this->state . ', ' . $this->country_name
        );
    }
    public function imageSrc(): Attribute {
        return new Attribute(
            get: fn() => getImage(getFilePath('userProfile') . '/' . $this->image, getFilePath('userProfile'), isAvatar: true),
        );
    }

    public function fullNameShortForm(): Attribute {
        return new Attribute(
            get: fn() => strtoupper(substr($this->firstname, 0, 1)) . strtoupper(substr($this->lastname, 0, 1)),
        );
    }

    public function mobileNumber(): Attribute {
        return new Attribute(
            get: fn() => '+' . $this->dial_code . $this->mobile,
        );
    }
    // SCOPES
    public function scopeActive($query) {
        return $query->where('status', Status::USER_ACTIVE)->where('ev', Status::VERIFIED)->where('sv', Status::VERIFIED)->where('kv', Status::VERIFIED);
    }

    public function scopeBanned($query) {
        return $query->where('status', Status::USER_BAN);
    }
    public function scopeDeletedUser($query) {
        return $query->where('status', Status::USER_DELETE);
    }

    public function scopeEmailUnverified($query) {
        return $query->where('ev', Status::UNVERIFIED);
    }

    public function scopeMobileUnverified($query) {
        return $query->where('sv', Status::UNVERIFIED);
    }

    public function scopeKycUnverified($query) {
        return $query->where('kv', Status::KYC_UNVERIFIED);
    }

    public function scopeKycPending($query) {
        return $query->where('kv', Status::KYC_PENDING);
    }

    public function scopeEmailVerified($query) {
        return $query->where('ev', Status::VERIFIED);
    }

    public function scopeMobileVerified($query) {
        return $query->where('sv', Status::VERIFIED);
    }

    public function scopeWithBalance($query) {
        return $query->where('balance', '>', 0);
    }

    public function deviceTokens() {
        return $this->hasMany(DeviceToken::class);
    }
}
