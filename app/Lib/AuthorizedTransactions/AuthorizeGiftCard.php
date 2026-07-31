<?php

namespace App\Lib\AuthorizedTransactions;

use App\Constants\Status;
use App\Lib\GiftCardProvider\ReloadLy;
use App\Models\ApiProvider;
use App\Models\GiftCard;
use App\Models\GiftCardPurchase;
use App\Models\Transaction;
use Exception;

class AuthorizeGiftCard
{
    public function store($userAction)
    {
        $user       = auth()->user();
        $details    = $userAction->details;

        $productId = $details->product_id;
        $quantity  = $details->quantity;
        $amount    = $details->amount;

        $product = GiftCard::active()->where('id', $productId)->first();

        if (!$product) {
            $notify[] = 'The Gift card is not found';
            return apiResponse("not_found", "error", $notify);
        }

        $exchangeRate = $product->recipient_to_sender_exchange_rate;
        $fee          = $product->sender_fee;

        $unitPrice = $exchangeRate * $amount;
        $subtotal  = $unitPrice * $quantity;
        $totalFee  = $fee * $quantity;
        $total     = $totalFee + $subtotal;

        if ($total  > $user->balance) {
            $notify[] = 'Insufficient Balance';
            return apiResponse("insufficient_balance", "error", $notify);
        }

        $giftCardDetails['product_id'] = $product->product_id;
        $giftCardDetails['email']      = $details->email;
        $giftCardDetails['name']       = $details->name;
        $giftCardDetails['quantity']   = $quantity;
        $giftCardDetails['amount']     = $amount;


        $provider = ApiProvider::whereJsonContains('module', 'gift_card')->where('status->gift_card', Status::YES)->first();

        if (!$provider) {
            $notify[] = 'Sorry! Gift card provider not found';
            return apiResponse("not_found", "error", $notify);
        }

        try {


            $reloadly    = new ReloadLy();
            $apiResponse = $reloadly->sendGiftCard($giftCardDetails);
            $totalFee       = $apiResponse['total_fee'] ?? 0;
            $discount       = $apiResponse['discount'] ?? 0;
            $totalAmount    = $apiResponse['total_amount'];
            $billStatus     = $apiResponse['bill_status'];
            $apiProviderTrx = $apiResponse['api_provider_trx'];

            if (!@$apiResponse['processing_status']) {
                $notify[] = @$apiResponse['message'] ?? "Something went wrong";
                return apiResponse("something_wrong", "error", $notify);
            }
        } catch (Exception $ex) {
            $notify[] = $ex->getMessage() ?? "The gift card purchase processing failed. Please try again later.";
            return apiResponse("something_wrong", "error", $notify);
        }

        $giftCardPurchase                   = new GiftCardPurchase();
        $giftCardPurchase->user_id          = $user->id;
        $giftCardPurchase->product_id       = $product->id;
        $giftCardPurchase->amount           = $details->amount;
        $giftCardPurchase->charge           = $totalFee;
        $giftCardPurchase->discount         = $discount;
        $giftCardPurchase->rate             = $exchangeRate;
        $giftCardPurchase->unit_price       = $unitPrice;
        $giftCardPurchase->sub_total        = $subtotal;
        $giftCardPurchase->quantity         = $details->quantity;
        $giftCardPurchase->total            = $totalAmount;
        $giftCardPurchase->trx              = generateUniqueTrxNumber();
        $giftCardPurchase->recipient_email  = $details->email;
        $giftCardPurchase->status           = $billStatus;
        $giftCardPurchase->api_provider_id  = @$provider->id ?? 0;
        $giftCardPurchase->api_provider_trx = $apiProviderTrx;
        $giftCardPurchase->cron_ordering    = time();
        $giftCardPurchase->save();

        $user->balance -= $totalAmount;
        $user->save();

        $transaction               = new Transaction();
        $transaction->user_id      = $user->id;
        $transaction->amount       = $totalAmount;
        $transaction->post_balance = $user->balance;
        $transaction->charge       = $totalFee;
        $transaction->trx_type     = '-';
        $transaction->remark       = 'gift_card_purchase';
        $transaction->details      = 'Gift card purchase';
        $transaction->trx          = $giftCardPurchase->trx;
        $transaction->save();

        notify($user, 'GIFT_CARD_PURCHASE_COMPLETED', [
            'user'         => $user->fullname,
            'amount'       => showAmount($totalAmount, currencyFormat: false),
            'charge'       => showAmount($totalFee, currencyFormat: false),
            'product'      => $giftCardPurchase->giftCard->product_name,
            'trx'          => $giftCardPurchase->trx,
            'time'         => showDateTime($giftCardPurchase->created_at),
            'quantity'     => $details->quantity,
            'email'        => $details->email,
            'post_balance' => showAmount($user->balance, currencyFormat: false),
        ]);

        $notify[] = 'Gift card purchase completed successfully';

        return apiResponse("gift_card_purchase_success", "success", $notify, [
            'redirect_type'      => 'new_url',
            'redirect_url'       => route('user.gift.card.history'),
            'gift_card_purchase' => $giftCardPurchase,
            'transaction'        => $transaction
        ]);
    }
}
