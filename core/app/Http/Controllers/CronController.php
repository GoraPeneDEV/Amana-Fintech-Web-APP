<?php

namespace App\Http\Controllers;

use App\Constants\Status;
use App\Events\QrCodeLogin;
use App\Lib\CurlRequest;
use App\Lib\GiftCardProvider\ReloadLy as GiftCardProviderReloadLy;
use App\Lib\Reloadly;
use App\Models\CronJob;
use App\Models\CronJobLog;
use App\Models\GiftCard;
use App\Models\GiftCardPurchase;
use App\Models\Investment;
use App\Models\InvestmentInterest;
use App\Models\Operator;
use App\Models\QrCode;
use App\Models\Transaction;
use App\Models\UtilityBill;
use Carbon\Carbon;
use Exception;

class CronController extends Controller
{

    public function cron()
    {
        $general            = gs();
        $general->last_cron = now();
        $general->save();

        $crons = CronJob::with('schedule');

        if (request()->alias) {
            $crons->where('alias', request()->alias);
        } else {
            $crons->where('next_run', '<', now())->where('is_running', Status::YES);
        }

        $crons = $crons->get();

        foreach ($crons as $cron) {
            $cronLog              = new CronJobLog();
            $cronLog->cron_job_id = $cron->id;
            $cronLog->start_at    = now();
            if ($cron->is_default) {
                $controller = new $cron->action[0];
                try {
                    $method = $cron->action[1];
                    $controller->$method();
                } catch (\Exception $e) {
                    $cronLog->error = $e->getMessage();
                }
            } else {
                try {
                    CurlRequest::curlContent($cron->url);
                } catch (\Exception $e) {
                    $cronLog->error = $e->getMessage();
                }
            }
            $cron->last_run = now();
            $cron->next_run = now()->addSeconds((int) $cron->schedule->interval);
            $cron->save();

            $cronLog->end_at = $cron->last_run;

            $startTime         = Carbon::parse($cronLog->start_at);
            $endTime           = Carbon::parse($cronLog->end_at);
            $diffInSeconds     = $startTime->diffInSeconds($endTime);
            $cronLog->duration = $diffInSeconds;
            $cronLog->save();
        }

        if (request()->target == 'all') {
            $notify[] = ['success', 'Cron executed successfully'];
            return back()->withNotify($notify);
        }

        if (request()->alias) {
            $notify[] = ['success', keyToTitle(request()->alias) . ' executed successfully'];
            return back()->withNotify($notify);
        }
    }

    public function resetLoginQrCode()
    {
        try {

            foreach (['user', 'agent', 'merchant'] as $guard) {
                $columnName = "for_" . $guard . "_login";
                $qrCode     = QrCode::where($columnName, Status::YES)->first();

                if (! $qrCode) {
                    getQrCodeUrlForLogin($guard, false);
                } else {
                    $diffInMinute = now()->parse($qrCode->created_at)->diffInMinutes(now());

                    if ($diffInMinute > 5) {
                        $qrCode->delete();
                        $qrCode = getQrCodeUrlForLogin($guard, false);
                        if (gs('pusher_config')) {
                            event(new QrCodeLogin("$guard-qr_code_reset", [
                                'qr_code' => $qrCode,
                            ], "qr_code_reset"));
                        }
                    }
                }
            }
        } catch (Exception $ex) {
            throw new Exception($ex->getMessage());
        }
    }

    public function updateAutomaticBillPayment()
    {
        $bills = UtilityBill::whereNotNull('api_provider_trx')->whereHas('apiProvider')->processing()->orderBy('cron_ordering')->take(20)->get();

        foreach ($bills as $bill) {

            $bill->cron_ordering = time();
            $bill->save();

            $provider = $bill->apiProvider;

            try {
                $className = 'App\\Lib\\UtilityBillProvider\\' . $provider->alias;
                (new $className())->payBillUpdate($bill);
            } catch (Exception $ex) {
                continue;
            }
        }
    }

    public function updateOperator()
    {

        $operators = Operator::active()->whereNotNull('unique_id')->where('unique_id', '!=', 0)->take(20)->orderBy('cron_ordering')->get();

        foreach ($operators as $operator) {

            $reloadly            = new Reloadly();
            $operatorUpdatedData = $reloadly->updateOperator($operator->unique_id);

            $operator->bundle                               = $operatorUpdatedData['bundle'] ?? 0;
            $operator->data                                 = $operatorUpdatedData['data'] ?? 0;
            $operator->pin                                  = $operatorUpdatedData['pin'] ?? 0;
            $operator->supports_local_amount                = $operatorUpdatedData['supportsLocalAmounts'] ?? 0;
            $operator->supports_geographical_recharge_plans = $operatorUpdatedData['supportsGeographicalRechargePlans'] ?? 0;
            $operator->denomination_type                    = $operatorUpdatedData['denominationType'] ?? null;
            $operator->commission                           = $operatorUpdatedData['commission'] ?? 0;
            $operator->international_discount               = $operatorUpdatedData['internationalDiscount'] ?? 0;
            $operator->local_discount                       = $operatorUpdatedData['localDiscount'] ?? 0;
            $operator->most_popular_amount                  = $operatorUpdatedData['mostPopularAmount'] ?? null;
            $operator->most_popular_local_amount            = $operatorUpdatedData['mostPopularLocalAmount'] ?? null;
            $operator->min_amount                           = $operatorUpdatedData['minAmount'] ?? null;
            $operator->max_amount                           = $operatorUpdatedData['maxAmount'] ?? null;
            $operator->local_min_amount                     = $operatorUpdatedData['localMinAmount'] ?? null;
            $operator->local_max_amount                     = $operatorUpdatedData['localMaxAmount'] ?? null;
            $operator->rate                                 = isset($operatorUpdatedData['fx']) ? $operatorUpdatedData['fx']['rate'] : 0;
            $operator->fixed_amounts                        = isset($operatorUpdatedData['fixedAmounts']) ? (array) json_encode($operatorUpdatedData['fixedAmounts']) : null;
            $operator->fixed_amounts_descriptions           = isset($operatorUpdatedData['fixedAmountsDescriptions']) ? (object) json_encode($operatorUpdatedData['fixedAmountsDescriptions']) : null;
            $operator->local_fixed_amounts                  = isset($operatorUpdatedData['localFixedAmounts']) ? (array) json_encode($operatorUpdatedData['localFixedAmounts']) : null;
            $operator->suggested_amounts                    = isset($operatorUpdatedData['suggestedAmounts']) ? (array) json_encode($operatorUpdatedData['suggestedAmounts']) : null;
            $operator->suggested_amounts_map                = isset($operatorUpdatedData['suggestedAmountsMap']) ? (object) json_encode($operatorUpdatedData['suggestedAmountsMap']) : null;
            $operator->cron_ordering                        = time();
            $operator->save();
        }
    }

    public function updateGiftCardPurchase()
    {
        $giftCardPurchases = GiftCardPurchase::whereNotNull('api_provider_trx')->whereHas('apiProvider')->pending()->orderBy('cron_ordering')->take(20)->get();

        foreach ($giftCardPurchases as $giftCardPurchase) {

            $giftCardPurchase->cron_ordering = time();
            $giftCardPurchase->save();

            $provider = $giftCardPurchase->apiProvider;

            try {
                $className = 'App\\Lib\\GiftCardProvider\\' . $provider->alias;
                (new $className())->giftCardPurchaseUpdate($giftCardPurchase);
            } catch (Exception $ex) {
                throw new Exception($ex->getMessage());
            }
        }
    }

    public function updateAllGiftCardsData()
    {
        GiftCard::active()->orderBy('cron_ordering')->chunk(20, function ($giftCards) {
            $reloadLy = new GiftCardProviderReloadLy();

            foreach ($giftCards as $giftCard) {
                try {
                    $item = $reloadLy->updateGiftCardData($giftCard);

                    $giftCard->supports_pre_order            = $item['supportsPreOrder'];
                    $giftCard->sender_fee                    = $item['senderFee'];
                    $giftCard->sender_fee_percentage         = $item['senderFeePercentage'];
                    $giftCard->discount_percentage           = $item['discountPercentage'];
                    $giftCard->denomination_type             = $item['denominationType'];
                    $giftCard->recipient_currency_code       = $item['recipientCurrencyCode'];
                    $giftCard->min_recipient_denomination    = $item['minRecipientDenomination'];
                    $giftCard->max_recipient_denomination    = $item['maxRecipientDenomination'];
                    $giftCard->sender_currency_code          = $item['senderCurrencyCode'];
                    $giftCard->min_sender_denomination       = $item['minSenderDenomination'];
                    $giftCard->max_sender_denomination       = $item['maxSenderDenomination'];
                    $giftCard->fixed_recipient_denominations = $item['fixedRecipientDenominations'];
                    $giftCard->fixed_sender_denominations    = $item['fixedSenderDenominations'];
                    $giftCard->fixed_recipient_to_sender_map = $item['fixedRecipientToSenderDenominationsMap'];
                    $giftCard->metadata                      = $item['metadata'];
                    $giftCard->logo_urls                     = $item['logoUrls'];
                    $giftCard->cron_ordering                 = time();
                    $giftCard->save();
                } catch (\Exception $ex) {
                    continue;
                }
            }
        });
    }

    public function investment()
    {

        $investments = Investment::running()->with('plan', 'user')->where('next_return_at', '<=', now())->get();

        foreach ($investments as $investment) {

            // dd(Carbon::now());
            $user = $investment->user;

            $interestAmount = $investment->per_interest_amount;
            $user->balance += $interestAmount;
            $user->save();

            $trxId = getTrx();

            $transaction               = new Transaction();
            $transaction->user_id      = $user->id;
            $transaction->amount       = $interestAmount;
            $transaction->post_balance = $user->balance;
            $transaction->trx_type     = '+';
            $transaction->details      = showAmount($interestAmount) . ' interest from ' . $investment->plan->name . ' investment trx: ' . $investment->trx;
            $transaction->trx          = $trxId;
            $transaction->remark       = 'investment_interest';
            $transaction->save();

            $investmentInterest                = new InvestmentInterest();
            $investmentInterest->user_id       = $user->id;
            $investmentInterest->investment_id = $investment->id;
            $investmentInterest->amount        = $interestAmount;
            $investmentInterest->trx           = $trxId;
            $investmentInterest->return_time   = now();
            $investmentInterest->save();

            $nextReturnAt                           = Carbon::parse(now()->addHours((int) $investment->plan->investTime->time));
            $investment->next_return_at             = $nextReturnAt;
            $investment->last_return_at             = Carbon::now();
            $investment->total_repeat_get          += 1;
            $investment->total_interest_amount_get += $interestAmount;

            if ($investment->total_repeat_get >= $investment->total_repeat && $investment->total_repeat != Status::LIFETIME) {
                $investment->status = Status::INVESTMENT_COMPLETED;


                $user->balance += $investment->invest_amount;
                $user->save();

                $transaction               = new Transaction();
                $transaction->user_id      = $user->id;
                $transaction->amount       = $investment->invest_amount;
                $transaction->post_balance = $user->balance;
                $transaction->trx_type     = '+';
                $transaction->details      = "Capital amount is back to your balance, Investment Trx: " . $investment->trx . " Capital amount: " . showAmount($investment->invest_amount);
                $transaction->trx          = $investment->trx;
                $transaction->remark       = 'investment_capital_back';
                $transaction->save();

                notify($user, 'INVESTMENT_COMPLETED', [
                    'plan_name'      => $investment->plan->name,
                    'user'           => $user->username,
                    'trx'            => $transaction->trx,
                    'invest_amount'  => showAmount($investment->invest_amount),
                    'total_return'   => showAmount($investment->total_interest_amount_get),
                    'time'           => showDateTime(now()),
                    'remain_balance' => showAmount($user->balance),
                ]);

                notify($user, 'INVESTMENT_CAPITAL_BACK', [
                    'plan_name'      => $investment->plan->name,
                    'user'           => $user->username,
                    'trx'            => $transaction->trx,
                    'capital_amount' => showAmount($investment->invest_amount),
                    'time'           => showDateTime(now()),
                    'remain_balance' => showAmount($user->balance),
                ]);
            }

            $investment->save();

            notify($user, 'INVESTMENT_INTEREST', [
                'user'           => $user->username,
                'amount'         => showAmount($interestAmount),
                'plan_name'      => $investment->plan->name,
                'trx'            => $transaction->trx,
                'remain_balance' => showAmount($user->balance),
                'time'           => showDateTime(now()),
            ]);
        }
    }
}
