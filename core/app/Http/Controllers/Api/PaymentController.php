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
