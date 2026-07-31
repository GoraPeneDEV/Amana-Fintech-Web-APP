<?php

namespace App\Http\Controllers\Admin;

use App\Constants\Status;
use App\Http\Controllers\Controller;
use App\Models\TransactionCharge;
use App\Models\User;
use App\Models\UtilityBill;
use Illuminate\Http\Request;

class UtilityBillController extends Controller
{

    public function processing()
    {
        $pageTitle = 'Processing Utility Bills';
        $billData  = $this->utilityBillData("processing");
        $bills     = $billData['data'];

        if (request()->export) {
            return exportData($bills, request()->export, "UtilityBill", "A4 landscape");
        }

        $bills  = $bills->paginate(getPaginate());
        $widget = $billData['widget'];

        return view('admin.utility_bills.history', compact('pageTitle', 'bills', 'widget'));
    }

    public function approved()
    {
        $pageTitle    = 'Successful Utility Bills';
        $billData = $this->utilityBillData("approved");
        $bills    = $billData['data'];

        if (request()->export) {
            return exportData($bills, request()->export, "UtilityBill", "A4 landscape");
        }

        $bills   = $bills->paginate(getPaginate());
        $widget = $billData['widget'];

        return view('admin.utility_bills.history', compact('pageTitle', 'bills', 'widget'));
    }

    public function refunded()
    {
        $pageTitle = 'Rejected Utility Bills';
        $billData   = $this->utilityBillData("refunded");
        $bills      = $billData['data'];

        if (request()->export) {
            return exportData($bills, request()->export, "UtilityBill", "A4 landscape");
        }

        $bills  = $bills->paginate(getPaginate());
        $widget = $billData['widget'];

        return view('admin.utility_bills.history', compact('pageTitle', 'bills', 'widget'));
    }

    public function all()
    {
        $pageTitle = 'All Utility Bills';
        $billData   = $this->utilityBillData();
        $bills      = $billData['data'];

        if (request()->export) {
            return exportData($bills, request()->export, "UtilityBill", "A4 landscape");
        }

        $bills = $bills->paginate(getPaginate());
        $widget    = $billData['widget'];

        return view('admin.utility_bills.history', compact('pageTitle', 'bills', 'widget'));
    }

    private function utilityBillData($scope = 'query')
    {
        $widget = [
            'processing'        => UtilityBill::processing()->sum('amount'),
            'approved'          => UtilityBill::approved()->sum('amount'),
            'refunded'          => UtilityBill::refunded()->sum('amount'),
            'all'               => UtilityBill::sum('amount'),
            'today_charge'      => UtilityBill::approved()->whereDate('created_at', now()->today())->sum('charge'),
            'yesterday_charge'  => UtilityBill::approved()->whereDate('created_at', now()->yesterday())->sum('charge'),
            'this_month_charge' => UtilityBill::approved()->whereBetween('created_at', [now()->startOfMonth(), now()->endOfMonth()])->sum('charge'),
            'all_charge'        => UtilityBill::approved()->sum('charge'),
        ];

        $query = UtilityBill::$scope();
        $bills = $query->searchable(['user:username', 'company:name', 'trx'])->dateFilter()->with('company', 'company.category', 'user', 'getTrx')->orderBy('id', getOrderBy());

        return [
            'data'    => $bills,
            'widget'  => $widget,
        ];
    }

    public function approve($id)
    {

        $utilityBill      = UtilityBill::where('status', Status::UTILITY_BILL_PENDING)->findOrFail($id);
        $setupUtilityBill = $utilityBill->company;
        $user             = User::findOrFail($utilityBill->user_id);

        $utilityBill->status = Status::UTILITY_BILL_SUCCESSFUL;
        $utilityBill->save();

        notify($user, 'UTILITY_BILL_APPROVE', [
            'user'         => $user->fullname,
            'amount'       => showAmount($utilityBill->amount, currencyFormat: false),
            'charge'       => showAmount($utilityBill->charge, currencyFormat: false),
            'utility'      => $setupUtilityBill->name,
            'trx'          => $utilityBill->trx,
            'time'         => showDateTime($utilityBill->created_at),
            'post_balance' => showAmount($utilityBill->getTrx->post_balance, currencyFormat: false),
        ]);

        $notify[] = ['success', 'Utility bill has been successfully paid'];
        return back()->withNotify($notify);
    }


    public function chargeSetting()
    {
        $charge    = TransactionCharge::where('slug', "utility_charge")->firstOrFail();
        $pageTitle = "Utility Bills Charge & Limit Setting ";
        return view('admin.utility_bills.charge_setting', compact('pageTitle', 'charge'));
    }

    public function updateCharges(Request $request)
    {
        $request->validate([
            'min_limit'      => 'required|numeric|gte:0',
            'max_limit'      => 'required|numeric|gt:min_limit',
            'fixed_charge'   => 'required|numeric|gte:0',
            'percent_charge' => 'required|numeric|between:0,100',
            'cap'            => 'required|numeric|gte:-1',
        ]);

        if ($request->monthly_limit != -1 && $request->monthly_limit < $request->daily_limit) {
            $notify[] = ['error', 'The daily limit must not exceed the monthly limit.'];
            return back()->withNotify($notify);
        }

        $charge = TransactionCharge::where('slug', 'utility_charge')->first();

        if (!$charge) {
            $charge       = new TransactionCharge();
            $charge->slug = "utility_charge";
        }

        $charge->percent_charge = $request->percent_charge ?? 0;
        $charge->fixed_charge   = $request->fixed_charge ?? 0;
        $charge->min_limit      = $request->min_limit ?? 0;
        $charge->max_limit      = $request->max_limit ?? 0;
        $charge->cap            = $request->cap ?? 0;
        $charge->save();


        $notify[] = ['success', 'Limit & charge updated successfully'];
        return back()->withNotify($notify);
    }
}
