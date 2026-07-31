<?php

namespace App\Http\Controllers\Admin;

use App\Constants\Status;
use App\Http\Controllers\Controller;
use App\Lib\Reloadly;
use App\Models\Country;
use App\Models\Operator;
use App\Models\Topup;
use Exception;
use Illuminate\Http\Request;

class AirtimeController extends Controller
{
    public function countries()
    {
        $pageTitle = 'Mobile Recharge Countries';
        $countries = Country::searchable(['name', 'iso_name', 'continent', 'currency_code'])->withCount('operators')->orderBy('name')->airtime()->paginate(getPaginate());

        if (session()->has('countries')) session()->forget('countries');

        return view('admin.airtime.countries', compact('pageTitle', 'countries'));
    }

    public function fetchCountries()
    {

        $pageTitle            = 'Reloadly Supported Countries';
        $existingCountryCodes = Country::where('is_airtime', Status::YES)->pluck('iso_name')->toArray();

        try {
            $reloadly     = new Reloadly();
            $apiCountries = $reloadly->getCountries();
        } catch (Exception $ex) {
            $notify[] = ['error', $ex->getMessage() ?? "Something went wrong"];
            return back()->withNotify($notify);
        }

        session()->put('countries', $apiCountries);
        $apiCountries = collect($apiCountries);

        return view('admin.airtime.fetch_countries', compact('pageTitle', 'existingCountryCodes', 'apiCountries'));
    }

    public function saveCountries(Request $request)
    {

        $request->validate([
            'countries' => 'required|array|min:1',
        ]);

        $countryArray     = [];
        $requestCountries = collect(session('countries'))->whereIn('isoName', $request->countries);



        foreach ($requestCountries as $item) {

            $item    = (object) $item;
            $country = Country::where('iso_name', @$item->isoName)->first();

            if ($country) {
                if ($country->is_airtime == Status::NO) {
                    $country->is_airtime    = Status::YES;
                    $country->calling_codes = $item->callingCodes;
                    $country->save();
                }
                continue;
            } else {
                $country = new Country();
                $country->name            = $item->name;
                $country->iso_name        = $item->isoName;
                $country->continent       = $item->continent;
                $country->currency_code   = $item->currencyCode;
                $country->currency_name   = $item->currencyName;
                $country->currency_symbol = $item->currencySymbol;
                $country->flag_url        = $item->flag;
                $country->is_airtime      = Status::YES;
                $country->calling_codes   = $item->callingCodes;
                $country->save();
            }
        }


        session()->forget('countries');

        $notify[] = ['success', 'Country added successfully'];
        return to_route('admin.airtime.countries')->withNotify($notify);
    }

    public function updateCountryStatus($id)
    {
        return Country::changeStatus($id);
    }

    public function operators($iso)
    {
        $country   = Country::where('iso_name', $iso)->firstOrFail();
        $pageTitle = $country->iso_name . ' Mobile Recharge Operators';
        $operators = Operator::searchable(['name', 'country:name'])->with('country:id,name,iso_name,currency_code')->where('country_id', $country->id);
        $operators = $operators->orderBy('name')->paginate(getPaginate());
        if (session()->has('operators')) session()->forget('operators');

        return view('admin.airtime.operators', compact('pageTitle', 'operators', 'iso'));
    }

    public function fetchOperatorsByISO($iso)
    {
        $country                    = Country::where('iso_name', $iso)->with('operators')->firstOrFail();
        $pageTitle                  = 'Reloadly Supported ' . $country->iso_name . ' Operators';

        try {
            $reloadly                   = new Reloadly();
            $reloadlySupportedOperators = $reloadly->getOperatorsByISO($iso);
        } catch (Exception $ex) {
            $notify[] = ['error', $ex->getMessage() ?? "Something went wrong"];
            return back()->withNotify($notify);
        }

        session()->put('operators', $reloadlySupportedOperators);
        $existingOperatorIds = Operator::pluck('unique_id')->toArray();
        return view('admin.airtime.fetch_operators', compact('pageTitle', 'country', 'reloadlySupportedOperators', 'existingOperatorIds'));
    }

    public function saveOperators(Request $request, $iso)
    {
        $request->validate([
            'operators' => 'required|array|min:1',
        ]);

        $country          = Country::where('iso_name', $iso)->firstOrFail();
        $requestOperators = collect(session('operators'))->whereIn('operatorId', $request->operators);

        session()->forget('operators');

        foreach ($requestOperators as $item) {

            $item = (object) $item;

            $operator = new Operator();

            $operator->country_id                           = $country->id;
            $operator->unique_id                            = $item->operatorId;
            $operator->name                                 = $item->name;
            $operator->bundle                               = $item->bundle ? 1 : 0;
            $operator->data                                 = $item->data ? 1 : 0;
            $operator->pin                                  = $item->pin ? 1 : 0;
            $operator->supports_local_amount                = $item->supportsLocalAmounts ? 1 : 0;
            $operator->supports_geographical_recharge_plans = $item->supportsGeographicalRechargePlans ? 1 : 0;
            $operator->denomination_type                    = $item->denominationType;
            $operator->sender_currency_code                 = $item->senderCurrencyCode;
            $operator->sender_currency_symbol               = $item->senderCurrencySymbol;
            $operator->destination_currency_code            = $item->destinationCurrencyCode;
            $operator->destination_currency_symbol          = $item->destinationCurrencySymbol;
            $operator->commission                           = $item->commission;
            $operator->international_discount               = $item->internationalDiscount;
            $operator->local_discount                       = $item->localDiscount;
            $operator->most_popular_amount                  = $item->mostPopularAmount;
            $operator->most_popular_local_amount            = $item->mostPopularLocalAmount;
            $operator->min_amount                           = $item->minAmount;
            $operator->max_amount                           = $item->maxAmount;
            $operator->local_min_amount                     = $item->localMinAmount;
            $operator->local_max_amount                     = $item->localMaxAmount;
            $operator->rate                                 = $item->fx['rate'];
            $operator->logo_urls                            = $item->logoUrls;
            $operator->fixed_amounts                        = $item->fixedAmounts;
            $operator->fixed_amounts_descriptions           = $item->fixedAmountsDescriptions;
            $operator->local_fixed_amounts                  = $item->localFixedAmounts;
            $operator->local_fixed_amounts_descriptions     = $item->localFixedAmountsDescriptions;
            $operator->suggested_amounts                    = $item->suggestedAmounts;
            $operator->suggested_amounts_map                = $item->suggestedAmountsMap;
            $operator->fees                                 = $item->fees;
            $operator->geographical_recharge_plans          = $item->geographicalRechargePlans;
            $operator->reloadly_status                      = $item->status;
            $operator->save();
        }

        $notify[] = ['success', 'Operators added successfully'];
        return to_route('admin.airtime.operators', $country->iso_name)->withNotify($notify);
    }

    public function updateOperatorStatus($id)
    {
        return Operator::changeStatus($id);
    }

    public function history()
    {
        $pageTitle  = 'All Topup History';
        $baseQuery  = Topup::with('operator')->orderBy('id', getOrderBy());

        if (request()->export) {
            return exportData($baseQuery, request()->export, "Topup");
        }

        $transactions = $baseQuery->paginate(getPaginate());
        return view('admin.airtime.history', compact('pageTitle', 'transactions'));
    }
}
