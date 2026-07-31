<?php

namespace App\Models;

use App\Constants\Status;
use App\Traits\ApiQuery;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;

class UtilityBill extends Model
{
    use ApiQuery;

    protected $appends = ['status_badge'];

    protected $casts = [
        'user_data'       => 'object',
        'amount'          => 'double',
        'charge'          => 'double',
        'total'           => 'double',
        'status'          => 'integer',
        'user_data'       => 'object',
        'api_provider_id' => 'integer',
        'user_id'         => 'integer',
        'company_id'      => 'integer',
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
            'utility' => [
                'name' =>  "Utility",
                'callback' => function ($item) {
                    return $item->company->name;
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

    public function company()
    {
        return $this->belongsTo(Company::class, 'company_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function getTrx()
    {
        return $this->belongsTo(Transaction::class, 'trx', 'trx');
    }

    public function apiProvider()
    {
        return $this->belongsTo(ApiProvider::class, 'api_provider_id');
    }
    public function scopePending($query)
    {
        return $query->where('status', Status::UTILITY_BILL_PENDING);
    }

    public function scopeProcessing($query)
    {
        return $query->where('status', Status::UTILITY_BILL_PROCESSING);
    }

    public function scopeApproved($query)
    {
        return $query->where('status', Status::UTILITY_BILL_SUCCESSFUL);
    }

    public function scopeRefunded($query)
    {
        return $query->where('status', Status::UTILITY_BILL_REFUNDED);
    }

    public function statusBadge(): Attribute
    {
        return new Attribute(
            function () {
                $html = '';
                if ($this->status == Status::UTILITY_BILL_PENDING) {
                    $html = '<span class="badge badge--warning">' . trans('Pending') . '</span>';
                } elseif ($this->status == Status::UTILITY_BILL_PROCESSING) {
                    $html = '<span class="badge badge--info">' . trans('Processing') . '</span>';
                } elseif ($this->status == Status::UTILITY_BILL_SUCCESSFUL) {
                    $html = '<span class="badge badge--success">' . trans('Successful') . '</span>';
                } elseif ($this->status == Status::UTILITY_BILL_REFUNDED) {
                    $html = '<span class="badge badge--danger">' . trans('Refunded') . '</span>';
                }
                return $html;
            }
        );
    }
}
