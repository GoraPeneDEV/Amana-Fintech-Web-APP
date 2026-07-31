<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class GeneralSetting extends Model
{
    protected $casts = [
        'mail_config'           => 'object',
        'sms_config'            => 'object',
        'global_shortcodes'     => 'object',
        'firebase_config'       => 'object',
        'quick_amounts'         => 'array',
        'supported_otp_type'    => 'array',
        "pusher_config"         => "object",
        "pusher_config"         => "object",
        'kv'                    => 'integer',
        'agent_kv'              => 'integer',
        'merchant_kv'           => 'integer',
        'ev'                    => 'integer',
        'en'                    => 'integer',
        'sv'                    => 'integer',
        'sn'                    => 'integer',
        'pn'                    => 'integer',
        'otp_verification'      => 'integer',
        'force_ssl'             => 'integer',
        'in_app_payment'        => 'integer',
        'maintenance_mode'      => 'integer',
        'secure_password'       => 'integer',
        'agree'                 => 'integer',
        'multi_language'        => 'integer',
        'registration'          => 'integer',
        'agent_registration'    => 'integer',
        'merchant_registration' => 'integer',
        'agent_verification'    => 'integer',
        'merchant_verification' => 'integer',
        'system_customized'     => 'integer',
        'currency_format'       => 'integer',
        'user_pin_digits'       => 'integer',
        'qrcode_login'          => 'integer'
    ];

    protected $hidden = ['email_template', 'mail_config', 'sms_config', 'system_info'];

    public function scopeSiteName($query, $pageTitle)
    {
        $pageTitle = empty($pageTitle) ? '' : ' - ' . $pageTitle;
        return $this->site_name . $pageTitle;
    }

    protected static function boot()
    {
        parent::boot();
        static::saved(function () {
            \Cache::forget('GeneralSetting');
        });
    }
}
