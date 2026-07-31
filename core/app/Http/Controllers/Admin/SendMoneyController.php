<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\SendMoney;
use App\Models\TransactionCharge;
use Illuminate\Http\Request;

class SendMoneyController extends Controller
{
    public function history()
    {
        $query  = SendMoney::orderBy('id', getOrderBy());
        $widget = [
            'today'            => (clone $query)->whereDate('created_at', now()->today())->sum('amount'),
            'yesterday'        => (clone $query)->whereDate('created_at', now()->yesterday())->sum('amount'),
            'this_month'       => (clone $query)->whereMonth('created_at', now()->month)->whereYear('created_at', now()->year)->sum('amount'),
            'today_charge'     => (clone $query)->whereDate('created_at', now()->today())->sum('charge'),
            'yesterday_charge' => (clone $query)->whereDate('created_at', now()->yesterday())->sum('charge'),
            'this_month_charge' => (clone $query)->whereBetween('created_at', [now()->startOfMonth(), now()->endOfMonth()])->sum('charge'),
            'all'              => (clone $query)->sum('amount'),
            'all_charge'       => (clone $query)->sum('charge'),
        ];

        $transactions = $query->searchable(['trx', 'user:username'])->dateFilter()->paginate(getPaginate());
        $pageTitle    = "Send Money History";
        if (request()->export) {
            return exportData($query, request()->export, "SendMoney", "A4 landscape");
        }

        return view('admin.send_money.history', compact('transactions', 'pageTitle', 'widget'));
    }

    public function chargeSetting()
    {
        $charge    = TransactionCharge::where('slug', "send_money")->firstOrFail();
        $pageTitle = "Send Money Charge & Limit Setting ";
        return view('admin.send_money.charge_setting', compact('pageTitle', 'charge'));
    }

    public function updateCharges(Request $request)
    {
        $request->validate([
            'min_limit'                    => 'required|numeric|gte:0',
            'max_limit'                    => 'required|numeric|gt:min_limit',
            'fixed_charge'                 => 'required|numeric|gte:0',
            'percent_charge'               => 'required|numeric|between:0,100',
            'cap'                          => 'required|numeric|gte:-1',
            'daily_limit'                  => 'required|numeric|gte:-1',
            'monthly_limit'                => 'required|numeric|gte:-1',
            'daily_request_accept_limit'   => 'required|numeric|gte:-1',
            'monthly_request_accept_limit' => 'required|numeric|gte:-1',
        ]);

        if ($request->monthly_limit != -1 && $request->monthly_limit < $request->daily_limit) {
            $notify[] = ['error', 'The daily limit must not exceed the monthly limit.'];
            return back()->withNotify($notify);
        }

        $charge = TransactionCharge::where('slug', 'send_money')->first();
        if (!$charge) {
            $charge       = new TransactionCharge();
            $charge->slug = "send_money";
        }

        $charge->percent_charge               = $request->percent_charge ?? 0;
        $charge->fixed_charge                 = $request->fixed_charge ?? 0;
        $charge->min_limit                    = $request->min_limit ?? 0;
        $charge->max_limit                    = $request->max_limit ?? 0;
        $charge->cap                          = $request->cap ?? 0;
        $charge->monthly_limit                = $request->monthly_limit ?? 0;
        $charge->daily_limit                  = $request->daily_limit ?? 0;
        $charge->daily_request_accept_limit   = $request->daily_request_accept_limit ?? 0;
        $charge->monthly_request_accept_limit = $request->monthly_request_accept_limit ?? 0;
        $charge->save();

        $notify[] = ['success', 'Limit & charge updated successfully'];
        return back()->withNotify($notify);
    }
}
