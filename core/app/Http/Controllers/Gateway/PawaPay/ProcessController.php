<?php

namespace App\Http\Controllers\Gateway\PawaPay;

use App\Constants\Status;
use App\Http\Controllers\Controller;
use App\Http\Controllers\Gateway\PaymentController;
use App\Models\Deposit;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

/*
 * PawaPay (mobile money aggregator, Africa) — v2 API: https://docs.pawapay.io/v2/docs/deposits
 *
 * Configuration follows the same simple pattern as every other automatic
 * gateway in this app (Flutterwave, Stripe, ...): only global fields
 * (api_token, environment) plus a standard currency row per enabled
 * currency (min/max/charge/rate) — no PawaPay-specific fields on the
 * gateway_currency itself.
 *
 * The one thing PawaPay genuinely needs that others don't is a `provider`
 * (which mobile money operator: MTN_MOMO_ZMB, ORANGE_SEN, ...). Rather than
 * make the admin maintain a per-country/operator mapping, this is resolved
 * automatically at deposit time via PawaPay's own POST /v2/predict-provider
 * (https://docs.pawapay.io/v2/api-reference/toolkit/predict-provider), which
 * takes the customer's phone number and returns the country + provider
 * PawaPay itself would route the payment to. This is PawaPay's documented,
 * recommended way to determine the provider — no local country/operator
 * configuration needed at all.
 *
 * Unlike Stripe/MercadoPago (hosted checkout redirect), a PawaPay deposit is
 * a server-initiated mobile money push: POST /v2/deposits returns an
 * immediate ACCEPTED/REJECTED/DUPLICATE_IGNORED acknowledgement, then the
 * real result (COMPLETED/FAILED) arrives later on the `ipn()` callback once
 * the customer approves the prompt on their phone. The confirmation Blade
 * view polls `status()` until the callback lands, then redirects to
 * success_url/failed_url.
 */
class ProcessController extends Controller
{
    public static function process($deposit)
    {
        $gatewayAcc = json_decode($deposit->gatewayCurrency()->gateway_parameter);
        $user = $deposit->user_id != 0 ? $deposit->user : $deposit->agent;

        $environment = strtolower($gatewayAcc->environment ?? 'sandbox');
        $baseUrl = $environment === 'production'
            ? 'https://api.pawapay.io'
            : 'https://api.sandbox.pawapay.io';
        $apiToken = $gatewayAcc->api_token ?? '';
        $phoneNumber = ltrim($user->mobileNumber, '+');

        $prediction = static::predictProvider($baseUrl, $apiToken, $phoneNumber);
        if (!$prediction) {
            $send['error'] = true;
            $send['message'] = "We couldn't detect a mobile money operator for this phone number. Please check the number on your profile and try again.";
            return json_encode($send);
        }

        $depositId = (string) Str::uuid();
        $deposit->btc_wallet = $depositId; // generic external-reference column, reused like StripeV3's session id
        $deposit->detail = json_encode(['provider' => $prediction['provider'], 'country' => $prediction['country']]);
        $deposit->save();

        // customerMessage must be 4-22 alphanumeric/space characters (v2 constraint).
        $customerMessage = substr(preg_replace('/[^A-Za-z0-9 ]/', '', 'Add Money ' . gs('site_name')), 0, 22);

        $payload = [
            'depositId' => $depositId,
            'amount' => (string) round($deposit->final_amount, 2),
            'currency' => $deposit->method_currency,
            'payer' => [
                'type' => 'MMO',
                'accountDetails' => [
                    'phoneNumber' => $phoneNumber,
                    'provider' => $prediction['provider'],
                ],
            ],
            'customerMessage' => $customerMessage,
        ];

        $ch = curl_init($baseUrl . '/v2/deposits');
        curl_setopt_array($ch, [
            CURLOPT_CUSTOMREQUEST => 'POST',
            CURLOPT_POSTFIELDS => json_encode($payload),
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_TIMEOUT => 30,
            CURLOPT_HTTPHEADER => [
                'Content-Type: application/json',
                'Authorization: Bearer ' . $apiToken,
            ],
        ]);
        $response = curl_exec($ch);
        $curlError = curl_error($ch);
        curl_close($ch);

        $result = json_decode($response, true);

        if ($curlError || !isset($result['status'])) {
            $send['error'] = true;
            $send['message'] = 'Unable to reach PawaPay, please try again later.';
            return json_encode($send);
        }

        if (in_array($result['status'], ['ACCEPTED', 'DUPLICATE_IGNORED'])) {
            $deposit->status = Status::PAYMENT_PENDING;
            $deposit->save();

            $alias = $deposit->gateway->alias;
            $send['view'] = 'user.payment.' . $alias;
            $send['phone'] = $user->mobileNumber;
            $send['amount'] = $deposit->final_amount;
            $send['currency'] = $deposit->method_currency;
            $send['status_url'] = route('ipn.' . $alias . '.status', $depositId);
            return json_encode($send);
        }

        $send['error'] = true;
        $send['message'] = $result['failureReason']['failureMessage'] ?? 'Deposit request rejected by PawaPay.';
        return json_encode($send);
    }

    /**
     * POST /v2/predict-provider — returns ['country' => 'GAB', 'provider' =>
     * 'AIRTEL_GAB'] for a valid phone number, or null on any failure. This is
     * the ONLY place a "country"/"provider" concept exists in this
     * integration; nothing is stored per-currency in the DB for it.
     */
    private static function predictProvider($baseUrl, $apiToken, $phoneNumber)
    {
        $ch = curl_init($baseUrl . '/v2/predict-provider');
        curl_setopt_array($ch, [
            CURLOPT_CUSTOMREQUEST => 'POST',
            CURLOPT_POSTFIELDS => json_encode(['phoneNumber' => $phoneNumber]),
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_TIMEOUT => 15,
            CURLOPT_HTTPHEADER => [
                'Content-Type: application/json',
                'Authorization: Bearer ' . $apiToken,
            ],
        ]);
        $response = curl_exec($ch);
        $curlError = curl_error($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        if ($curlError || $httpCode !== 200) {
            return null;
        }

        $result = json_decode($response, true);
        if (empty($result['provider']) || empty($result['country'])) {
            return null;
        }

        return ['provider' => $result['provider'], 'country' => $result['country']];
    }

    /**
     * PawaPay v2 deposit callback. Terminal statuses are COMPLETED/FAILED
     * (REJECTED only ever comes back synchronously from the initiate call,
     * never the callback, but is still handled here defensively). Payload
     * shape isn't fully documented publicly, so this reads both a flat
     * top-level shape and a `data.*`-wrapped shape (matching the status-check
     * endpoint's wrapper) — whichever PawaPay actually sends will be picked up.
     * Signature verification (RFC-9421) is optional on PawaPay's side and not
     * implemented here — the lookup is scoped to a deposit still in
     * INITIATE/PENDING state as a minimal safeguard, mirroring the idempotency
     * guard already in PaymentController::userDataUpdate().
     */
    public function ipn(Request $request)
    {
        $payload = $request->all();
        $data = $payload['data'] ?? $payload;
        $depositId = $data['depositId'] ?? null;

        $deposit = Deposit::where('btc_wallet', $depositId)
            ->whereIn('status', [Status::PAYMENT_INITIATE, Status::PAYMENT_PENDING])
            ->orderBy('id', 'DESC')
            ->first();

        if (!$deposit) {
            return response()->json(['received' => true]);
        }

        $status = $data['status'] ?? null;

        if ($status === 'COMPLETED') {
            PaymentController::userDataUpdate($deposit);
        } elseif (in_array($status, ['FAILED', 'REJECTED'])) {
            $deposit->status = Status::PAYMENT_REJECT;
            $deposit->save();
        }

        return response()->json(['received' => true]);
    }

    public function status($depositId)
    {
        $deposit = Deposit::where('btc_wallet', $depositId)->orderBy('id', 'DESC')->first();

        abort_if(!$deposit, 404);

        return response()->json([
            'status' => match ((int) $deposit->status) {
                Status::PAYMENT_SUCCESS => 'success',
                Status::PAYMENT_REJECT => 'failed',
                default => 'pending',
            },
            'success_url' => route('home') . $deposit->success_url,
            'failed_url' => route('home') . $deposit->failed_url,
        ]);
    }
}
