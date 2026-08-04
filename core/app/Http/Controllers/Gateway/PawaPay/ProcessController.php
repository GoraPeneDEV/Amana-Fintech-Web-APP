<?php

namespace App\Http\Controllers\Gateway\PawaPay;

use App\Constants\Status;
use App\Http\Controllers\Controller;
use App\Http\Controllers\Gateway\PaymentController;
use App\Models\Deposit;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

/*
 * PawaPay (mobile money aggregator, Africa) — v2 Payment Page:
 * https://docs.pawapay.io/v2/docs/payment_page
 *
 * Configuration follows the same simple pattern as every other automatic
 * gateway in this app (Flutterwave, Stripe, ...): only global fields
 * (api_token, environment), no PawaPay-specific fields on the
 * gateway_currency itself.
 *
 * Unlike the direct deposit-push API, the Payment Page is a hosted checkout:
 * POST /v2/paymentpage returns a redirectUrl, the customer completes the
 * mobile money flow on PawaPay's own page (which handles phone validation,
 * operator detection and limits itself — nothing to configure locally), then
 * lands back on returnUrl(). This is the same redirect-to-hosted-checkout
 * shape as Flutterwave/Stripe, so Gateway\PaymentController::depositConfirm()
 * already has a generic `redirect`/`redirect_url` shortcut for it and the
 * existing Flutter WebView (which detects success/failure purely by matching
 * the landing URL against success_url/failed_url) needs no changes.
 *
 * Locking `amount` on the Payment Page requires also passing `country`
 * (ISO alpha-3) — resolved automatically via PawaPay's own
 * POST /v2/predict-provider (phone number in, country out), so there is
 * still no local country/operator configuration to maintain.
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

        $country = static::predictCountry($baseUrl, $apiToken, $phoneNumber);
        if (!$country) {
            $send['error'] = true;
            $send['message'] = "We couldn't detect a mobile money country for this phone number. Please check the number on your profile and try again.";
            return json_encode($send);
        }

        $depositId = (string) Str::uuid();
        $deposit->btc_wallet = $depositId; // generic external-reference column, reused like StripeV3's session id
        $deposit->save();

        // `reason` visibility/format on the hosted page isn't documented as
        // strictly as `customerMessage` on the direct deposit API, but the
        // same safe alphanumeric filter is applied defensively.
        $reason = substr(preg_replace('/[^A-Za-z0-9 ]/', '', 'Add Money ' . gs('site_name')), 0, 22);

        $payload = [
            'depositId' => $depositId,
            'returnUrl' => route('ipn.PawaPay.return', $depositId),
            'msisdn' => $phoneNumber,
            'amount' => (string) round($deposit->final_amount, 2),
            'country' => $country,
            'reason' => $reason,
        ];

        $ch = curl_init($baseUrl . '/v2/paymentpage');
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

        if ($curlError || empty($result['redirectUrl'])) {
            $send['error'] = true;
            $send['message'] = $result['failureReason']['failureMessage'] ?? 'Unable to reach PawaPay, please try again later.';
            return json_encode($send);
        }

        $deposit->status = Status::PAYMENT_PENDING;
        $deposit->save();

        $send['redirect'] = true;
        $send['redirect_url'] = $result['redirectUrl'];
        return json_encode($send);
    }

    /**
     * POST /v2/predict-provider — used here only to resolve the ISO alpha-3
     * country for the given phone number (required by /v2/paymentpage
     * whenever `amount` is fixed). The `provider` in the response is
     * intentionally unused: the Payment Page resolves the operator itself.
     */
    private static function predictCountry($baseUrl, $apiToken, $phoneNumber)
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
        return $result['country'] ?? null;
    }

    /**
     * Browser landing point after the customer completes (or abandons) the
     * hosted Payment Page. Reaching this URL only means the checkout flow
     * ended, not that payment succeeded — the authoritative status is
     * fetched live via GET /v2/deposits/{depositId} (same source `ipn()`
     * trusts), so this works correctly whether or not the async webhook
     * callback has already landed.
     */
    public function returnFromPaymentPage($depositId)
    {
        $deposit = Deposit::where('btc_wallet', $depositId)->orderBy('id', 'DESC')->first();

        abort_if(!$deposit, 404);

        if ($deposit->status == Status::PAYMENT_SUCCESS) {
            return redirect($deposit->success_url);
        }

        if ($deposit->status == Status::PAYMENT_REJECT) {
            return redirect($deposit->failed_url);
        }

        $gatewayAcc = json_decode($deposit->gatewayCurrency()->gateway_parameter);
        $environment = strtolower($gatewayAcc->environment ?? 'sandbox');
        $baseUrl = $environment === 'production' ? 'https://api.pawapay.io' : 'https://api.sandbox.pawapay.io';

        $ch = curl_init($baseUrl . '/v2/deposits/' . $depositId);
        curl_setopt_array($ch, [
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_TIMEOUT => 15,
            CURLOPT_HTTPHEADER => [
                'Authorization: Bearer ' . ($gatewayAcc->api_token ?? ''),
            ],
        ]);
        $response = curl_exec($ch);
        curl_close($ch);

        $result = json_decode($response, true);
        $status = $result['status'] ?? null;

        if ($status === 'COMPLETED') {
            PaymentController::userDataUpdate($deposit);
            return redirect($deposit->success_url);
        }

        if (in_array($status, ['FAILED', 'REJECTED'])) {
            $deposit->status = Status::PAYMENT_REJECT;
            $deposit->save();
            return redirect($deposit->failed_url);
        }

        // Still PROCESSING/IN_RECONCILIATION on PawaPay's side — the ipn()
        // webhook will complete the deposit asynchronously once resolved.
        $notify[] = ['error', 'Your payment is still being processed. Your balance will update automatically once PawaPay confirms it.'];
        return redirect($deposit->failed_url)->withNotify($notify);
    }

    /**
     * PawaPay v2 deposit callback (server-to-server webhook, independent of
     * the browser returnUrl above). Terminal statuses are COMPLETED/FAILED
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
}
