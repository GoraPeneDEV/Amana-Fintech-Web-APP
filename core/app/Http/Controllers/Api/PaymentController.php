<?php

namespace App\Http\Controllers\Api;

use App\Constants\Status;
use App\Http\Controllers\Controller;
use App\Models\Deposit;
use App\Models\GatewayCurrency;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class PaymentController extends Controller
{
    public function methods()
    {
        $gatewayCurrency = GatewayCurrency::whereHas('method', function ($gate) {
            $gate->where('status', Status::ENABLE);
        })->with('method')->orderby('method_code')->get();

        $gatewayCurrency = $this->filterByUserCountry($gatewayCurrency);

        // Expose the mobile money operator names (not the PawaPay correspondent
        // codes, which stay server-side) so the app can show a picker for
        // country-scoped gateways instead of forcing a single fixed operator.
        // Also try to pre-select the right one via PawaPay's own predict-provider
        // API using the user's own phone number, so the picker starts on the
        // most likely answer instead of nothing (silently skipped on any
        // failure — this is a convenience default, never required).
        $mobileNumber = auth()->user()->mobileNumber ?? null;
        $gatewayCurrency->each(function ($currency) use ($mobileNumber) {
            $operators = $this->operatorNames($currency);
            $currency->operators = $operators;
            $currency->predicted_operator = count($operators) > 1
                ? $this->predictOperator($currency, $mobileNumber, $operators)
                : null;
        });

        $notify[] = 'Add Money Methods';

        return apiResponse("deposit_methods", "success", $notify, [
            'methods'    => $gatewayCurrency,
            'image_path' => getFilePath('gateway')
        ]);
    }

    /**
     * Country-scoped gateways (e.g. PawaPay mobile money) configure a separate
     * gateway_currency per country/correspondent, with the country code stored
     * in gateway_parameter->country. For those, only surface the entry matching
     * the connected user's own country_code, so a user in Gabon automatically
     * sees the XAF option and a user in Senegal automatically sees XOF, instead
     * of manually picking a currency. Gateways whose currencies have no
     * `country` in their parameters (Stripe, InTouch, ...) are left untouched.
     */
    private function filterByUserCountry($gatewayCurrency)
    {
        $userCountryCode = auth()->user()->country_code ?? null;

        return $gatewayCurrency->filter(function ($currency) use ($userCountryCode) {
            $params = json_decode($currency->gateway_parameter ?? '{}');
            if (!isset($params->country) || $params->country === '') {
                return true;
            }
            return $userCountryCode && strcasecmp($params->country, $userCountryCode) === 0;
        })->values();
    }

    /**
     * Returns the list of mobile money operator names configured for this
     * gateway_currency (gateway_parameter->correspondents), or an empty array
     * for gateways that don't use the operator-picker pattern. Tolerates
     * `correspondents` being stored either as a real JSON array (fresh seed)
     * or as a JSON-encoded string (after being round-tripped through the
     * plain-text admin form field).
     */
    private function operatorNames($currency)
    {
        $params = json_decode($currency->gateway_parameter ?? '{}');
        $correspondents = $params->correspondents ?? null;

        if (is_string($correspondents)) {
            $correspondents = json_decode($correspondents);
        }

        if (!is_array($correspondents)) {
            return [];
        }

        return collect($correspondents)
            ->pluck('name')
            ->filter()
            ->values()
            ->all();
    }

    /**
     * Calls PawaPay's POST /v2/predict-provider with the user's own phone
     * number and, if the predicted provider code matches one of this
     * currency's configured correspondents, returns that operator's name so
     * the app can pre-select it. Returns null on any failure/mismatch — this
     * is purely a convenience default, the picker always stays available.
     */
    private function predictOperator($currency, $mobileNumber, $operatorNames)
    {
        if (empty($mobileNumber)) {
            return null;
        }

        $params = json_decode($currency->gateway_parameter ?? '{}');
        $environment = strtolower($params->environment ?? 'sandbox');
        $baseUrl = $environment === 'production' ? 'https://api.pawapay.io' : 'https://api.sandbox.pawapay.io';

        try {
            $ch = curl_init($baseUrl . '/v2/predict-provider');
            curl_setopt_array($ch, [
                CURLOPT_CUSTOMREQUEST => 'POST',
                CURLOPT_POSTFIELDS => json_encode(['phoneNumber' => ltrim($mobileNumber, '+')]),
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_TIMEOUT => 5, // best-effort, never let this hold up the deposit-methods list
                CURLOPT_HTTPHEADER => [
                    'Content-Type: application/json',
                    'Authorization: Bearer ' . ($params->api_token ?? ''),
                ],
            ]);
            $response = curl_exec($ch);
            $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            curl_close($ch);

            if ($httpCode !== 200) {
                return null;
            }

            $predictedCode = json_decode($response, true)['provider'] ?? null;
            if (!$predictedCode) {
                return null;
            }

            $correspondents = $params->correspondents ?? [];
            if (is_string($correspondents)) {
                $correspondents = json_decode($correspondents);
            }

            foreach ($correspondents as $item) {
                if (($item->code ?? null) === $predictedCode) {
                    return $item->name ?? null;
                }
            }
        } catch (\Throwable $e) {
            return null;
        }

        return null;
    }

    public function depositInsert(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'amount'      => 'required|numeric|gt:0',
            'method_code' => 'required',
            'currency'    => 'required',
        ]);

        if ($validator->fails()) {
            return apiResponse("validation_error", "error", $validator->errors()->all());
        }

        $user         = auth()->user();
        $guardDetails = $this->guard();

        $idColumn     = $guardDetails['id_column'];
        $type         = $guardDetails['type'];

        $gate = GatewayCurrency::whereHas('method', function ($gate) {
            $gate->where('status', Status::ENABLE);
        })->where('method_code', $request->method_code)->where('currency', $request->currency)->first();


        if (!$gate) {
            $notify[] = 'The payment gateway is not found';
            return apiResponse("invalid_gateway", "error", $notify);
        }

        $operatorNames = $this->operatorNames($gate);
        if (count($operatorNames) > 0 && !in_array($request->operator, $operatorNames, true)) {
            $notify[] = 'Please select a valid mobile money operator';
            return apiResponse("invalid_operator", "error", $notify);
        }

        if ($gate->min_amount > $request->amount) {
            $notify[] = 'The amount is below the minimum limit';
            return apiResponse("below_min_limit", "error", $notify);
        }

        if ($gate->max_amount < $request->amount) {
            $notify[] = 'The amount exceeds the maximum limit';
            return apiResponse("above_max_limit", "error", $notify);
        }

        $charge      = $gate->fixed_charge + ($request->amount * $gate->percent_charge / 100);
        $payable     = $request->amount + $charge;
        $finalAmount = $payable * $gate->rate;

        $data                  = new Deposit();
        $data->from_api        = 1;
        $data->$idColumn       = $user->id;
        $data->method_code     = $gate->method_code;
        $data->method_currency = strtoupper($gate->currency);
        $data->amount          = $request->amount;
        $data->charge          = $charge;
        $data->rate            = $gate->rate;
        $data->final_amount    = $finalAmount;
        $data->btc_amount      = 0;
        $data->btc_wallet      = "";
        $data->detail          = $request->operator ? json_encode(['operator' => $request->operator]) : null;

        if ($type == 'user') {
            $data->success_url = urlPath("user.deposit.history");
            $data->failed_url  = urlPath("user.deposit.history");
        } else {
            $data->success_url = urlPath("agent.add.money.history");
            $data->failed_url  = urlPath("agent.add.money.history");
        }

        $data->trx             = getTrx();
        $data->save();

        $notify[] = 'Add Money inserted';
        return apiResponse("deposit_inserted", "success", $notify, [
            'deposit'      => $data,
            'redirect_url' => route('deposit.app.confirm', encrypt($data->id))
        ]);
    }

    public function guard()
    {
        $user     = auth()->user();
        $userType = substr($user->getTable(), 0, -1);

        return match ($userType) {
            'user'  => ['id_column' => 'user_id', 'type' => 'user'],
            'agent' => ['id_column' => 'agent_id', 'type' => 'agent'],
            default => ['id_column' => 'user_id', 'type' => 'user'],
        };
    }
}
