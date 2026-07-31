<?php

namespace App\Http\Controllers\Gateway\PawaPay;

use App\Constants\Status;
use App\Http\Controllers\Controller;
use App\Http\Controllers\Gateway\PaymentController;
use App\Models\Deposit;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

/*
 * PawaPay (mobile money aggregator, Africa) — https://docs.pawapay.io/using_the_api
 *
 * Unlike Stripe/MercadoPago (hosted checkout redirect), a PawaPay deposit is a
 * server-initiated mobile money push: the API call returns an immediate
 * ACCEPTED/REJECTED acknowledgement, then the real result (COMPLETED/FAILED)
 * arrives later on the `ipn()` callback once the customer approves the USSD
 * prompt on their phone. The confirmation Blade view polls `status()` until
 * the callback lands, then redirects the WebView to success_url/failed_url.
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

        $depositId = (string) Str::uuid();
        $deposit->btc_wallet = $depositId; // generic external-reference column, reused like StripeV3's session id
        $deposit->save();

        $payload = [
            'depositId' => $depositId,
            'amount' => (string) round($deposit->final_amount, 2),
            'currency' => $deposit->method_currency,
            'country' => $gatewayAcc->country ?? '',
            'correspondent' => $gatewayAcc->correspondent ?? '',
            'payer' => [
                'type' => 'MSISDN',
                'address' => [
                    'value' => ltrim($user->mobileNumber, '+'),
                ],
            ],
            'customerTimestamp' => now()->toIso8601String(),
            'statementDescription' => substr('Add Money ' . gs('site_name'), 0, 22),
        ];

        $ch = curl_init($baseUrl . '/deposits');
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

        if (in_array($result['status'], ['ACCEPTED', 'ENQUEUED'])) {
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
     * PawaPay callback. Signature verification (RFC-9421) is optional on
     * PawaPay's side and not implemented here — the lookup is scoped to a
     * deposit still in INITIATE/PENDING state as a minimal safeguard, mirroring
     * the idempotency guard already in PaymentController::userDataUpdate().
     */
    public function ipn(Request $request)
    {
        $payload = $request->all();
        $depositId = $payload['depositId'] ?? ($payload['data']['depositId'] ?? null);

        $deposit = Deposit::where('btc_wallet', $depositId)
            ->whereIn('status', [Status::PAYMENT_INITIATE, Status::PAYMENT_PENDING])
            ->orderBy('id', 'DESC')
            ->first();

        if (!$deposit) {
            return response()->json(['received' => true]);
        }

        $status = $payload['status'] ?? ($payload['data']['status'] ?? null);

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
