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
 * Unlike Stripe/MercadoPago (hosted checkout redirect), a PawaPay deposit is a
 * server-initiated mobile money push: the API call returns an immediate
 * ACCEPTED/REJECTED/DUPLICATE_IGNORED acknowledgement, then the real result
 * (COMPLETED/FAILED) arrives later on the `ipn()` callback once the customer
 * approves the USSD prompt on their phone. The confirmation Blade view polls
 * `status()` until the callback lands, then redirects to success/failed_url.
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

        $provider = static::resolveCorrespondent($gatewayAcc, $deposit);
        if (empty($provider)) {
            $send['error'] = true;
            $send['message'] = 'This mobile money operator is not configured yet. Please contact support.';
            return json_encode($send);
        }

        $depositId = (string) Str::uuid();
        $deposit->btc_wallet = $depositId; // generic external-reference column, reused like StripeV3's session id
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
                    'phoneNumber' => ltrim($user->mobileNumber, '+'),
                    'provider' => $provider,
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
                'Authorization: Bearer ' . ($gatewayAcc->api_token ?? ''),
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
     * Resolves the PawaPay correspondent code for this deposit: looks up the
     * operator name the user picked (stored on Deposit::detail by
     * Api\PaymentController::depositInsert()) inside this currency's
     * gateway_parameter->correspondents list. Falls back to the single
     * legacy `correspondent` field for currencies configured before the
     * multi-operator picker existed. Returns null if nothing usable is found.
     */
    private static function resolveCorrespondent($gatewayAcc, $deposit)
    {
        $correspondents = $gatewayAcc->correspondents ?? null;
        if (is_string($correspondents)) {
            $correspondents = json_decode($correspondents);
        }

        if (is_array($correspondents) && count($correspondents) > 0) {
            $operator = json_decode($deposit->detail ?? '{}')->operator ?? null;

            foreach ($correspondents as $item) {
                if (($item->name ?? null) === $operator) {
                    return $item->code ?? null;
                }
            }

            return null; // configured for multi-operator but no match found — don't guess
        }

        return $gatewayAcc->correspondent ?? null; // legacy single-operator config
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
