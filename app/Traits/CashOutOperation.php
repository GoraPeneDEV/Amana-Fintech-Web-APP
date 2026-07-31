<?php

namespace App\Traits;

use App\Models\CashOut;
use App\Models\TransactionCharge;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

trait CashOutOperation
{
    public function create()
    {
        $pageTitle      = 'Cash Out';
        $user           = auth()->user();
        $view           = 'Template::user.cash_out.create';
        $cashOutCharge  = TransactionCharge::where('slug', 'cash_out')->first();
        $latestCashOuts = CashOut::latest()->where('user_id', $user->id)->groupBy('agent_id')->with("receiverAgent")->take(3)->get();
        return responseManager("cash_out", $pageTitle, 'success', compact('view', 'pageTitle', 'cashOutCharge', 'latestCashOuts'));
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'agent'            => 'required',
            'amount'           => 'required|numeric|gt:0',
            'remark'           => 'required|in:' . implode(",", getOtpRemark()),
            ...getOtpValidationRules()
        ]);

        if ($validator->fails()) {
            return apiResponse('validation_error', 'error', $validator->errors()->all());
        }

        $user  = auth()->user();
        $agent = findAgentWithUsernameOrMobile();
        $cashOutCharge = TransactionCharge::where('slug', 'cash_out')->first();

        if (!$cashOutCharge) {
            $notify[] = 'The transaction charge is not found';
            return apiResponse('charge_not_found', 'error', $notify);
        }

        if ($cashOutCharge->min_limit > $request->amount) {
            $notify[] = 'The amount must be greater then' . showAmount($cashOutCharge->min_limit);
            return apiResponse('invalid_amount', 'error', $notify);
        }

        if ($cashOutCharge->max_limit < $request->amount) {
            $notify[] = 'The amount must be smaller then' . showAmount($cashOutCharge->max_limit);
            return apiResponse('invalid_amount', 'error', $notify);
        }

        $fixedCharge = $cashOutCharge->fixed_charge;
        $totalCharge = ($request->amount * $cashOutCharge->percent_charge / 100) + $fixedCharge;
        $cap         = $cashOutCharge->cap;

        if ($cashOutCharge->cap != -1 && $totalCharge > $cap) {
            $totalCharge = $cap;
        }

        //Agent commission
        $fixedCommission   = $cashOutCharge->agent_commission_fixed;
        $percentCommission = $request->amount * $cashOutCharge->agent_commission_percent / 100;

        $totalAmount = getAmount($request->amount + $totalCharge);

        if ($totalAmount > $user->balance) {
            $notify[] = 'Sorry! Insufficient balance';
            return apiResponse("validation_error", "error", $notify);
        }

        $dailyTransaction = CashOut::where('user_id', $user->id)
            ->whereDate('created_at', Carbon::today())
            ->sum('amount');

        if ($cashOutCharge->daily_limit != -1 && ($dailyTransaction + $totalAmount) > $cashOutCharge->daily_limit) {
            $notify[] = 'Your daily cash out limit exceeded';
            return apiResponse("validation_error", "error", $notify);
        }

        $monthlyTransaction = CashOut::where('user_id', $user->id)
            ->whereMonth('created_at', Carbon::now()->month)
            ->whereYear('created_at', Carbon::now()->year)
            ->sum('amount');

        if ($cashOutCharge->monthly_limit != -1 && ($monthlyTransaction + $totalAmount) > $cashOutCharge->monthly_limit) {
            $notify[] = 'Your monthly cash out limit exceeded';
            return apiResponse("validation_error", "error", $notify);
        }

        $totalCommission = getAmount($fixedCommission + $percentCommission);
        $totalCharge     = getAmount($totalCharge);

        $details = [
            'amount'           => $request->amount,
            'total_amount'     => $totalAmount,
            'total_charge'     => $totalCharge,
            'agent_id'         => $agent->id,
            'total_commission' => $totalCommission,
        ];
        return storeAuthorizedTransactionData('cash_out', $details);
    }

    public function history()
    {
        $pageTitle = 'Cash Out History';
        $user = auth()->user();
        $view = 'Template::user.cash_out.index';

        $cashOuts = CashOut::where('user_id', $user->id)
            ->with(['receiverAgent'])
            ->latest()
            ->searchable(['trx', 'receiverAgent:mobile'])
            ->paginate(getPaginate());

        return responseManager("cash_out", $pageTitle, 'success', compact('view', 'pageTitle', 'cashOuts'));
    }

    public function details($id)
    {
        $pageTitle = 'Cash Out Details';
        $user = auth()->user();
        $view = 'Template::user.cash_out.details';
        $cashOut = CashOut::where('id', $id)->where('user_id', $user->id)->first();

        if (!$cashOut) {
            $notify = "The cash out transaction is not found";
            return responseManager('not_fund', $notify);
        }
        return responseManager("cash_out_details", $pageTitle, 'success', compact('view', 'pageTitle', 'cashOut'));
    }

    public function pdf($id)
    {
        $pageTitle = "Cash Out Receipt";
        $user      = auth()->user();
        $cashOut   = CashOut::where('id', $id)->where('user_id', $user->id)->first();
        if (!$cashOut) {
            $notify = "The cash out transaction is not found";
            return responseManager('not_fund', $notify);
        }
        $activeTemplateTrue = activeTemplate(true);
        $activeTemplate     = activeTemplate();

        $pdf      = Pdf::loadView($activeTemplate . '.user.cash_out.pdf', compact('pageTitle', 'cashOut', 'user', 'activeTemplateTrue', 'activeTemplate'));
        $fileName = "Cash Out Receipt - " . $cashOut->trx . ".pdf";
        return $pdf->download($fileName);
    }
}
