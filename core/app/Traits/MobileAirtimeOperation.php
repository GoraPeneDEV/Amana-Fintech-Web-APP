<?php

namespace App\Traits;

use App\Lib\Reloadly;
use App\Models\Country;
use App\Models\Operator;
use App\Models\Topup;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

trait MobileAirtimeOperation
{

    public function create()
    {
        $pageTitle = 'Mobile Recharge';
        $user      = auth()->user();
        $view      = 'Template::user.airtime.create';
        $countries = Country::airtime()->active()->with(['operators' => function ($q) {
            $q->active();
        }])->whereHas('operators', function ($q) {
            $q->active();
        })->get();

        return responseManager("mobile_recharge", $pageTitle, 'success', compact('view', 'pageTitle', 'countries'));
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'country'       => 'required|integer',
            'operator'      => 'required|integer',
            'mobile_number' => 'required|numeric',
            'amount'        => 'required|numeric|gt:0',
            ...getOtpValidationRules()
        ]);

        if ($validator->fails()) {
            return apiResponse("validation_error", "error", $validator->errors()->all());
        }

        $user    = auth()->user();
        $country = Country::active()->find($request->country);

        if (!$country) {
            $notify[] = 'Country not found';
            return apiResponse("country_not_found", "error", $notify);
        }

        $operator = Operator::active()->where('id', $request->operator)->first();

        if (!$operator) {
            $notify[] = 'Invalid operator selected';
            return apiResponse("invalid_operator", "error", $notify);
        }

        if ($operator->denomination_type == 'FIXED') {
            $fixedAmounts = is_array($operator->fixed_amounts)
                ? $operator->fixed_amounts
                : json_decode($operator->fixed_amounts, true);

            if (!in_array($request->amount, $fixedAmounts)) {
                $notify[] = 'Invalid amount selected';
                return apiResponse("invalid_amount", "error", $notify);
            }
        } else {

            $minAmount = $operator->min_amount;
            $maxAmount = $operator->max_amount;

            if ($request->amount < $minAmount) {
                $notify[] = 'Thew amount should be greater than ' . showAmount($minAmount);
                return apiResponse("invalid_amount", "error", $notify);
            }

            if ($request->amount > $maxAmount) {
                $notify[] = 'Amount should be less than ' . showAmount($maxAmount);
                return apiResponse("invalid_amount", "error", $notify);
            }
        }

        if ($request->amount > $user->balance) {
            $notify[] = 'Insufficient balance';
            return apiResponse("insufficient_balance", "error", $notify);
        }

        $details = [
            'amount'             => $request->amount,
            'mobile_number'      => $request->mobile_number,
            'country_iso_name'   => $country->iso_name,
            'dial_code'          => $country->calling_codes[0],
            'operator_unique_id' => $operator->unique_id,
            'operator_id'        => $operator->id
        ];

        return storeAuthorizedTransactionData('mobile_recharge', $details);
    }


    public function history()
    {
        $pageTitle = 'Mobile Recharge History';
        $view      = 'Template::user.airtime.index';
        $topUps    = Topup::where('user_id', auth()->id())
            ->latest('id')
            ->searchable(['trx', 'mobile_number'])
            ->with("operator")
            ->paginate(getPaginate());

        return responseManager("airtime_history", $pageTitle, 'success', compact('view', 'pageTitle', 'topUps'));
    }


    public function numberLookUp(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'mobile_number' => 'required',
            'country'       => 'required'
        ]);

        if ($validator->fails()) {
            return apiResponse("validation_error", "error", $validator->errors()->all());
        }

        $country = Country::where('id', $request->country)->first();

        if (!$country) {
            $notify[] = 'Country not found';
            return apiResponse("not_found", "error", $notify);
        }

        $isoCode = $country->iso_name;

        try {
            $reloadly = new Reloadly();
            $operator = $reloadly->getOperatorByISOAndNumber($isoCode, $request->mobile_number);
            return apiResponse('success', 'success', ['Operator Detected Successfully'], [
                'operator' => $operator,
            ]);
        } catch (\Exception $e) {
            return apiResponse("error", "error", [$e->getMessage()]);
        }
    }

    public function amountValidate(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'amount'   => 'required',
            'operator' => 'required'
        ]);

        if ($validator->fails()) {
            return apiResponse("validation_error", "error", $validator->errors()->all());
        }

        $user     = auth()->user();
        $operator = Operator::active()->where('id', $request->operator)->first();

        if (!$operator) {
            $notify[] = 'Operator not found';
            return apiResponse("not_found", "error", $notify);
        }

        if ($operator->denomination_type == 'FIXED') {
            $fixedAmounts = is_array($operator->fixed_amounts)
                ? $operator->fixed_amounts
                : json_decode($operator->fixed_amounts, true);

            if (!in_array($request->amount, $fixedAmounts)) {
                $notify[] = 'Invalid amount selected';
                return apiResponse("invalid_amount", "error", $notify);
            }
        } else {

            $minAmount = $operator->min_amount;
            $maxAmount = $operator->max_amount;

            if ($request->amount < $minAmount) {
                $notify[] = 'Thew amount should be greater than ' . showAmount($minAmount);
                return apiResponse("invalid_amount", "error", $notify);
            }

            if ($request->amount > $maxAmount) {
                $notify[] = 'Amount should be less than ' . showAmount($maxAmount);
                return apiResponse("invalid_amount", "error", $notify);
            }
        }

        if ($request->amount > $user->balance) {
            $notify[] = 'Insufficient balance';
            return apiResponse("insufficient_balance", "error", $notify);
        }

        return apiResponse('success', 'success', ['Amount Verified Successfully'], [
            'operator' => $operator,
            'amount' => $request->amount,
        ]);
    }
}
