<?php

namespace App\Traits;

use App\Models\SendMoney;
use App\Models\TransactionCharge;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

trait SendMoneyOperation
{
    public function create()
    {
        $pageTitle       = 'Send Money';
        $user            = auth()->user();
        $view            = 'Template::user.send_money.create';
        $sendMoneyCharge = TransactionCharge::where('slug', 'send_money')->first();
        $latestSendMoney = SendMoney::latest('id')->where('sender_id', $user->id)->groupBy('receiver_id')->with("receiverUser")->take(3)->get();
        return responseManager("send_money", $pageTitle, 'success', compact('view', 'pageTitle', 'sendMoneyCharge', 'latestSendMoney'));
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'user'   => 'required',
            'amount' => 'required|numeric|gt:0',
            'remark' => 'required|in:' . implode(",", getOtpRemark()),
            ...getOtpValidationRules()
        ]);

        if ($validator->fails()) {
            return apiResponse('validation_error', 'error', $validator->errors()->all());
        }

        $sendUser     = auth()->user();
        $receivedUser = findUserWithUsernameOrMobile("The send money receiver not found");

        if ($sendUser->id == $receivedUser->id) {
            $notify[] = 'You cannot send money to yourself';
            return apiResponse('cannot_send_to_self', 'error', $notify);
        }

        $sendMoneyCharge = TransactionCharge::where('slug', 'send_money')->first();

        if (!$sendMoneyCharge) {
            $notify[] = 'Transaction charge not found';
            return apiResponse('transaction_charge_not_found', 'error', $notify);
        }

        if ($sendMoneyCharge->min_limit > $request->amount || $sendMoneyCharge->max_limit < $request->amount) {
            $message = 'Amount must be between ' . showAmount($sendMoneyCharge->min_limit, currencyFormat: false) . ' and ' . showAmount($sendMoneyCharge->max_limit, currencyFormat: false);
            return apiResponse('invalid_amount', 'error', [$message]);
        }

        $fixedCharge = $sendMoneyCharge->fixed_charge;
        $totalCharge = ($request->amount * $sendMoneyCharge->percent_charge / 100) + $fixedCharge;
        $cap         = $sendMoneyCharge->cap;

        if ($sendMoneyCharge->cap != -1 && $totalCharge > $cap) {
            $totalCharge = $cap;
        }

        $totalAmount = getAmount($request->amount + $totalCharge);

        if ($totalAmount > $sendUser->balance) {
            $notify[] = 'Insufficient balance to send money';
            return apiResponse('insufficient_balance', 'error', $notify);
        }

        $dailyTransaction = SendMoney::where('sender_id', $sendUser->id)
            ->whereDate('created_at', Carbon::today())
            ->sum('amount');

        if ($sendMoneyCharge->daily_limit != -1 && ($dailyTransaction + $totalAmount) > $sendMoneyCharge->daily_limit) {
            $notify[] = 'Your daily send money limit exceeded';
            return apiResponse("validation_error", "error", $notify);
        }

        $monthlyTransaction = SendMoney::where('sender_id', $sendUser->id)
            ->whereMonth('created_at', Carbon::now()->month)
            ->whereYear('created_at', Carbon::now()->year)
            ->sum('amount');

        if ($sendMoneyCharge->monthly_limit != -1 && ($monthlyTransaction + $totalAmount) > $sendMoneyCharge->monthly_limit) {
            $notify[] = 'Your monthly send money limit exceeded';
            return apiResponse("validation_error", "error", $notify);
        }

        $details = [
            'amount'       => $request->amount,
            'total_amount' => $totalAmount,
            'total_charge' => $totalCharge,
            'receiver_id'  => $receivedUser->id,
        ];

        return storeAuthorizedTransactionData('send_money', $details);
    }

    public function history()
    {
        $pageTitle = 'Send Money History';
        $user      = auth()->user();
        $view      = 'Template::user.send_money.index';

        $sendMoneys = SendMoney::where('sender_id', $user->id)
            ->with(['receiverUser'])
            ->latest()
            ->searchable(['trx', 'receiverUser:mobile'])
            ->paginate(getPaginate());

        return responseManager("send_money_history", $pageTitle, 'success', compact('view', 'pageTitle', 'sendMoneys'));
    }

    public function details($id)
    {

        $pageTitle = 'Send Money Details';
        $user      = auth()->user();
        $view      = 'Template::user.send_money.details';
        $sendMoney = SendMoney::where('id', $id)->where('sender_id', $user->id)->first();

        if (!$sendMoney) {
            $notify = "The send money transaction is not found";
            return responseManager('not_fund', $notify);
        }

        return responseManager("send_money_details", $pageTitle, 'success', compact('view', 'pageTitle', 'sendMoney'));
    }

    public function pdf($id)
    {
        $pageTitle = "Send Money Receipt";
        $user      = auth()->user();
        $sendMoney = SendMoney::where('id', $id)->where('sender_id', $user->id)->first();

        if (!$sendMoney) {
            $notify = "The send money transaction is not found";
            return responseManager('not_fund', $notify);
        }

        $activeTemplateTrue = activeTemplate(true);
        $activeTemplate     = activeTemplate();

        $pdf      = Pdf::loadView($activeTemplate . '.user.send_money.pdf', compact('pageTitle', 'sendMoney', 'user', 'activeTemplateTrue', 'activeTemplate'));
        $fileName = "Send Money Receipt - " . $sendMoney->trx . ".pdf";
        return $pdf->download($fileName);
    }
}
