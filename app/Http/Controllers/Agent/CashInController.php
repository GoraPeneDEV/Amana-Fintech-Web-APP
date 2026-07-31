<?php

namespace App\Http\Controllers\Agent;

use App\Http\Controllers\Controller;
use App\Models\CashIn;
use App\Models\Transaction;
use App\Models\TransactionCharge;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class CashInController extends Controller
{

    public function create()
    {
        $pageTitle    = 'Cash In';
        $agent         = auth('agent')->user();
        $view         = 'Template::agent.cash_in.create';
        $cashInCharge = TransactionCharge::where('slug', 'cash_in')->first();
        $latestCashIn = CashIn::latest('id')->where('agent_id', $agent->id)->groupBy('user_id')->with("user")->take(3)->get();
        return view($view, compact('pageTitle', 'cashInCharge', 'latestCashIn'));
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'user'   => 'required',
            'amount' => 'required|numeric|gt:0',
            ...pinValidationRule()
        ]);

        if ($validator->fails()) {
            return apiResponse("validation_error", "error", $validator->errors()->all());
        }

        $agent = auth('agent')->user();
        $user = findUserWithUsernameOrMobile();

     
        if (!Hash::check($request->pin, $agent->password)) {
            return apiResponse("error", "error", ["'The PIN doesn\'t match!'"]);
        }

        $amount = $request->amount;
        $cashInCharge = TransactionCharge::where('slug', 'cash_in')->first();

        if (!$cashInCharge) {
            $notify[] = "Sorry, Transaction charge not found";
            return apiResponse("validation_error", "error", $notify);
        }

        $dailyTransaction = CashIn::where('agent_id', $agent->id)
            ->whereDate('created_at', Carbon::today())
            ->sum('amount');

        if ($cashInCharge->daily_limit != -1 && ($dailyTransaction + $amount) > $cashInCharge->daily_limit) {
            $notify[] = 'Your daily cash in limit exceeded';
            return apiResponse("validation_error", "error", $notify);
        }

        $monthlyTransaction = CashIn::where('agent_id', $agent->id)
            ->whereMonth('created_at', Carbon::now()->month)
            ->whereYear('created_at', Carbon::now()->year)
            ->sum('amount');

        if ($cashInCharge->monthly_limit != -1 && ($monthlyTransaction + $amount) > $cashInCharge->monthly_limit) {
            $notify[] = 'Your monthly cash in limit exceeded';
            return apiResponse("validation_error", "error", $notify);
        }
        if ($amount < $cashInCharge->min_limit) {
            $notify[] = "Please follow the minimum cash-in limit.";
            return apiResponse("validation_error", "error", $notify);
        }

        if ($amount > $cashInCharge->max_limit) {
            $notify[] = "Please follow the maximum cash-in limit.";
            return apiResponse("validation_error", "error", $notify);
        }

        if ($amount > $agent->balance) {
            $notify[] = "Sorry! Insufficient balance";
            return apiResponse("validation_error", "error", $notify);
        }



        //Agent commission
        $fixedCommission   = $cashInCharge->agent_commission_fixed;
        $percentCommission = $amount * $cashInCharge->agent_commission_percent / 100;
        $totalCommission   = $fixedCommission + $percentCommission;


        $cap  = $cashInCharge->cap;

        if ($cap != -1 && $totalCommission > $cap) {
            $totalCommission = $cap;
        }

        $trx = generateUniqueTrxNumber();

        $agent->balance -= $amount;
        $agent->save();

        $agentTrx               = new Transaction();
        $agentTrx->agent_id     = $agent->id;
        $agentTrx->amount       = $amount;
        $agentTrx->post_balance = $agent->balance;
        $agentTrx->charge       = 0;
        $agentTrx->trx_type     = '-';
        $agentTrx->remark       = 'cash_in';
        $agentTrx->details      = 'Cash in to ' . $user->fullname;
        $agentTrx->trx          = $trx;
        $agentTrx->save();

        $user->balance += $amount;
        $user->save();

        $userTrx               = new Transaction();
        $userTrx->user_id      = $user->id;
        $userTrx->amount       = $amount;
        $userTrx->post_balance = $user->balance;
        $userTrx->charge       = 0;
        $userTrx->trx_type     = '+';
        $userTrx->remark       = 'cash_in';
        $userTrx->details      = 'Cash in from ' . $agent->fullname;
        $userTrx->trx          = $trx;
        $userTrx->save();

        if ($totalCommission) {
            $agent->balance += $totalCommission;
            $agent->save();

            $agentCommissionTrx               = new Transaction();
            $agentCommissionTrx->agent_id     = $agent->id;
            $agentCommissionTrx->amount       = $totalCommission;
            $agentCommissionTrx->post_balance = $agent->balance;
            $agentCommissionTrx->charge       = 0;
            $agentCommissionTrx->trx_type     = '+';
            $agentCommissionTrx->remark       = 'cash_in_commission';
            $agentCommissionTrx->details      = 'Cash in commission for ' . $user->fullname;
            $agentCommissionTrx->trx          = $trx;
            $agentCommissionTrx->save();

            //Agent commission
            notify($agent, 'CASH_IN_COMMISSION_AGENT', [
                'agent'      => $agent->username,
                'amount'     => showAmount($amount, currencyFormat: false),
                'commission' => showAmount($totalCommission, currencyFormat: false),
                'trx'        => $agentTrx->trx,
                'time'       => showDateTime($agentTrx->created_at),
                'balance'    => showAmount($agent->balance, currencyFormat: false),
            ]);
        }

        $cashIn                      = new CashIn();
        $cashIn->user_id             = $user->id;
        $cashIn->agent_id            = $agent->id;
        $cashIn->amount              = $amount;
        $cashIn->commission          = $totalCommission;
        $cashIn->user_post_balance   = $user->balance;
        $cashIn->agent_post_balance  = $agent->balance;
        $cashIn->user_details        = 'Cash in from ' . $agent->fullname;
        $cashIn->agent_details       = 'Cash in to ' . $user->fullname;
        $cashIn->trx                 = $trx;
        $cashIn->save();

        //To user
        notify($user, 'CASH_IN', [
            'amount'  => showAmount($amount, currencyFormat: false),
            'agent'   => $agent->username,
            'user'    => $user->username,
            'trx'     => $agentTrx->trx,
            'time'    => showDateTime($agentTrx->created_at),
            'balance' => showAmount($user->balance, currencyFormat: false),
        ]);


        $notify[] = 'Cash in successful';
        return apiResponse("cash_in", "success", $notify, [
            'redirect_type' => "new_url",
            'redirect_url'  => route('agent.cash.in.details', $cashIn->id),
        ]);
    }

    public function history()
    {
        $pageTitle = 'Cash In History';
        $agent     = auth('agent')->user();
        $view      = 'Template::agent.cash_in.index';

        $cashIns = CashIn::where('agent_id', $agent->id)
            ->with(['user'])
            ->latest()
            ->searchable(['trx', 'user:mobile'])
            ->paginate(getPaginate());

        return responseManager("cash_in", $pageTitle, 'success', compact('view', 'pageTitle', 'cashIns'));
    }


    public function details($id)
    {

        $pageTitle = 'Cash In Details';
        $agent      = auth('agent')->user();
        $view      = 'Template::agent.cash_in.details';
        $cashIn    = CashIn::where('id', $id)->where('agent_id', $agent->id)->first();

        if (!$cashIn) {
            $notify = "The cash in transaction is not found";
            return responseManager('not_fund', $notify);
        }

        return responseManager("cash_in_details", $pageTitle, 'success', compact('view', 'pageTitle', 'cashIn'));
    }



    public function pdf($id)
    {
        $pageTitle  = "Cash In Receipt";
        $agent      = auth('agent')->user();
        $cashIn     = CashIn::where('id', $id)->where('agent_id', $agent->id)->first();

        if (!$cashIn) {
            $notify = "The cash in transaction is not found";
            return responseManager('not_fund', $notify);
        }

        $pdf      = Pdf::loadView('Template::agent.cash_in.pdf', compact('pageTitle', 'cashIn', 'agent'));
        $fileName = "Cash In Receipt - " . $cashIn->trx . ".pdf";
        return $pdf->download($fileName);
    }
}
