<?php

namespace App\Traits;

use App\Constants\Status;
use App\Models\MoneyRequest;
use App\Models\TransactionCharge;
use App\Models\User;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

trait RequestMoneyOperation
{

    public function create()
    {
        $pageTitle          = 'Request Money';
        $user               = auth()->user();
        $view               = 'Template::user.request_money.create';
        $latestRequestMoney = MoneyRequest::latest()->where('sender_id', $user->id)->groupBy('sender_id')->with('requestReceiver')->take(3)->get();
        $pendingRequest     = MoneyRequest::latest()->where('receiver_id', $user->id)->where('status', Status::PENDING)->count();
        $charge             = TransactionCharge::where('slug', 'send_money')->first();

        return responseManager("request_money", $pageTitle, 'success', compact('view', 'pageTitle', 'latestRequestMoney', 'charge', 'pendingRequest'));
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

        $sender   = auth()->user();
        $receiver = findUserWithUsernameOrMobile("The request received user not found");

        if ($sender->id === $receiver->id) {
            $notify[] = 'You cannot request money from yourself';
            return apiResponse("cannot_request_from_self", "error", $notify);
        }

        $details = [
            'sender_id'   => $sender->id,
            'receiver_id' => $receiver->id,
            'amount'      => $request->amount,
            'note'        => $request->note ?? null,
        ];

        return storeAuthorizedTransactionData('request_money', $details);
    }

    public function history()
    {
        $pageTitle     = 'Request Money History';
        $user          = auth()->user();
        $view          = 'Template::user.request_money.index';
        $requestMoneys = MoneyRequest::where('sender_id', $user->id)
            ->with(['requestReceiver'])
            ->latest()
            ->searchable(['requestReceiver:mobile'])
            ->paginate(getPaginate());

        return responseManager("request_money_history", $pageTitle, 'success', compact('view', 'pageTitle', 'requestMoneys', 'user'));
    }

    public function details($id)
    {
        $pageTitle    = 'Request Money Details';
        $user         = auth()->user();
        $view         = 'Template::user.request_money.details';
        $requestMoney = MoneyRequest::where('id', $id)->where('sender_id', $user->id)->first();

        if (!$requestMoney) {
            $notify = "The request money transaction is not found";
            return responseManager('not_fund', $notify);
        }

        return responseManager("request_money_details", $pageTitle, 'success', compact('view', 'pageTitle', 'user', 'requestMoney'));
    }

    public function requestHistory()
    {
        $user            = auth()->user();
        $pageTitle       = "Received Request Money History";
        $view            = 'Template::user.request_money.received_history';
        $requestedMoneys = MoneyRequest::where('receiver_id', $user->id)
            ->with(['requestSender'])
            ->latest()
            ->searchable(['trx', 'requestSender:mobile'])
            ->paginate(getPaginate());

        return responseManager("request_money_history", $pageTitle, 'success', compact('view', 'pageTitle', 'requestedMoneys', 'user'));
    }


    public function requestDetails($id)
    {

        $pageTitle          = 'Accept Request Money';
        $user               = auth()->user();
        $view               = 'Template::user.request_money.received_details';
        $requestMoney       = MoneyRequest::where('id', $id)->where('receiver_id', $user->id)->first();
        $requestMoneyCharge = TransactionCharge::where('slug', 'send_money')->first();

        if (!$requestMoney) {
            return responseManager('error', 'Transaction not found', 'error');
        }

        return responseManager("request_money_received", $pageTitle, 'success', compact('view', 'pageTitle', 'user', 'requestMoney', 'requestMoneyCharge'));
    }

    public function requestStore(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'remark' => 'required|in:' . implode(",", getOtpRemark()),
            ...getOtpValidationRules()
        ]);

        if ($validator->fails()) {
            return apiResponse('validation_error', 'error', $validator->errors()->all());
        }

        $receiver     = auth()->user();
        $moneyRequest = MoneyRequest::where('id', $id)
            ->where('receiver_id', $receiver->id)
            ->where('status', Status::PENDING)
            ->first();

        if (!$moneyRequest) {
            $notify[] = 'The money request is not found';
            return apiResponse("not_found", "error", $notify);
        }

        $sender = User::active()->where('id', $moneyRequest->sender_id)->first();

        if (!$sender) {
            $notify[] = 'The request money send user currently does not exist';
            return apiResponse('user_not_found', 'error', $notify);
        }

        $requestedMoneyCharge = TransactionCharge::where('slug', 'send_money')->first();

        if (!$requestedMoneyCharge) {
            $notify[] = 'Transaction charge not found';
            return apiResponse('transaction_charge_not_found', 'error', $notify);
        }

        if ($requestedMoneyCharge->min_limit > $moneyRequest->amount) {
            $message = 'The amount must be at least ' . showAmount($requestedMoneyCharge->min_limit);
            return apiResponse('invalid_amount', 'error', [$message]);
        }

        if ($requestedMoneyCharge->max_limit < $moneyRequest->amount) {
            $message = 'The amount must not exceed ' . showAmount($requestedMoneyCharge->max_limit);
            return apiResponse('invalid_amount', 'error', [$message]);
        }

        $fixedCharge = $requestedMoneyCharge->fixed_charge;
        $totalCharge = ($moneyRequest->amount * $requestedMoneyCharge->percent_charge / 100) + $fixedCharge;
        $cap         = $requestedMoneyCharge->cap;

        if ($requestedMoneyCharge->cap != -1 && $totalCharge > $cap) {
            $totalCharge = $cap;
        }

        $totalAmount = getAmount($moneyRequest->amount + $totalCharge);

        if ($totalAmount > $receiver->balance) {
            $notify[] = 'Insufficient balance to request money';
            return apiResponse('insufficient_balance', 'error', $notify);
        }

        $dailyTransaction = MoneyRequest::where('sender_id', $receiver->id)
            ->whereDate('created_at', Carbon::today())
            ->sum('amount');


        if ($requestedMoneyCharge->daily_limit != -1 && ($dailyTransaction + $moneyRequest->amount) > $requestedMoneyCharge->daily_limit) {
            $notify[] = 'Your daily request money send limit exceeded';
            return apiResponse('daily_limit', 'error', $notify);
        }

        $monthlyTransaction = MoneyRequest::where('sender_id', $receiver->id)
            ->whereMonth('created_at', Carbon::now()->month)
            ->whereYear('created_at', Carbon::now()->year)
            ->sum('amount');


        if ($requestedMoneyCharge->monthly_limit != -1 && ($monthlyTransaction + $moneyRequest->amount) > $requestedMoneyCharge->monthly_limit) {
            $notify[] = 'Your monthly request money send limit exceeded';
            return apiResponse('monthly_limit', 'error',  $notify);
        }

        $details = [
            'amount'           => $moneyRequest->amount,
            'total_amount'     => $totalAmount,
            'total_charge'     => $totalCharge,
            'money_request_id' => $moneyRequest->id
        ];
        return storeAuthorizedTransactionData('request_money_received', $details);
    }

    public function rejectRequest($id)
    {
        $user = auth()->user();

        $moneyRequest = MoneyRequest::where('id', $id)
            ->where('receiver_id', $user->id)
            ->where('status', Status::PENDING)
            ->first();

        if (!$moneyRequest) {
            return responseManager('error', 'The money request is not found');
        }

        $moneyRequest->status = Status::REJECTED;
        $moneyRequest->save();

        $receiver = $moneyRequest->requestReceiver;
        $sender   = $moneyRequest->requestSender;

        notify($receiver, 'MONEY_REQUEST_REJECT', [
            'amount'    => showAmount($moneyRequest->amount, currencyFormat: false),
            'from_user' => $sender->fullname . ' (' . $sender->username . ')',
            'time'      => now(),
        ]);

        return responseManager("success", "Request rejected successfully", 'success');
    }

    public function requestDetailsView($id)
    {
        $pageTitle          = 'Receive Request Money Details';
        $user               = auth()->user();
        $view               = 'Template::user.request_money.received_details_view';
        $requestMoney       = MoneyRequest::where('id', $id)->where('receiver_id', $user->id)->first();

        if (!$requestMoney) {
            return responseManager('error', 'Transaction not found', 'error');
        }
        return responseManager("request_money_received_details", $pageTitle, 'success', compact('view', 'pageTitle', 'user', 'requestMoney'));
    }


    public function pdf($id)
    {
        $pageTitle = "Request Money Receipt";
        $user      = auth()->user();
        $requestMoney = MoneyRequest::where('id', $id)->where('sender_id', $user->id)->first();
        if (!$requestMoney) {
            $notify = "The request money transaction is not found";
            return responseManager('not_fund', $notify);
        }

        $activeTemplateTrue = activeTemplate(true);
        $activeTemplate     = activeTemplate();
        $pdf      = Pdf::loadView($activeTemplate . '.user.request_money.pdf', compact('pageTitle', 'requestMoney', 'user', 'activeTemplateTrue', 'activeTemplate'));
        $fileName = "Request Money Receipt - " . $requestMoney->id . ".pdf";
        return $pdf->download($fileName);
    }

    public function requestPdf($id)
    {

        $pageTitle = "Receive Request Money Receipt";
        $user      = auth()->user();
        $requestMoney = MoneyRequest::where('id', $id)->where('receiver_id', $user->id)->first();
        if (!$requestMoney) {
            $notify = "The request money transaction is not found";
            return responseManager('not_fund', $notify);
        }

        $activeTemplateTrue = activeTemplate(true);
        $activeTemplate     = activeTemplate();

        $pdf      = Pdf::loadView($activeTemplate . '.user.request_money.request_pdf', compact('pageTitle', 'requestMoney', 'user', 'activeTemplateTrue', 'activeTemplate'));
        $fileName = "Request Money Receipt - " . $requestMoney->id . ".pdf";
        return $pdf->download($fileName);
    }
}
