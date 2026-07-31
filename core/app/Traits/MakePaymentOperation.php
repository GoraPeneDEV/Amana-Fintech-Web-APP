<?php

namespace App\Traits;

use App\Models\MakePayment;
use App\Models\Merchant;
use App\Models\TransactionCharge;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

trait MakePaymentOperation
{
    public function create()
    {
        $pageTitle = 'Make Payment';
        $user      = auth()->user();
        $view      = 'Template::user.make_payment.create';
        $latestMakePayments = MakePayment::latest()->where('user_id', auth()->id())->groupBy('merchant_id')->with("merchant")->take(3)->get();
        return responseManager("make_payment", $pageTitle, 'success', compact('view', 'pageTitle', 'latestMakePayments'));
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'merchant'         => 'required',
            'amount'           => 'required|numeric|gt:0',
            'remark'           => 'required|in:' . implode(",", getOtpRemark()),
            ...getOtpValidationRules()
        ]);

        if ($validator->fails()) {
            return apiResponse('validation_error', 'error', $validator->errors()->all());
        }

        $user     = auth()->user();
        $merchant = findMerchantWithUsernameOrMobile();


        $makePaymentCharge = TransactionCharge::where('slug', 'payment_charge')->first();

        if (!$makePaymentCharge) {
            $notify[] = 'Transaction charge not found';
            return apiResponse("not_found_charge", "error", $notify);
        }

        $fixedCharge   = $makePaymentCharge->merchant_fixed_charge;
        $percentCharge = $request->amount * $makePaymentCharge->merchant_percent_charge / 100;
        $totalCharge   = $fixedCharge + $percentCharge;
        $cap           = $makePaymentCharge->cap;

        if ($cap != -1 && $totalCharge > $cap) {
            $totalCharge = $cap;
        }

        if ($request->amount > $user->balance) {
            $notify[] = 'Sorry! Insufficient balance';
            return apiResponse("insufficient", "error", $notify);
        }

        $details = [
            'amount'       => $request->amount,
            'total_charge' => $totalCharge,
            'merchant_id'  => $merchant->id,
        ];

        return storeAuthorizedTransactionData('make_payment', $details);
    }



    public function history()
    {
        $pageTitle = 'Make Payment History';
        $user = auth()->user();
        $view = 'Template::user.make_payment.index';
        $makePayments = MakePayment::where('user_id', $user->id)
            ->with(['merchant'])
            ->latest()
            ->searchable(['trx', 'merchant:mobile'])
            ->paginate(getPaginate());

        return responseManager("make_payment_history", $pageTitle, 'success', compact('view', 'pageTitle', 'makePayments', 'user'));
    }

    public function details($id)
    {

        $pageTitle = 'Make Payment Details';
        $user = auth()->user();
        $view = 'Template::user.make_payment.details';
        $makePayment = MakePayment::where('id', $id)->where('user_id', $user->id)->first();
        if (!$makePayment) {
            $notify = "The make payment transaction is not found";
            return responseManager('not_fund', $notify);
        }

        return responseManager("make_payment_details", $pageTitle, 'success', compact('view', 'pageTitle', 'makePayment'));
    }


    public function pdf($id)
    {
        $pageTitle   = "Make Payment Receipt";
        $user        = auth()->user();
        $makePayment = MakePayment::where('id', $id)->where('user_id', $user->id)->first();

        if (!$makePayment) {
            $notify = "The make payment transaction is not found";
            return responseManager('not_fund', $notify);
        }

        $activeTemplateTrue = activeTemplate(true);
        $activeTemplate     = activeTemplate();

        $pdf      = Pdf::loadView($activeTemplate . '.user.make_payment.pdf', compact('pageTitle', 'makePayment', 'user', 'activeTemplateTrue', 'activeTemplate'));
        $fileName = "Make Payment Receipt - " . $makePayment->trx . ".pdf";
        return $pdf->download($fileName);
    }
}
