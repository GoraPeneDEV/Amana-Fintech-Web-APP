<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\MakePayment;
use App\Models\TransactionCharge;
use Illuminate\Http\Request;

class PaymentController extends Controller
{
    public function history()
    {
        $query  = MakePayment::orderBy('id', getOrderBy());
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
        $pageTitle    = "Payment History";

        if (request()->export) {
            return exportData($query, request()->export, "MakePayment", "A4 landscape");
        }

        return view('admin.payment.history', compact('transactions', 'pageTitle', 'widget'));
    }

    public function chargeSetting()
    {
        $charge    = TransactionCharge::where('slug', "payment_charge")->firstOrFail();
        $pageTitle = "Payment Charge & Limit Setting";
        return view('admin.payment.charge_setting', compact('pageTitle', 'charge'));
    }

    public function updateCharges(Request $request)
    {
        $request->validate([
            'fixed_charge'   => 'required|numeric|gte:0',
            'percent_charge' => 'required|numeric|between:0,100',
            'cap'            => 'required|numeric|gte:-1',
        ]);

        $charge = TransactionCharge::where('slug', 'payment_charge')->first();

        if (!$charge) {
            $charge       = new TransactionCharge();
            $charge->slug = "payment_charge";
        }

        $charge->percent_charge = $request->percent_charge ?? 0;
        $charge->fixed_charge   = $request->fixed_charge ?? 0;
        $charge->cap            = $request->cap ?? 0;
        $charge->save();

        $notify[] = ['success', 'Limit & charge updated successfully'];
        return back()->withNotify($notify);
    }
}
