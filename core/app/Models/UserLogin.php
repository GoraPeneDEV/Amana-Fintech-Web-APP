<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UserLogin extends Model
{


    public function exportColumns(): array
    {
        return [
            'user_id' => [
                'name' => "User",
                'callback' => function ($item) {
                    if ($item->user_id != 0 && $item->user) {
                        return $item->user->username;
                    } elseif ($item->agent_id != 0 && $item->agent) {
                        return $item->agent->username;
                    } elseif ($item->merchant_id != 0 && $item->merchant) {
                        return $item->merchant->username;
                    }
                    return 'N/A';
                }
            ],
            'user_type' => [
                'name' => "User Type",
                'callback' => function ($item) {
                    if ($item->user_id != 0) {
                        return 'User';
                    } elseif ($item->agent_id != 0) {
                        return 'Agent';
                    } elseif ($item->merchant_id != 0) {
                        return 'Merchant';
                    }
                    return 'N/A';
                }
            ],
            'created_at' => [
                'name' => "Login At",
                'callback' => function ($item) {
                    return showDateTime($item->created_at, lang: 'en');
                }
            ],
            'user_ip' => [
                'name' => "IP"
            ],
            'location' => [
                'callback' => function ($item) {
                    return ($item->city ? $item->city . ", " : '') . ($item->country ?? '');
                }
            ],
            'browser',
            'os'
        ];
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function agent()
    {
        return $this->belongsTo(Agent::class);
    }

    public function merchant()
    {
        return $this->belongsTo(Merchant::class);
    }
}
