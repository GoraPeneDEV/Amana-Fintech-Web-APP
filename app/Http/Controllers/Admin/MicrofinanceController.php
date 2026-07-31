<?php

namespace App\Http\Controllers\Admin;

use App\Constants\Status;
use App\Http\Controllers\Controller;
use App\Models\Microfinance;
use App\Models\Transaction;
use App\Models\TransactionCharge;
use App\Models\User;
use Illuminate\Http\Request;

class MicrofinanceController extends Controller
{
    public function pending()
    {
        $pageTitle   = 'Pending Microfinance Payment';
        $financeData = $this->microFinanceData("pending");
        $transfers   = $financeData['data'];

        if (request()->export) {
            return exportData($transfers, request()->export, "Microfinance", "A4 landscape");
        }

        $transfers = $transfers->paginate(getPaginate());
        $widget    = $financeData['widget'];

        return view('admin.microfinance.history', compact('pageTitle', 'transfers', 'widget'));
    }

    public function approved()
    {
        $pageTitle   = 'Approved Microfinance Payment';
        $financeData = $this->microFinanceData("approved");
        $transfers   = $financeData['data'];

        if (request()->export) {
            return exportData($transfers, request()->export, "Microfinance", "A4 landscape");
        }

        $transfers = $transfers->paginate(getPaginate());
        $widget    = $financeData['widget'];

        return view('admin.microfinance.history', compact('pageTitle', 'transfers', 'widget'));
    }

    public function rejected()
    {
        $pageTitle   = 'Rejected Microfinance Payment';
        $financeData = $this->microFinanceData("rejected");
        $transfers   = $financeData['data'];

        if (request()->export) {
            return exportData($transfers, request()->export, "Microfinance", "A4 landscape");
        }

        $transfers = $transfers->paginate(getPaginate());
        $widget    = $financeData['widget'];

        return view('admin.microfinance.history', compact('pageTitle', 'transfers', 'widget'));
    }

    public function all()
    {
        $pageTitle   = 'All Microfinance Payment';
        $financeData = $this->microFinanceData();
        $transfers   = $financeData['data'];

        if (request()->export) {
            return exportData($transfers, request()->export, "Microfinance", "A4 landscape");
        }

        $transfers = $transfers->paginate(getPaginate());
        $widget    = $financeData['widget'];

        return view('admin.microfinance.history', compact('pageTitle', 'transfers', 'widget'));
    }

    private function microFinanceData($scope = 'query')
    {
        $widget = [
            'pending'           => Microfinance::pending()->sum('amount'),
            'approved'          => Microfinance::approved()->sum('amount'),
            'rejected'          => Microfinance::rejected()->sum('amount'),
            'all'               => Microfinance::sum('amount'),
            'today_charge'      => Microfinance::approved()->whereDate('created_at', now()->today())->sum('charge'),
            'yesterday_charge'  => Microfinance::approved()->whereDate('created_at', now()->yesterday())->sum('charge'),
            'this_month_charge' => Microfinance::approved()->whereBetween('created_at', [now()->startOfMonth(), now()->endOfMonth()])->sum('charge'),
            'all_charge'        => Microfinance::approved()->sum('charge'),
        ];

        $query     = Microfinance::$scope();
        $transfers = $query->searchable(['user:username', 'ngo:name', 'trx'])->dateFilter()->with('ngo', 'user', 'getTrx')->orderBy('id', getOrderBy());

        return [
            'data'    => $transfers,
            'widget'  => $widget,
        ];
    }

    public function approve($id)
    {
        $microFinance = Microfinance::where('status', Status::PENDING)->findOrFail($id);
        $ngo          = $microFinance->ngo;
        $getTrx       = $microFinance->getTrx;
        $user         = User::findOrFail($microFinance->user_id);

        $microFinance->status = Status::APPROVED;
        $microFinance->save();

        notify($user, 'MICROFINANCE_PAY_APPROVE', [
            'user'         => $user->fullname,
            'amount'       => showAmount($microFinance->amount, currencyFormat: false),
            'charge'       => showAmount($getTrx->charge, currencyFormat: false),
            'organization' => $ngo->name,
            'trx'          => $getTrx->trx,
            'time'         => showDateTime($microFinance->created_at),
            'post_balance' => showAmount($getTrx->post_balance, currencyFormat: false),
        ]);

        $notify[] = ['success', 'Microfinance payment approved successfully'];
        return back()->withNotify($notify);
    }
    public function reject(Request $request, $id)
    {
        $request->validate([
            'message' => 'required',
        ]);

        $microFinance = Microfinance::where('status', Status::PENDING)->findOrFail($id);
        $ngo          = $microFinance->ngo;
        $user         = User::findOrFail($microFinance->user_id);

        $microFinance->status         = Status::REJECTED;
        $microFinance->admin_feedback = $request->message;
        $microFinance->save();

        $user->balance += $microFinance->total;
        $user->save();

        $transaction                = new Transaction();
        $transaction->user_id       = $user->id;
        $transaction->amount        = $microFinance->total;
        $transaction->post_balance  = $user->balance;
        $transaction->charge        = 0;
        $transaction->trx_type      = '+';
        $transaction->remark        = 'reject_microfinance';
        $transaction->details       = 'Rejection of microfinance payment';
        $transaction->trx           = $microFinance->trx;
        $transaction->save();

        notify($user, 'MICROFINANCE_PAY_REJECT', [
            'user'         => $user->fullname,
            'amount'       => showAmount($microFinance->amount, currencyFormat: false),
            'charge'       => showAmount($microFinance->charge, currencyFormat: false),
            'organization' => $ngo->name,
            'trx'          => $microFinance->trx,
            'reason'       => $request->message,
            'time'         => showDateTime($microFinance->created_at),
            'post_balance' => showAmount($user->post_balance, currencyFormat: false),
        ]);

        $notify[] = ['success', 'Microfinance payment has been rejected successfully'];
        return back()->withNotify($notify);
    }

    public function chargeSetting()
    {
        $charge    = TransactionCharge::where('slug', "microfinance_charge")->firstOrFail();
        $pageTitle = "MicroFinance Payment Charge & Limit Setting ";

        return view('admin.microfinance.charge_setting', compact('pageTitle', 'charge'));
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
        ]);

        if ($request->monthly_limit != -1 && $request->monthly_limit < $request->daily_limit) {
            $notify[] = ['error', 'The daily limit must not exceed the monthly limit.'];
            return back()->withNotify($notify);
        }

        $charge = TransactionCharge::where('slug', 'microfinance_charge')->first();

        if (!$charge) {
            $charge       = new TransactionCharge();
            $charge->slug = "microfinance_charge";
        }

        $charge->percent_charge           = $request->percent_charge ?? 0;
        $charge->fixed_charge             = $request->fixed_charge ?? 0;
        $charge->min_limit                = $request->min_limit ?? 0;
        $charge->max_limit                = $request->max_limit ?? 0;
        $charge->cap                      = $request->cap ?? 0;
        $charge->monthly_limit            = $request->monthly_limit ?? 0;
        $charge->daily_limit              = $request->daily_limit ?? 0;
        $charge->save();


        $notify[] = ['success', 'Limit & charge updated successfully'];
        return back()->withNotify($notify);
    }
}
