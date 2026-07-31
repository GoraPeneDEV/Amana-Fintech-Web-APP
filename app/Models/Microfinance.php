<?php

namespace App\Models;

use App\Constants\Status;
use App\Traits\ApiQuery;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Casts\Attribute;

class Microfinance extends Model
{
    use ApiQuery;

    protected $appends = ['status_badge'];

    protected $casts = [
        'user_data' => 'object',
        'amount'    => 'double',
        'charge'    => 'double',
        'total'     => 'double',
        'status'    => 'integer'
    ];

    public function exportColumns(): array
    {
        return  [
            'user_id' => [
                'name' => "User",
                'callback' => function ($item) {
                    if ($item->user_id != 0 && $item->user) {
                        return $item->user->username;
                    }
                    return 'N/A';
                }
            ],
            'trx',
            'created_at' => [
                'name' =>  "Transacted",
                'callback' => function ($item) {
                    return showDateTime($item->created_at, lang: 'en');
                }
            ],
            'organization' => [
                'name' =>  "Organization",
                'callback' => function ($item) {
                    return $item->ngo->name;
                }
            ],
            'amount' => [
                'name' =>  "Amount",
                'callback' => function ($item) {
                    return showAmount($item->amount);
                }
            ],
            'charge' => [
                'name' =>  "Charge",
                'callback' => function ($item) {
                    return showAmount($item->charge);
                }
            ],
            'total' => [
                'name' =>  "Total",
                'callback' => function ($item) {
                    return showAmount($item->total);
                }
            ],
            'status' => [
                'name' =>  "Status",
                "callback" => function ($item) {
                    return strip_tags($item->statusBadge);
                }
            ],
        ];
    }

    public function ngo()
    {
        return $this->belongsTo(Ngo::class, 'ngo_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function getTrx()
    {
        return $this->belongsTo(Transaction::class, 'trx', 'trx');
    }

    public function scopePending($query)
    {
        return $query->where('status', Status::PENDING);
    }

    public function scopeApproved($query)
    {
        return $query->where('status', Status::APPROVED);
    }

    public function scopeRejected($query)
    {
        return $query->where('status', Status::REJECTED);
    }

    public function statusBadge(): Attribute
    {
        return new Attribute(
            function () {
                $html = '';
                if ($this->status == Status::PENDING) {
                    $html = '<span class="badge badge--warning">' . trans('Pending') . '</span>';
                } elseif ($this->status == Status::APPROVED) {
                    $html = '<span class="badge badge--success">' . trans('Approved') . '</span>';
                } elseif ($this->status == Status::REJECTED) {
                    $html = '<span class="badge badge--danger">' . trans('Rejected') . '</span>';
                }
                return $html;
            }
        );
    }
}
