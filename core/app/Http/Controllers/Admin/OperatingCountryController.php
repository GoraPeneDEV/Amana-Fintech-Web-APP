<?php

namespace App\Http\Controllers\Admin;

use App\Constants\Status;
use App\Http\Controllers\Controller;
use App\Models\OperatingCountry;
use Illuminate\Http\Request;

class OperatingCountryController extends Controller
{
    public function index()
    {
        $pageTitle    = 'All Operating Country';
        $countries    = OperatingCountry::searchable(['name'])->orderBy('name')->paginate(getPaginate());
        $allCountries = json_decode(file_get_contents(resource_path('views/partials/country.json')));
        return view('admin.operating_country.index', compact('pageTitle', 'countries', 'allCountries'));
    }

    public function save(Request $request, $id = 0)
    {

        $countryData = (array)json_decode(file_get_contents(resource_path('views/partials/country.json')));

        $request->validate([
            'country'             => 'required|unique:operating_countries,code,' . $id . '|in:' . implode(",", array_keys($countryData)),
            'mobile_number_digit' => 'required|integer|gt:0',
        ]);

        $operatingCountry = $countryData[$request->country];

        if ($id) {
            $country      = OperatingCountry::findOrFail($id);
            $notification = 'Operating country updated successfully';
        } else {
            $country            = new OperatingCountry();
            $notification       = 'Operating country added successfully';
            $country->name      = $operatingCountry->country;
            $country->code      = $request->country;
            $country->dial_code = $operatingCountry->dial_code;
        }

        $country->mobile_number_digit = $request->mobile_number_digit;
        $country->save();

        $notify[] = ['success',  $notification];
        return back()->withNotify($notify);
    }

    public function status($id)
    {
        if (OperatingCountry::where('status', Status::ENABLE)->count() <= 1) {
            $notify[] = ['error',  "At least one operating country is required"];
            return back()->withNotify($notify);
        }
        return OperatingCountry::changeStatus($id);
    }
}
