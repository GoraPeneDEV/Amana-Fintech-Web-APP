<?php

namespace App\Http\Controllers\Api\Agent;

use App\Http\Controllers\Controller;
use App\Models\CashIn;
use App\Models\Transaction;
use App\Models\TransactionCharge;
use App\Models\User;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class CashInController extends Controller
{

    public function checkUser(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'user' => 'required',
        ]);

        if ($validator->fails()) {
            return apiResponse("validation_error", "error", $validator->errors()->all());
        }

        $user = findUserWithUsernameOrMobile();
        
        $notify[] = 'Check User';
        
        return apiResponse("check_user", "success", $notify, [
            'user' => $user
        ]);
    }

    public function cashInForm()
    {
        $notify[]     = "Cash In";
        $cashInCharge = TransactionCharge::where('slug', 'cash_in')->first();
        $agent        = auth()->user();

        $latestCashIn = CashIn::latest('id')->where('agent_id', $agent->id)->groupBy('user_id')->with("user")->take(3)->get();

        return apiResponse("cash_in", "success", $notify, [
            'current_balance'       => $agent->balance,
            'cash_in_charge'        => $cashInCharge,
            'latest_cashin_history' => $latestCashIn,
        ]);
    }
    public function history()
    {
        $notify[]    = "Cash In History";
        $agent       = auth()->user();
        $transaction = CashIn::where('agent_id', $agent->id)->with('user')->apiQuery();

        return apiResponse("cash_in", "success", $notify, [
            'latest_cashin_history' => $transaction,
        ]);
    }
    public function confirmCashIn(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'amount'   => 'required|gt:0',
            'user'     => 'required',
            ...pinValidationRule()
        ]);

        if ($validator->fails()) {
            return apiResponse("validation_error", "error", $validator->errors()->all());
        }

        $agent = auth()->user();

        if (!Hash::check($request->pin, $agent->password)) {
            $notify[] = 'The provided pin does not correct';
            return apiResponse("validation_error", "error", $notify);
        }

        $cashInCharge = TransactionCharge::where('slug', 'cash_in')->first();

        if (!$cashInCharge) {
            $notify[] = "Sorry, Transaction charge not found";
            return apiResponse("validation_error", "error", $notify);
        }

        if ($cashInCharge->daily_limit != -1 && $agent->trxLimit('cash_in')['daily'] > $cashInCharge->daily_limit) {
            $notify[] = "Your daily money in limit exceeded";
            return apiResponse("validation_error", "error", $notify);
        }

        if ($cashInCharge->monthly_limit != -1 && $agent->trxLimit('money_out')['monthly'] > $cashInCharge->monthly_limit) {
            $notify[] = "Your monthly money in limit exceeded";
            return apiResponse("validation_error", "error", $notify);
        }

        $user = User::active()
            ->where(function ($query) use ($request) {
                $query->where('username', $request->user)
                    ->orWhere('mobile', $request->user);
            })->first();

        if (!$user) {
            $notify[] = "User not found";
            return apiResponse("validation_error", "error", $notify);
        }

        if ($request->amount < $cashInCharge->min_limit || $request->amount > $cashInCharge->max_limit) {
            $notify[] = "Please Follow the cash in in limit";
            return apiResponse("validation_error", "error", $notify);
        }

        //Agent commission
        $fixedCommission   = $cashInCharge->agent_commission_fixed;
        $percentCommission = $request->amount * $cashInCharge->agent_commission_percent / 100;
        $totalCommission   = $fixedCommission + $percentCommission;


        if ($request->amount > $agent->balance) {
            $notify[] = "Sorry! Insufficient balance";
            return apiResponse("validation_error", "error", $notify);
        }

        $cap  = $cashInCharge->cap;

        if ($cap != -1 && $totalCommission > $cap) {
            $totalCommission = $cap;
        }

        $amount = $request->amount;
        $trx    = generateUniqueTrxNumber();

        $agent->balance -= $amount;
        $agent->save();

        $agentTrx                = new Transaction();
        $agentTrx->agent_id      = $agent->id;
        $agentTrx->amount        = $amount;
        $agentTrx->post_balance  = $agent->balance;
        $agentTrx->charge        = 0;
        $agentTrx->trx_type      = '-';
        $agentTrx->remark        = 'cash_in';
        $agentTrx->details       = 'Cash in to ' . $user->fullname;
        $agentTrx->trx           = $trx;
        $agentTrx->save();

        $user->balance += $amount;
        $user->save();

        $userTrx                = new Transaction();
        $userTrx->user_id       = $user->id;
        $userTrx->amount        = $amount;
        $userTrx->post_balance  = $user->balance;
        $userTrx->charge        = 0;
        $userTrx->trx_type      = '+';
        $userTrx->remark        = 'cash_in';
        $userTrx->details       = 'Cash in from ' . $agent->fullname;
        $userTrx->trx           = $agentTrx->trx;
        $userTrx->save();

        //To user
        notify($user, 'CASH_IN', [
            'amount'  => showAmount($amount, currencyFormat: false),
            'agent'   => $agent->username,
            'trx'     => $agentTrx->trx,
            'time'    => showDateTime($agentTrx->created_at),
            'balance' => showAmount($user->balance, currencyFormat: false),
        ]);

        //To agent
        notify($agent, 'CASH_IN_AGENT', [
            'amount'  => showAmount($amount, currencyFormat: false),
            'user'    => $user->fullname,
            'trx'     => $agentTrx->trx,
            'time'    => showDateTime($agentTrx->created_at),
            'balance' => showAmount($agent->balance, currencyFormat: false),
        ]);


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
            $agentCommissionTrx->trx          = $agentTrx->trx;
            $agentCommissionTrx->save();

            //Agent commission
            notify($agent, 'CASH_IN_COMMISSION_AGENT', [
                'amount'     => showAmount($amount, currencyFormat: false),
                'commission' => showAmount($totalCommission, currencyFormat: false),
                'trx'        => $agentTrx->trx,
                'time'       => showDateTime($agentTrx->created_at),
                'balance'    => showAmount($agent->balance, currencyFormat: false),
            ]);
        }

        $cashIn                     = new CashIn();
        $cashIn->user_id            = $user->id;
        $cashIn->agent_id           = $agent->id;
        $cashIn->amount             = $amount;
        $cashIn->commission         = $totalCommission;
        $cashIn->user_post_balance  = $user->balance;
        $cashIn->agent_post_balance = $agent->balance;
        $cashIn->user_details       = 'Cash in from ' . $agent->fullname;
        $cashIn->agent_details      = 'Cash in to ' . $user->fullname;
        $cashIn->trx                = $trx;
        $cashIn->save();


        $notify[] = "Cash in successfully";
        return apiResponse("cash_in_done", "success", $notify, [
            'cash_in' => $cashIn->load('user'),
        ]);
    }

    public function details($id)
    {

        $pageTitle = 'Cash In Details';
        $agent     = auth()->user();
        $cashIn    = CashIn::where('id', $id)->where('agent_id', $agent->id)->with('user')->first();

        if (!$cashIn) {
            $notify[] = "The cash in transaction is not found";
            return apiResponse('not_found', 'error', $notify);
        }

        return apiResponse("cash_in_details", "success", [$pageTitle], [
            'cash_in' => $cashIn,
        ]);
    }




    public function pdf($id)
    {

        $pageTitle  = "Cash In Receipt";
        $agent      = auth()->user();
        $cashIn     = CashIn::where('id', $id)->where('agent_id', $agent->id)->first();

        if (!$cashIn) {
            $notify[] = "The cash in transaction is not found";
            return apiResponse('not_found', 'error', $notify);
        }

        $activeTemplateTrue = activeTemplate(true);
        $activeTemplate     = activeTemplate();

        $pdf      = Pdf::loadView($activeTemplate . '.agent.cash_in.pdf', compact('pageTitle', 'cashIn', 'agent', 'activeTemplateTrue', 'activeTemplate'));
        $fileName = "Cash In Receipt - " . $cashIn->trx . ".pdf";
        return $pdf->download($fileName);
    }
}
