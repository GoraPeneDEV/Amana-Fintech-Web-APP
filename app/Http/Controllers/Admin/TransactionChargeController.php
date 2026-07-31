<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\TransactionCharge;
use Illuminate\Http\Request;

class TransactionChargeController extends Controller
{
    public function index()
    {
        $pageTitle = "Limit & Charges";
        $charges   = TransactionCharge::searchable(['name'])->get();
        return view('admin.charge.list', compact('pageTitle', 'charges'));
    }

    public function updateCharges(Request $request, $slug)
    {
        $validationRule = [
            'percentage_charge'        => 'numeric|between:0,100',
            'fixed_charge'             => 'numeric|gte:0',
            'cap'                      => 'numeric|gte:-1',
            'min_limit'                => 'numeric|gte:0',
            'max_limit'                => 'numeric|gt:min_limit',
            'monthly_limit'            => 'numeric|gte:-1',
            'daily_limit'              => 'numeric|gte:-1',
            'agent_commission_fixed'   => 'numeric|gte:0',
            'agent_commission_percent' => 'numeric|gte:0',
            'merchant_fixed_charge'    => 'numeric|gte:0',
            'merchant_percent_charge'  => 'numeric|gte:0',
        ];

        if ($slug == 'send_money') {
            $validationRule['daily_request_accept_limit']   = 'numeric';
            $validationRule['monthly_request_accept_limit'] = 'numeric';
        }

        $request->validate($validationRule);

        if ($request->monthly_limit != -1 && $request->monthly_limit < $request->daily_limit) {
            $notify[] = ['error', 'The daily limit must not exceed the monthly limit.'];
            return back()->withNotify($notify);
        }

        $charge = TransactionCharge::where('slug', $slug)->firstOrFail();

        $charge->daily_request_accept_limit   = $request->daily_request_accept_limit ?? 0;
        $charge->monthly_request_accept_limit = $request->monthly_request_accept_limit ?? 0;

        $charge->percent_charge           = $request->percent_charge ?? 0;
        $charge->fixed_charge             = $request->fixed_charge ?? 0;
        $charge->min_limit                = $request->min_limit ?? 0;
        $charge->max_limit                = $request->max_limit ?? 0;
        $charge->cap                      = $request->cap ?? 0;
        $charge->agent_commission_fixed   = $request->agent_commission_fixed ?? 0;
        $charge->agent_commission_percent = $request->agent_commission_percent ?? 0;
        $charge->merchant_fixed_charge    = $request->merchant_fixed_charge ?? 0;
        $charge->merchant_percent_charge  = $request->merchant_percent_charge ?? 0;
        $charge->monthly_limit            = $request->monthly_limit ?? 0;
        $charge->daily_limit              = $request->daily_limit ?? 0;

        $charge->save();

        $notify[] = ['success', 'Limit & charge updated successfully'];
        return back()->withNotify($notify);
    }
}
