<?php

namespace App\Models;

use App\Constants\Status;
use App\Traits\ApiQuery;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;

class MoneyRequest extends Model
{
    use ApiQuery;

    protected $casts = [
        'amount' => 'double',
        'charge' => 'double',
        'status' => 'integer',
    ];

    public function exportColumns(): array
    {
        return  [
            'sender_id' => [
                'name' => "Sender",
                'callback' => function ($item) {
                    if ($item->sender_id != 0 && $item->requestSender) {
                        return $item->requestSender->username;
                    } else {
                        return 'N/A';
                    }
                }
            ],
            'receiver_id' => [
                'name' => "Receiver",
                'callback' => function ($item) {
                    if ($item->sender_id != 0 && $item->requestReceiver) {
                        return $item->requestReceiver->username;
                    } else {
                        return 'N/A';
                    }
                }
            ],
            'trx',
            'created_at' => [
                'name' =>  "Transacted",
                'callback' => function ($item) {
                    return showDateTime($item->created_at, lang: 'en');
                }
            ],
            'amount' => [
                'name' => "Amount",
                'callback' => function ($item) {
                    return showAmount($item->amount);
                }
            ],
            'charge' => [
                'name' => "Charge",
                'callback' => function ($item) {
                    return showAmount($item->charge);
                }
            ],
            'note' => [
                'name' => "Note",
                'callback' => function ($item) {
                    return $item->note;
                }
            ],
        ];
    }


    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function requestSender()
    {
        return $this->belongsTo(User::class, 'sender_id');
    }

    public function requestReceiver()
    {
        return $this->belongsTo(User::class, 'receiver_id');
    }


    public function requestMoneyStatus(): Attribute
    {
        return new Attribute(get: fn() => $this->status());
    }

    public function status(): string
    {
        return match ($this->status) {
            Status::PENDING  => '<span class="badge--warning badge">' . trans('Pending') . '</span>',
            Status::REJECTED => '<span class="badge--danger badge">' . trans('Rejected') . '</span>',
            Status::APPROVED => '<span class="badge--success badge">' . trans('Accepted') . '</span>',
            default => '',
        };
    }
}
