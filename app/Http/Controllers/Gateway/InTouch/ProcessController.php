<?php

namespace App\Http\Controllers\Gateway\InTouch;

use App\Constants\Status;
use App\Http\Controllers\Controller;
use App\Http\Controllers\Gateway\PaymentController;
use App\Models\Deposit;
use Illuminate\Http\Request;

/*
 * InTouch / Gutouch Transfer (cashin) API —
 * https://developers.intouchgroup.net/documentation/TRANSFER/1
 *
 * Same async, no-hosted-page pattern as PawaPay: `process()` calls the
 * `cashin` endpoint synchronously (Basic auth), which pushes a mobile money
 * confirmation prompt to the customer's phone and returns SUCCESSFUL/PENDING
 * immediately; the definitive result is confirmed via `call_back_url` (ipn()).
 */
class ProcessController extends Controller
{
    public static function process($deposit)
    {
        $gatewayAcc = json_decode($deposit->gatewayCurrency()->gateway_parameter);
        $user = $deposit->user_id != 0 ? $deposit->user : $deposit->agent;

        $agencyCode = $gatewayAcc->agency_code ?? '';
        $urlTemplate = $gatewayAcc->base_url ?? 'https://apidist.gutouch.net/apidist/sec/{agency_code}/cashin';
        $url = str_replace('{agency_code}', $agencyCode, $urlTemplate);

        $loginApi = $gatewayAcc->login_api ?? '';
        $passwordApi = $gatewayAcc->password_api ?? '';

        $payload = [
            'service_id' => $gatewayAcc->service_id ?? '',
            'recipient_phone_number' => ltrim($user->mobile, '0'),
            'amount' => round($deposit->final_amount),
            'partner_id' => $gatewayAcc->partner_id ?? '',
            'partner_transaction_id' => $deposit->trx,
            'login_api' => $loginApi,
            'password_api' => $passwordApi,
            'call_back_url' => route('ipn.InTouch'),
        ];

        $ch = curl_init($url);
        curl_setopt_array($ch, [
            CURLOPT_CUSTOMREQUEST => 'POST',
            CURLOPT_POSTFIELDS => json_encode($payload),
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_TIMEOUT => 30,
            CURLOPT_HTTPHEADER => [
                'Accept: application/json',
                'Content-Type: application/json',
                'Authorization: Basic ' . base64_encode($loginApi . ':' . $passwordApi),
            ],
        ]);
        $response = curl_exec($ch);
        $curlError = curl_error($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        $result = json_decode($response, true);

        if ($curlError) {
            $send['error'] = true;
            $send['message'] = 'Unable to reach InTouch, please try again later.';
            return json_encode($send);
        }

        $status = strtoupper($result['status'] ?? '');

        if ($httpCode == 200 && $status === 'SUCCESSFUL') {
            PaymentController::userDataUpdate($deposit);
            $send['redirect'] = true;
            $send['redirect_url'] = route('home') . $deposit->success_url;
            $send['view'] = '';
            return json_encode($send);
        }

        if (in_array($httpCode, [200, 201]) && $status === 'PENDING') {
            $deposit->status = Status::PAYMENT_PENDING;
            $deposit->save();

            $alias = $deposit->gateway->alias;
            $send['view'] = 'user.payment.' . $alias;
            $send['phone'] = $user->mobileNumber;
            $send['amount'] = $deposit->final_amount;
            $send['currency'] = $deposit->method_currency;
            $send['status_url'] = route('ipn.' . $alias . '.status', $deposit->trx);
            return json_encode($send);
        }

        $send['error'] = true;
        $send['message'] = $result['message'] ?? 'Deposit request rejected by InTouch.';
        return json_encode($send);
    }

    /**
     * InTouch callback (call_back_url). InTouch's docs do not document a
     * request-signature scheme for this callback — the lookup is scoped to a
     * deposit still in INITIATE/PENDING state as a minimal safeguard, mirroring
     * the idempotency guard already in PaymentController::userDataUpdate().
     */
    public function ipn(Request $request)
    {
        $payload = $request->all();
        $trx = $payload['partner_transaction_id'] ?? null;

        $deposit = Deposit::where('trx', $trx)
            ->whereIn('status', [Status::PAYMENT_INITIATE, Status::PAYMENT_PENDING])
            ->orderBy('id', 'DESC')
            ->first();

        if (!$deposit) {
            return response()->json(['received' => true]);
        }

        $status = strtoupper($payload['status'] ?? '');

        if ($status === 'SUCCESSFUL') {
            PaymentController::userDataUpdate($deposit);
        } elseif (in_array($status, ['FAILED', '300', '400'])) {
            $deposit->status = Status::PAYMENT_REJECT;
            $deposit->save();
        }

        return response()->json(['received' => true]);
    }

    public function status($trx)
    {
        $deposit = Deposit::where('trx', $trx)->orderBy('id', 'DESC')->first();

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
