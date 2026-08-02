<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Gateway;
use App\Models\GatewayCurrency;
use App\Rules\FileTypeValidate;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class AutomaticGatewayController extends Controller
{
    public function index()
    {
        $pageTitle      = 'Automatic Gateways';
        $gateways       = Gateway::automatic()->with('currencies')->get();
        $manualGateways = Gateway::manual()->orderBy('id','desc')->with('currencies')->get();

        return view('admin.gateways.automatic.list', compact('pageTitle', 'gateways','manualGateways'));
    }

    public function edit($alias)
    {
        $gateway = Gateway::automatic()->with('currencies', 'currencies.method')->where('alias', $alias)->firstOrFail();
        $pageTitle = 'Update Gateway';

        $supportedCurrencies = collect($gateway->supported_currencies)->except($gateway->currencies->pluck('currency'));
        $globalParameters = null;
        $hasCurrencies = false;
        $currencyIndex = 1;
        $parameters = collect(json_decode($gateway->gateway_parameters));
        if ($gateway->currencies->count()) {
            $globalParameters = json_decode($gateway->currencies->first()->gateway_parameter);
            $hasCurrencies = true;
        }

        return view('admin.gateways.automatic.edit', compact('pageTitle', 'gateway', 'supportedCurrencies', 'parameters', 'hasCurrencies', 'currencyIndex', 'globalParameters'));
    }


    public function update(Request $request, $code)
    {
        $gateway             = Gateway::where('code', $code)->firstOrFail();
        $supportedCurrencies = collect($gateway->supported_currencies)->flip()->implode(',');
        $parameters          = collect(json_decode($gateway->gateway_parameters));

        $validationRule = [
            'image'                     => ['nullable', 'image', new FileTypeValidate(['jpg', 'jpeg', 'png'])],
            'currency'                  => "required|array|min:1",
            'currency.*.currency'       => 'required|string|in:' . $supportedCurrencies,
            'currency.*.symbol'         => 'required|string',
            'currency.*.name'           => 'required',
            'currency.*.min_amount'     => 'required|numeric|gt:0',
            'currency.*.max_amount'     => 'required|numeric|gt:0',
            'currency.*.fixed_charge'   => 'required|numeric|gte:0',
            'currency.*.percent_charge' => 'required|numeric|gte:0|max:100',
            'currency.*.rate'           => 'required|numeric|gt:0',
        ];

        foreach ($parameters as $key => $pram) {
            if ($pram->global) {
                $validationRule[$key] = "required";
            } else {
                $validationRule["currency.*.$key"] = "required";
            }
        }

        $request->validate($validationRule);

        foreach ($parameters->where('global', true) as $key => $pram) {
            $parameters[$key]->value = $request->$key;
        }

        $filename = $gateway->image;

        if ($request->hasFile('image')) {
            try {
                $filename = fileUploader($request->image, getFilePath('gateway'), old: $filename);
            } catch (\Exception $exp) {
                $notify[] = ['errors', 'Image could not be uploaded'];
                return back()->withNotify($notify);
            }
        }

        $gateway->gateway_parameters = json_encode($parameters);
        $gateway->image              = $filename;
        $gateway->save();

        foreach ($request->currency as $key => $currency) {
            $param = [];

            foreach ($parameters->where('global', true) as $pkey => $pram) {
                $param[$pkey] = $pram->value;
            }

            foreach ($parameters->where('global', false) as $paramKey => $paramValue) {
                $param[$paramKey] = $currency[$paramKey];
            }

            $gatewayCurrency = GatewayCurrency::where('currency', strtoupper($currency['currency']))->where('method_code', $code)->first();

            if (!$gatewayCurrency) {
                $gatewayCurrency              = new GatewayCurrency();
                $gatewayCurrency->method_code = $code;
            }

            $gatewayCurrency->name              = $currency['name'];
            $gatewayCurrency->gateway_alias     = $gateway->alias;
            $gatewayCurrency->currency          = $currency['currency'];
            $gatewayCurrency->min_amount        = $currency['min_amount'];
            $gatewayCurrency->max_amount        = $currency['max_amount'];
            $gatewayCurrency->fixed_charge      = $currency['fixed_charge'];
            $gatewayCurrency->percent_charge    = $currency['percent_charge'];
            $gatewayCurrency->rate              = $currency['rate'];
            $gatewayCurrency->symbol            = $currency['symbol'];
            $gatewayCurrency->gateway_parameter = json_encode($param);
            $gatewayCurrency->save();
        }

        $notify[] = ['success', $gateway->name . ' updated successfully'];
        return to_route('admin.gateway.automatic.edit', $gateway->alias)->withNotify($notify);
    }



    public function remove($id)
    {
        $gatewayCurrency = GatewayCurrency::findOrFail($id);
        fileManager()->removeFile(getFilePath('gateway') . '/' . $gatewayCurrency->image);
        $gatewayCurrency->delete();
        $notify[] = ['success', 'Gateway currency removed successfully'];
        return back()->withNotify($notify);
    }

    // ISO 3166-1 alpha-2 -> alpha-3, for the countries our `country` field
    // (alpha-2, shared with operating_countries/users.country_code) needs to
    // be translated to when calling PawaPay's alpha-3-only active-conf API.
    // Add more entries here as new operating countries are enabled.
    private const ISO2_TO_ISO3 = [
        'GA' => 'GAB', 'SN' => 'SEN', 'CI' => 'CIV', 'CM' => 'CMR', 'TD' => 'TCD',
        'CG' => 'COG', 'CD' => 'COD', 'BJ' => 'BEN', 'TG' => 'TGO', 'BF' => 'BFA',
        'ML' => 'MLI', 'NE' => 'NER', 'GH' => 'GHA', 'KE' => 'KEN', 'UG' => 'UGA',
        'ZM' => 'ZMB', 'RW' => 'RWA', 'TZ' => 'TZA', 'NG' => 'NGA',
    ];

    /**
     * Live-syncs the mobile money operators for one PawaPay gateway_currency
     * from PawaPay's own /v2/active-conf endpoint, replacing the manually
     * typed `correspondents` JSON with verified provider codes straight from
     * the merchant's own PawaPay account — no manual code entry needed.
     */
    public function syncOperators($id)
    {
        $gatewayCurrency = GatewayCurrency::with('method')->findOrFail($id);

        if (($gatewayCurrency->method->alias ?? null) !== 'PawaPay') {
            $notify[] = ['error', 'Operator sync is only available for PawaPay currencies'];
            return back()->withNotify($notify);
        }

        $params = json_decode($gatewayCurrency->gateway_parameter ?? '{}');
        $countryIso2 = strtoupper($params->country ?? '');
        $countryIso3 = self::ISO2_TO_ISO3[$countryIso2] ?? null;

        if (!$countryIso3) {
            $notify[] = ['error', "Country \"$countryIso2\" is not in the ISO2→ISO3 mapping yet — add it to AutomaticGatewayController::ISO2_TO_ISO3."];
            return back()->withNotify($notify);
        }

        $environment = strtolower($params->environment ?? 'sandbox');
        $baseUrl = $environment === 'production' ? 'https://api.pawapay.io' : 'https://api.sandbox.pawapay.io';

        $ch = curl_init($baseUrl . '/v2/active-conf?country=' . $countryIso3 . '&operationType=DEPOSIT');
        curl_setopt_array($ch, [
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_TIMEOUT => 20,
            CURLOPT_HTTPHEADER => [
                'Content-Type: application/json',
                'Authorization: Bearer ' . ($params->api_token ?? ''),
            ],
        ]);
        $response = curl_exec($ch);
        $curlError = curl_error($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        if ($curlError) {
            $notify[] = ['error', 'Unable to reach PawaPay: ' . $curlError];
            return back()->withNotify($notify);
        }

        $result = json_decode($response, true);

        if ($httpCode !== 200) {
            $message = $result['failureReason']['failureMessage'] ?? 'PawaPay rejected the request.';
            $notify[] = ['error', 'PawaPay sync failed: ' . $message];
            return back()->withNotify($notify);
        }

        $providers = $result['countries'][0]['providers'] ?? [];
        $correspondents = [];
        foreach ($providers as $provider) {
            $isOperational = collect($provider['currencies'] ?? [])
                ->contains(fn($c) => ($c['operationTypes']['DEPOSIT']['status'] ?? null) === 'OPERATIONAL');
            if ($isOperational) {
                $correspondents[] = [
                    'name' => $provider['displayName'] ?? $provider['provider'],
                    'code' => $provider['provider'],
                ];
            }
        }

        if (empty($correspondents)) {
            $notify[] = ['error', "PawaPay returned no operational DEPOSIT providers for $countryIso3 on this account."];
            return back()->withNotify($notify);
        }

        $params->correspondents = $correspondents;
        $gatewayCurrency->gateway_parameter = json_encode($params);
        $gatewayCurrency->save();

        $names = collect($correspondents)->pluck('name')->implode(', ');
        $notify[] = ['success', "Synced from PawaPay: $names"];
        return back()->withNotify($notify);
    }

    public function status($id)
    {
        return Gateway::changeStatus($id);
    }



    public function gatewayCurrencyValidator(Request $request, Gateway $gateway)
    {
        $customAttributes = [];
        $validationRule = [];

        $paramList = collect(json_decode($gateway->gateway_parameters));
        $supportedCurrencies = collect($gateway->supported_currencies)->flip()->implode(',');

        foreach ($paramList->where('global', true) as $key => $pram) {
            $validationRule['global.' . $key] = 'required';
            $customAttributes['global.' . $key] = keyToTitle($key);
        }


        if ($request->has('currency')) {
            foreach ($request->currency as $key => $currency) {
                $validationRule['currency.' . $key . '.currency']       = 'required|string|in:' . $supportedCurrencies;
                $validationRule['currency.' . $key . '.symbol']       = 'required|string';

                $validationRule['currency.' . $key . '.name']           = 'required';
                $validationRule['currency.' . $key . '.min_amount']     = 'required|numeric|gt:0|lte:currency.' . $key . '.max_amount';
                $validationRule['currency.' . $key . '.max_amount']     = 'required|numeric|gt:0|gte:currency.' . $key . '.min_amount';
                $validationRule['currency.' . $key . '.fixed_charge']   = 'required|numeric|gte:0';
                $validationRule['currency.' . $key . '.percent_charge'] = 'required|numeric|gte:0|max:100';
                $validationRule['currency.' . $key . '.rate']           = 'required|numeric|gt:0';

                $supportedCurrencies = explode(',', $supportedCurrencies);

                $supportedCurrencies = collect(removeElement($supportedCurrencies, $currency['currency']))->implode(',');

                $currencyIdentifier = $this->currencyIdentifier($currency['name'], $gateway->name . ' ' . $currency['currency']);

                $customAttributes['currency.' . $key . '.name']           = $currencyIdentifier . ' name';
                $customAttributes['currency.' . $key . '.min_amount']     = $currencyIdentifier . ' ' . keyToTitle('min_amount');
                $customAttributes['currency.' . $key . '.max_amount']     = $currencyIdentifier . ' ' . keyToTitle('max_amount');
                $customAttributes['currency.' . $key . '.fixed_charge']   = $currencyIdentifier . ' ' . keyToTitle('fixed_charge');
                $customAttributes['currency.' . $key . '.percent_charge'] = $currencyIdentifier . ' ' . keyToTitle('percent_charge');
                $customAttributes['currency.' . $key . '.rate']           = $currencyIdentifier . ' ' . keyToTitle('rate');
                $customAttributes['currency.' . $key . '.currency']           = $currencyIdentifier . ' ' . keyToTitle('currency');
                $customAttributes['currency.' . $key . '.symbol']           = $currencyIdentifier . ' ' . keyToTitle('symbol');

                foreach ($paramList->where('global', false) as $param_key => $param_value) {
                    $validationRule['currency.' . $key . '.param.' . $param_key] = 'required';
                    $customAttributes['currency.' . $key . '.param.' . $param_key] = $currencyIdentifier . ' ' . keyToTitle($param_value->title);
                }
            }
        }

        $validator = Validator::make($request->all(), $validationRule, $customAttributes);
        return $validator;
    }

    private function currencyIdentifier($name, $default = '')
    {
        return $name ?? $default;
    }
}
