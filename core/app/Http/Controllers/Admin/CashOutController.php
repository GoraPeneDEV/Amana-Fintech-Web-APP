<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\CashOut;
use App\Models\TransactionCharge;
use Illuminate\Http\Request;

class CashOutController extends Controller
{
    public function history()
    {
        $query  = CashOut::orderBy('id', getOrderBy());
        $widget = [
            'today'             => (clone $query)->whereDate('created_at', now()->today())->sum('amount'),
            'yesterday'         => (clone $query)->whereDate('created_at', now()->yesterday())->sum('amount'),
            'this_month'        => (clone $query)->whereMonth('created_at', now()->month)->whereYear('created_at', now()->year)->sum('amount'),
            'today_charge'      => (clone $query)->whereDate('created_at', now()->today())->sum('charge'),
            'yesterday_charge'  => (clone $query)->whereDate('created_at', now()->yesterday())->sum('charge'),
            'this_month_charge' => (clone $query)->whereBetween('created_at', [now()->startOfMonth(), now()->endOfMonth()])->sum('charge'),
            'all'               => (clone $query)->sum('amount'),
            'all_charge'        => (clone $query)->sum('charge'),
        ];
        $transactions = $query->searchable(['trx', 'user:username'])->dateFilter()->paginate(getPaginate());
        $pageTitle    = "CashOut History";
        if (request()->export) {
            return exportData($query, request()->export, "CashOut", "A4 landscape");
        }

        return view('admin.cashout.history', compact('transactions', 'pageTitle', 'widget'));
    }

    public function chargeSetting()
    {
        $charge    = TransactionCharge::where('slug', "cash_out")->firstOrFail();
        $pageTitle = "CashOut Charge & Limit Setting ";
        return view('admin.cashout.charge_setting', compact('pageTitle', 'charge'));
    }

    public function updateCharges(Request $request)
    {
        $request->validate([
            'min_limit'                => 'required|numeric|gte:0',
            'max_limit'                => 'required|numeric|gt:min_limit',
            'fixed_charge'             => 'required|numeric|gte:0',
            'percent_charge'           => 'required|numeric|between:0,100',
            'cap'                      => 'required|numeric|gte:-1',
            'daily_limit'              => 'required|numeric|gte:-1',
            'monthly_limit'            => 'required|numeric|gte:-1',
            'agent_commission_fixed'   => 'numeric|gte:0',
            'agent_commission_percent' => 'numeric|gte:0',
        ]);

        if ($request->monthly_limit != -1 && $request->monthly_limit < $request->daily_limit) {
            $notify[] = ['error', 'The daily limit must not exceed the monthly limit.'];
            return back()->withNotify($notify);
        }

        $charge = TransactionCharge::where('slug', 'cash_out')->first();

        if (!$charge) {
            $charge       = new TransactionCharge();
            $charge->slug = "cash_out";
        }

        $charge->percent_charge           = $request->percent_charge ?? 0;
        $charge->fixed_charge             = $request->fixed_charge ?? 0;
        $charge->min_limit                = $request->min_limit ?? 0;
        $charge->max_limit                = $request->max_limit ?? 0;
        $charge->cap                      = $request->cap ?? 0;
        $charge->monthly_limit            = $request->monthly_limit ?? 0;
        $charge->daily_limit              = $request->daily_limit ?? 0;
        $charge->agent_commission_fixed   = $request->agent_commission_fixed ?? 0;
        $charge->agent_commission_percent = $request->agent_commission_percent ?? 0;
        $charge->save();


        $notify[] = ['success', 'Limit & charge updated successfully'];
        return back()->withNotify($notify);
    }
}
