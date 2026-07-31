<?php

namespace App\Models;

use App\Traits\ApiQuery;
use Illuminate\Database\Eloquent\Model;

class NotificationLog extends Model
{
    use ApiQuery;

    protected $casts = [
        'status' => 'integer'
    ];

    public function exportColumns(): array
    {
        return  [
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
            'created_at' => [
                'name' =>  "Sent",
                'callback' => function ($item) {
                    return showDateTime($item->created_at, lang: 'en');
                }
            ],
            'sender',
            'subject'
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
