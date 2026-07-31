<?php

namespace App\Lib\AuthorizedTransactions;

use App\Constants\Status;
use App\Models\MakePayment;
use App\Models\Merchant;
use App\Models\Offer;
use App\Models\Transaction;

class AuthorizeMakePayment
{
    public function store($userAction)
    {
        $user = auth()->user();

        $details = $userAction->details;

        $merchant = Merchant::active()->where('id', $details->merchant_id)->first();
        if (!$merchant) {
            $notify[] = 'Sorry! Merchant not found';
            return apiResponse("merchant_not_found", "error", $notify);
        }
        if (@$details->amount > $user->balance) {
            $notify[] = 'Sorry! Insufficient balance';
            return apiResponse("insufficient_balance", "error", $notify);
        }

        $userAction->is_used = Status::YES;
        $userAction->save();

        $user->balance -= $details->amount;
        $user->save();

        $senderTrx                = new Transaction();
        $senderTrx->user_id       = $user->id;
        $senderTrx->amount        = $details->amount;
        $senderTrx->post_balance  = $user->balance;
        $senderTrx->charge        = 0;
        $senderTrx->trx_type      = '-';
        $senderTrx->remark        = 'make_payment';
        $senderTrx->details       = 'Payment complete to ' . $merchant->fullname;
        $senderTrx->trx           = generateUniqueTrxNumber();
        $senderTrx->save();

        $afterCharge = ($details->amount - $details->total_charge);

        $merchant->balance += $afterCharge;
        $merchant->save();

        $merchantTrx               = new Transaction();
        $merchantTrx->merchant_id  = $merchant->id;
        $merchantTrx->amount       = $afterCharge;
        $merchantTrx->post_balance = $merchant->balance;
        $merchantTrx->charge       = $details->total_charge;
        $merchantTrx->trx_type     = '+';
        $merchantTrx->remark       = 'receive_payment';
        $merchantTrx->details      = 'Payment received from ' . $user->fullname;
        $merchantTrx->trx          = $senderTrx->trx;
        $merchantTrx->save();

        $makePayment                        = new MakePayment();
        $makePayment->user_id               = $user->id;
        $makePayment->merchant_id           = $merchant->id;
        $makePayment->amount                = $details->amount;
        $makePayment->charge                = $details->total_charge;
        $makePayment->merchant_amount       = $afterCharge;
        $makePayment->user_post_balance     = $user->balance;
        $makePayment->merchant_post_balance = $merchant->balance;
        $makePayment->user_details          = 'Payment complete to ' . $merchant->fullname;
        $makePayment->merchant_details      = 'Payment successful from ' . $user->fullname;
        $makePayment->trx                   = $senderTrx->trx;
        $makePayment->save();

        $offer    = Offer::active()->where('merchant_id', $merchant->id)->first();

        if ($offer) {

            if ($offer->min_payment <= $details->amount) {
                $offerAmount = 0;

                if ($offer->discount_type == Status::DISCOUNT_FIXED) {
                    $offerAmount = $offer->amount;
                } elseif ($offer->discount_type == Status::DISCOUNT_PERCENT) {
                    $offerAmount = ($details->amount * $offer->amount) / 100;

                    if ($offer->maximum_discount > 0 && $offerAmount > $offer->maximum_discount) {
                        $offerAmount = $offer->maximum_discount;
                    }
                }

                if ($offerAmount > 0) {

                    $user->balance += $offerAmount;
                    $user->save();

                    $transaction               = new Transaction();
                    $transaction->user_id      = $user->id;
                    $transaction->amount       = $offerAmount;
                    $transaction->post_balance = $user->balance;
                    $transaction->charge       = 0;
                    $transaction->trx_type     = '+';
                    $transaction->remark       = 'cashback';
                    $transaction->details      = 'Cashback for make payment to ' . $merchant->fullname;
                    $transaction->trx          = $senderTrx->trx;
                    $transaction->save();
                }
            }
        }

        notify($merchant, 'MAKE_PAYMENT_RECEIVE', [
            'merchant' => $merchant->fullname,
            'amount'   => showAmount($details->amount, currencyFormat: false),
            'after_charge'   => showAmount($afterCharge, currencyFormat: false),
            'charge'   => showAmount($details->total_charge, currencyFormat: false),
            'user'     => $user->fullname . ' ( ' . $user->username . ' )',
            'trx'      => $senderTrx->trx,
            'time'     => showDateTime($senderTrx->created_at),
            'balance'  => showAmount($merchant->balance, currencyFormat: false),
        ]);

        $notify[] = 'Successfully complete the payment';
        return apiResponse('make_payment_done', 'success', $notify, [
            'redirect_type' => "new_url",
            'redirect_url'  => route('user.make.payment.details', $makePayment->id),
            'payment'       => $makePayment
        ]);
    }
}
