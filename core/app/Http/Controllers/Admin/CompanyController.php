<?php

namespace App\Http\Controllers\Admin;

use App\Constants\Status;
use App\Http\Controllers\Controller;
use App\Models\ApiProvider;
use App\Models\BillCategory;
use App\Models\Company;
use App\Models\Country;
use App\Models\TransactionCharge;
use App\Rules\FileTypeValidate;
use Exception;
use Illuminate\Http\Request;

class CompanyController extends Controller
{

    public function countries()
    {
        $pageTitle = 'Utility Biller Countries';
        $countries = Country::utilityBill()->searchable(['name', 'iso_name', 'continent', 'currency_code'])->orderBy('name')->withCount('companies')->utilityBill()->paginate(getPaginate());

        if (session()->has('countries')) session()->forget('countries');

        return view('admin.company.countries', compact('pageTitle', 'countries'));
    }

    public function fetchCountries()
    {

        $provider = ApiProvider::whereJsonContains('module', 'utility_bill')->where('status->utility_bill', Status::YES)->first();

        if (!$provider) {
            $notify[] = ['error', 'At least one automated utility bill provider integration is required to import biller counties.'];
            return back()->withNotify($notify);
        }

        $pageTitle            = 'Reloadly Supported Countries';
        $existingCountryCodes = Country::utilityBill()->pluck('iso_name')->toArray();

        try {
            $className  = 'App\\Lib\\UtilityBillProvider\\' . $provider->alias;
            $apiCountries = (new $className())->getCountries();
        } catch (Exception $ex) {
            $notify[] = ['error', $ex->getMessage()];
            return back()->withNotify($notify);
        }

        session()->put('countries', $apiCountries);
        $apiCountries = collect($apiCountries);

        return view('admin.company.fetch_countries', compact('pageTitle', 'existingCountryCodes', 'apiCountries'));
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
                if ($country->is_utility_bill == Status::NO) {
                    $country->is_utility_bill = Status::YES;
                    $country->save();
                }

                continue;
            };

            $countryArray[] = [
                'name'            => $item->name,
                'iso_name'        => $item->isoName,
                'currency_name'   => $item->currencyName,
                'flag_url'        => $item->flagUrl,
                'is_utility_bill' => Status::YES
            ];
        }

        Country::insert($countryArray);

        session()->forget('countries');

        $notify[] = ['success', 'Country added successfully'];
        return to_route('admin.utility.bill.company.countries')->withNotify($notify);
    }

    public function updateCountryStatus($id)
    {
        return Country::changeStatus($id);
    }

    public function all($countryId)
    {
        $country = Country::utilityBill()->findOrFail($countryId);

        $pageTitle  = $country->name . ' Utility Biller Companies';
        $baseQuery  = Company::where('country_id', $country->id)->searchable(['name'])->with('form')->orderBy('id', getOrderBy());

        if (request()->export) {
            return exportData($baseQuery, request()->export, "Company");
        }

        $companies = $baseQuery->paginate(getPaginate());
        $charge    = TransactionCharge::where('slug', "utility_charge")->first();

        return view('admin.company.all', compact('pageTitle', 'companies', 'charge', 'country'));
    }


    public function status($id)
    {
        return Company::changeStatus($id);
    }

    public function fetchCompanies($countryId)
    {
        $country = Country::utilityBill()->findOrFail($countryId);

        $provider = ApiProvider::whereJsonContains('module', 'utility_bill')->where('status->utility_bill', Status::YES)->first();

        if (!$provider) {
            $notify[] = ['error', 'At least one automated utility bill provider integration is required to import biller categories.'];
            return back()->withNotify($notify);
        }

        $pageTitle            = $country->name . ' Supported Biller Companies';
        $existingCompanyNames = Company::pluck('company_id')->toArray();


        try {
            $className  = 'App\\Lib\\UtilityBillProvider\\' . $provider->alias;
            $apiBillers = (new $className())->getBillers($country);
        } catch (Exception $ex) {
            $notify[] = ['error', $ex->getMessage()];
            return back()->withNotify($notify);
        }

        $billers = collect($apiBillers->content ?? []);
        session()->put('billers', $billers);


        return view('admin.utility_bills.fetch_billers', compact('pageTitle', 'billers', 'existingCompanyNames', 'country'));
    }

    public function saveCompanies(Request $request, $countryId)
    {
        $request->validate([
            'biller_ids' => 'required|array|min:1',
        ]);

        $billerArray    = [];
        $sessionBillers = collect(session('billers'))->keyBy('id');
        $country        = Country::utilityBill()->findOrFail($countryId);

        foreach ($request->biller_ids as $billerId) {

            $item = $sessionBillers->get($billerId);

            if (!$item || Company::where('company_id', $item->id)->exists()) continue;

            $category = BillCategory::where('name', $item->type)->first();

            if (!$category) {
                $category             = new BillCategory();
                $category->name       = $item->type;
                $category->created_at = now();
                $category->updated_at = now();
                $category->save();
            }

            $billerArray[] = [
                'name'              => $item->name,
                'currency_code'     => @$item->localTransactionCurrencyCode,
                'category_id'       => $category->id,
                'company_id'        => $item->id,
                'country_id'        => $country->id,
                'service_type'      => @$item->serviceType,
                'minimum_amount'    => $item->minInternationalTransactionAmount ?? null,
                'maximum_amount'    => $item->maxInternationalTransactionAmount ?? null,
                'fixed_charge'      => $item->internationalTransactionFee ?? null,
                'percent_charge'    => $item->localTransactionFeePercentage ?? null,
                'rate'              => @$item->fx->rate ?? 1,
                'denomination_type' => @$item->denominationType ?? 'RANGE',
                'fixed_amounts'     => json_encode(@$item->internationalFixedAmounts ?? []),
                'created_at'        => now(),
                'updated_at'        => now(),
            ];
        }

        Company::insert($billerArray);

        session()->forget('billers');

        $notify[] = ['success', 'The ' . count($billerArray) . ' billers company added successfully'];
        return to_route('admin.utility.bill.company.all', $countryId)->withNotify($notify);
    }

    public function save(Request $request, $id = 0)
    {
        $imageValidation = $id ? 'nullable' : 'required';

        $request->validate([
            'name'           => 'required|max:255|unique:companies,name,' . $id,
            'fixed_charge'   => 'nullable|numeric|min:0',
            'percent_charge' => 'nullable|numeric|min:0',
            'minimum_amount' => 'nullable|numeric|min:0',
            'maximum_amount' => 'nullable|numeric|min:0|gt:minimum_amount',
            'image'          => [$imageValidation, 'image', new FileTypeValidate(['jpg', 'jpeg', 'png'])],
        ]);

        $utility  = Company::findOrFail($id);
        $notify[] = ['success', 'Utility bill setting updated successfully'];


        if ($request->hasFile('image')) {
            try {
                $old            = $utility->image;
                $utility->image = fileUploader($request->image, getFilePath('utility'), getFileSize('utility'), $old);
            } catch (\Exception $exp) {
                $notify[] = ['error', 'Couldn\'t upload your image'];
                return back()->withNotify($notify);
            }
        }

        $utility->name           = $request->name;
        $utility->fixed_charge   = $request->fixed_charge ?? null;
        $utility->percent_charge = $request->percent_charge ?? null;
        $utility->minimum_amount = $request->minimum_amount ?? null;
        $utility->maximum_amount = $request->maximum_amount ?? null;
        $utility->save();

        return back()->withNotify($notify);
    }
}
