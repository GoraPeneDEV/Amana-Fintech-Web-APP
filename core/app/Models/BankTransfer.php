<?php

namespace App\Models;

use App\Constants\Status;
use App\Traits\ApiQuery;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BankTransfer extends Model
{
    use HasFactory, ApiQuery;

    protected $appends = ['status_badge'];

    protected $casts = [
        'user_data' => 'object',
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
            'bank' => [
                'name' =>  "Bank",
                'callback' => function ($item) {
                    return $item->bank->name;
                }
            ],
            'account_holder' => [
                'name' => "Account Holder",
                'callback' => function ($item) {
                    return $item->account_holder;
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

    public function bank()
    {
        return $this->belongsTo(Bank::class, 'bank_id', 'id');
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
