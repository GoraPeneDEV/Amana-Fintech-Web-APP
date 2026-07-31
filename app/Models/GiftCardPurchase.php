<?php

namespace App\Models;

use App\Constants\Status;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Casts\Attribute;

class GiftCardPurchase extends Model
{
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function giftCard()
    {
        return $this->belongsTo(GiftCard::class,'product_id', 'id');
    }

    public function apiProvider()
    {
        return $this->belongsTo(ApiProvider::class, 'api_provider_id');
    }

    public function scopePending($query)
    {
        return $query->where('status', Status::GIFT_CARD_PENDING);
    }

    public function statusBadge(): Attribute
    {
        return new Attribute(
            function () {
                $html = '';
                if ($this->status == Status::GIFT_CARD_PENDING) {
                    $html = '<span class="badge badge--warning">' . trans('Pending') . '</span>';
                } elseif ($this->status == Status::GIFT_CARD_SUCCESSFUL) {
                    $html = '<span class="badge badge--success">' . trans('Successful') . '</span>';
                } elseif ($this->status == Status::GIFT_CARD_REJECTED) {
                    $html = '<span class="badge badge--danger">' . trans('Rejected') . '</span>';
                }
                return $html;
            }
        );
    }

    public function exportColumns(): array
    {
        return  [
            'user_id' => [
                'name' =>  "User",
                'callback' => function ($item) {
                    return $item->user->username;
                }
            ],
            'trx',
            'created_at' => [
                'name' =>  "Time",
                'callback' => function ($item) {
                    return showDateTime($item->created_at, lang: 'en');
                }
            ],
            'product_id' => [
                'name' =>  "Product",
                'callback' => function ($item) {
                    return $item->giftCard->product_name;
                }
            ],
            'recipient_email' => [
                'name' =>  "Recipient",
                'callback' => function ($item) {
                    return $item->recipient_email;
                }
            ],
            'amount' => [
                'name' =>  "Amount",
                'callback' => function ($item) {
                    return (getAmount($item->amount) . ' ' . $item->giftCard->recipient_currency_code) . ' (' . getAmount($item->amount * $item->rate) . ' ' . $item->giftCard->sender_currency_code . ')';
                }
            ],
            'quantity' => [
                'name' =>  "Quantity",
                'callback' => function ($item) {
                    return getAmount($item->quantity);
                }
            ],
            'discount' => [
                'name' =>  "Discount",
                'callback' => function ($item) {
                    return showAmount($item->discount);
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
            ]
        ];
    }
}
