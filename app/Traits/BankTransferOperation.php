<?php

namespace App\Traits;

use App\Lib\FormProcessor;
use App\Models\Bank;
use App\Models\BankTransfer;
use App\Models\TransactionCharge;
use App\Models\UserBank;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use Illuminate\Http\Request;

trait BankTransferOperation
{

    public function create()
    {
        $pageTitle          = 'Bank Transfer';
        $user               = auth()->user();
        $view               = 'Template::user.bank_transfer.create';
        $bankTransferCharge = TransactionCharge::where('slug', 'bank_transfer')->first();
        $savedBanks         = UserBank::where('user_id', $user->id)->with('bank', function ($q) {
            $q->active()->with('form');
        })->latest()->get();

        $allBank = Bank::active()->with('form')->get();
        return responseManager("bank_transfer", $pageTitle, 'success', compact('view', 'pageTitle', 'bankTransferCharge', 'savedBanks', 'allBank'));
    }


    public function account(Request $request)
    {
        $request->validate([
            'bank_id'        => 'required',
            'account_holder' => 'required',
            'account_number' => 'required',
        ]);

        $user = auth()->user();
        $bank = Bank::active()->where('id', $request->bank_id)->first();

        if (!$bank) {
            return responseManager("error", "The bank not found");
        }
        
        $existsCheck = UserBank::where('user_id',$user->id)->where('bank_id', $bank->id)->where('account_holder', $request->account_holder)->where('account_number', $request->account_number)->exists();
        
        if($existsCheck){
            return responseManager("error", "This bank account already saved");
        }

        $account                 = new UserBank();
        $account->user_id        = $user->id;
        $account->bank_id        = $request->bank_id;
        $account->account_holder = $request->account_holder;
        $account->account_number = $request->account_number;
        $account->save();

        return responseManager("success", "The account saved successfully", 'success');
    }

    public function accountDetails($bankId)
    {

        $bank       = Bank::where('id', $bankId)->active()->with('form')->first();
        if (!$bank) {
            $notify[] = 'The bank is not found';
            return apiResponse("error", "error", $notify);
        }

        $userBankId = request()->user_bank_id ?? 0;

        if ($userBankId) {
            $account    = UserBank::with(['bank'])->where('bank_id', $bank->id)->where('id', $userBankId)->where('user_id', auth()->id())->first();
            if (!$account) {
                $notify[] = 'The saved bank account not found';
                return apiResponse("error", "error", $notify);
            }
        } else {
            $account = null;
        }

        $content   = view('Template::user.bank_transfer.account_details', compact('account', 'bank'))->render();
        $message[] = 'Bank Transfer Account Details';

        return apiResponse('success', 'success', $message, ['content' => $content]);
    }

    public function deleteBank($id)
    {
        $account = UserBank::where('id', $id)->where('user_id', auth()->id())->first();
        if (!$account) {
            return responseManager("error", "The saved bank account not found");
        }
        $account->delete();
        return responseManager("success", "Account deleted successfully", 'success');
    }


    public function store(Request $request)
    {

        $request->validate([
            'account_number' => 'required',
            'account_holder' => 'required',
            'bank_id'        => 'required|integer',
            'user_bank_id'   => 'nullable|integer',
            'amount'         => 'required|numeric|gt:0',
        ], [
            'bank_id.required' => 'Please select a bank',
        ]);


        $bank = Bank::where('id', $request->bank_id)->active()->with('form')->first();
        $user = auth()->user();

        if (!$bank) {
            $notify[] = 'The bank is not found';
            return apiResponse("error", "error", $notify);
        }

        $userBankId = request()->user_bank_id ?? 0;

        if ($userBankId) {
            $userBank = UserBank::with(['bank'])->where('bank_id', $bank->id)->where('id', $userBankId)->where('user_id', $user->id)->first();
            if (!$userBank) {
                $notify[] = 'The saved bank account not found';
                return apiResponse("error", "error", $notify);
            }
        } else {
            $userBank = null;
        }

        $form = $bank->form;

        if ($form) {
            $formData       = @$form->form_data ?? [];
            $formProcessor  = new FormProcessor();
            $validationRule = $formProcessor->valueValidation($formData);
            $request->validate($validationRule);

            $userData = $formProcessor->processFormData($request, $formData);
        } else {
            $userData = [];
        }

        $accountNumber = $request->account_number;
        $accountHolder = $request->account_holder;

        $bankTransferCharge = TransactionCharge::where('slug', 'bank_transfer')->first();

        if (!$bankTransferCharge) {
            $notify[] = 'Sorry, Transaction charge not found';
            return apiResponse("validation_error", "error", $notify);
        }

        if ($request->amount < $bankTransferCharge->min_limit || $request->amount > $bankTransferCharge->max_limit) {
            $notify[] = 'Please follow the bank transfer limit';
            return apiResponse("validation_error", "error", $notify);
        }

        if ($bank->fixed_charge > 0 || $bank->percent_charge > 0) {
            $fixedCharge   = $bank->fixed_charge;
            $percentCharge = $request->amount * $bank->percent_charge / 100;
            $totalCharge   = $fixedCharge + $percentCharge;
        } else {
            $fixedCharge   = $bankTransferCharge->fixed_charge;
            $percentCharge = $request->amount * $bankTransferCharge->percent_charge / 100;
            $totalCharge   = $fixedCharge + $percentCharge;
        }

        $cap = $bankTransferCharge->cap;

        if ($cap != -1 && $totalCharge > $cap) {
            $totalCharge = $cap;
        }

        $totalAmount = getAmount($request->amount + $totalCharge);

        $dailyTransaction = BankTransfer::where('user_id', $user->id)
            ->whereDate('created_at', Carbon::today())
            ->sum('amount');

        if ($bankTransferCharge->daily_limit != -1 && ($dailyTransaction + $request->amount) > $bankTransferCharge->daily_limit) {
            $notify[] = 'Your daily bank transfer limit exceeded';
            return apiResponse("validation_error", "error", $notify);
        }

        $monthlyTransaction = BankTransfer::where('user_id', $user->id)
            ->whereMonth('created_at', Carbon::now()->month)
            ->whereYear('created_at', Carbon::now()->year)
            ->sum('amount');

        if ($bankTransferCharge->monthly_limit != -1 && ($monthlyTransaction + $request->amount) > $bankTransferCharge->monthly_limit) {
            $notify[] = 'Your monthly bank transfer limit exceeded';
            return apiResponse("validation_error", "error", $notify);
        }

        if ($totalAmount > $user->balance) {
            $notify[] = 'Sorry! Insufficient balance';
            return apiResponse("validation_error", "error", $notify);
        }

        $details = [
            'user_data'      => $userData,
            'amount'         => $request->amount,
            'bank_id'        => $bank->id,
            'account_number' => $accountNumber,
            'account_holder' => $accountHolder,
            'total_amount'   => $totalAmount,
            'total_charge'   => $totalCharge,
        ];

        return storeAuthorizedTransactionData("bank_transfer", $details);
    }

    public function history()
    {
        $pageTitle = 'Bank Transfer History';
        $user      = auth()->user();
        $view      = 'Template::user.bank_transfer.index';

        $bankTransfers = BankTransfer::where('user_id', $user->id)
            ->with(['bank'])
            ->latest()
            ->searchable(['trx', 'bank:name'])
            ->paginate(getPaginate());

        return responseManager("bank_transfer_history", $pageTitle, 'success', compact('view', 'pageTitle', 'bankTransfers'));
    }

    public function details($id)
    {
        $pageTitle    = 'Bank Transfer Details';
        $user         = auth()->user();
        $view         = 'Template::user.bank_transfer.details';
        $bankTransfer = BankTransfer::where('id', $id)->where('user_id', $user->id)->first();

        if (!$bankTransfer) {
            $notify = "The bank transfer transaction is not found";
            return responseManager('not_fund', $notify);
        }

        return responseManager("bank_transfer_details", $pageTitle, 'success', compact('view', 'pageTitle', 'bankTransfer'));
    }

    public function deleteAccount($id)
    {
        $account = UserBank::where('id', $id)->where('user_id', auth()->id())->first();
        if (!$account) {
            return responseManager("error", "Account not found", 'error');
        }
        $account->delete();
        return responseManager("success", "Account deleted successfully", 'success');
    }

    public function pdf($id)
    {
        $pageTitle   = "Bank Transfer Receipt";
        $user        = auth()->user();
        $bankTransfer = BankTransfer::where('id', $id)->where('user_id', $user->id)->first();

        if (!$bankTransfer) {
            $notify = "The bank transfer transaction is not found";
            return responseManager('not_fund', $notify);
        }

        $activeTemplateTrue = activeTemplate(true);
        $activeTemplate     = activeTemplate();

        $pdf      = Pdf::loadView($activeTemplate . '.user.bank_transfer.pdf', compact('pageTitle', 'bankTransfer', 'user', 'activeTemplateTrue', 'activeTemplate'));
        $fileName = "Bank Transfer Receipt - " . $bankTransfer->trx . ".pdf";
        return $pdf->download($fileName);
    }
}
