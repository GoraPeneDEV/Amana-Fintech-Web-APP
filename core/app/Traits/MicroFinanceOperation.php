<?php

namespace App\Traits;

use App\Lib\FormProcessor;
use App\Models\Form;
use App\Models\Microfinance;
use App\Models\Ngo;
use App\Models\TransactionCharge;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

trait MicroFinanceOperation
{


    public function create()
    {
        $pageTitle          = 'Microfinance';
        $user               = auth()->user();
        $view               = 'Template::user.microfinance.create';
        $latestMicrofinance = Microfinance::latest()->where('user_id', $user->id)->groupBy('ngo_id')->take(2)->with('ngo', function ($q) {
            $q->active();
        })->get();
        $microfinanceCharge = TransactionCharge::where('slug', 'microfinance_charge')->first();
        $allNgo             = Ngo::active()->with('form')->get();
        return responseManager("microfinance", $pageTitle, 'success', compact('view', 'pageTitle', 'latestMicrofinance', 'microfinanceCharge', 'allNgo'));
    }


    public function form($id)
    {
        $form = Form::where('id', $id)->first();
        if (!$form) {
            $notify[] = 'Microfinance dynamic data is not properly configured';
            return apiResponse('not_found', 'error', $notify);
        }
        $content = view('Template::user.microfinance.form', compact('form'))->render();

        $message[] = 'Microfinance Fee Form';
        return apiResponse('success', 'success', $message, ['content' => $content]);
    }



    public function store(Request $request)
    {
        $user = auth()->user();
        $finance = Ngo::active()->where('id', $request->ngo_id)->first();
        if (!$finance) {
            $notify[] = 'Sorry, NGO not found';
            return apiResponse("validation_error", "error", $notify);
        }

        $formData       = $finance->form->form_data;
        $formProcessor  = new FormProcessor();
        $validationRule = $formProcessor->valueValidation($formData);
        $userData       = $formProcessor->processFormData($request, $formData);

        $validator = Validator::make($request->all(), array_merge([
            'amount'     => 'required|gt:0',
            'ngo_id'     => 'required',
        ], $validationRule));

        if ($validator->fails()) {
            return apiResponse("validation_error", "error", $validator->errors()->all());
        }

        $microfinanceCharge = TransactionCharge::where('slug', 'microfinance_charge')->first();
        if (!$microfinanceCharge) {
            $notify[] = "Sorry, Transaction charge not found";
            return apiResponse("validation_error", "error", $notify);
        }

        if ($request->amount < $microfinanceCharge->min_limit || $request->amount > $microfinanceCharge->max_limit) {
            $notify[] = "Please Follow the microfinance payment limit";
            return apiResponse("validation_error", "error", $notify);
        }


        $dailyTransaction = Microfinance::where('user_id', $user->id)
            ->whereDate('created_at', Carbon::today())
            ->sum('amount');

        if ($microfinanceCharge->daily_limit != -1 && ($dailyTransaction + $request->amount) > $microfinanceCharge->daily_limit) {
            $notify[] = 'Your daily utility bill limit exceeded';
            return apiResponse("validation_error", "error", $notify);
        }

        $monthlyTransaction = Microfinance::where('user_id', $user->id)
            ->whereMonth('created_at', Carbon::now()->month)
            ->whereYear('created_at', Carbon::now()->year)
            ->sum('amount');

        if ($microfinanceCharge->monthly_limit != -1 && ($monthlyTransaction + $request->amount) > $microfinanceCharge->monthly_limit) {
            $notify[] = 'Your monthly utility bill limit exceeded';
            return apiResponse("validation_error", "error", $notify);
        }


        if ($finance->fixed_charge > 0 || $finance->percent_charge > 0) {
            $fixedCharge   = $finance->fixed_charge;
            $percentCharge = $request->amount * $finance->percent_charge / 100;
            $totalCharge   = $fixedCharge + $percentCharge;
        } else {
            $fixedCharge   = $microfinanceCharge->fixed_charge;
            $percentCharge = $request->amount * $microfinanceCharge->percent_charge / 100;
            $totalCharge   = $fixedCharge + $percentCharge;
        }

        $cap = $microfinanceCharge->cap;

        if ($cap != -1 && $totalCharge > $cap) {
            $totalCharge = $cap;
        }

        $totalAmount   = getAmount($request->amount + $totalCharge);

        if ($totalAmount > $user->balance) {
            $notify[] = 'Sorry! Insufficient balance';
            return apiResponse("validation_error", "error", $notify);
        }

        $details = [
            'amount'       => $request->amount,
            'total_amount' => $totalAmount,
            'total_charge' => $totalCharge,
            'ngo_id'       => $finance->id,
            'user_data'    => $userData,
        ];

        return storeAuthorizedTransactionData("microfinance", $details);
    }



    public function history()
    {
        $pageTitle = 'Microfinance History';
        $user = auth()->user();
        $view = 'Template::user.microfinance.index';

        $microFinances = Microfinance::where('user_id', $user->id)
            ->with(['ngo'])
            ->latest()
            ->searchable(['trx', 'ngo:name'])
            ->paginate(getPaginate());

        return responseManager("microfinance_history", $pageTitle, 'success', compact('view', 'pageTitle', 'microFinances', 'user'));
    }


    public function details($id)
    {
        $pageTitle     = 'Microfinance Details';
        $user          = auth()->user();
        $view          = 'Template::user.microfinance.details';
        $microfinance  = Microfinance::where('id', $id)->where('user_id', $user->id)->first();

        if (!$microfinance) {
            $notify = "The microfinance transaction is not found";
            return responseManager('not_fund', $notify);
        }

        return responseManager("microfinance_details", $pageTitle, 'success', compact('view', 'pageTitle', 'microfinance'));
    }



    public function pdf($id)
    {
        $pageTitle = "Microfinance Receipt";
        $user      = auth()->user();
        $microfinance = Microfinance::where('id', $id)->where('user_id', $user->id)->first();
        if (!$microfinance) {
            $notify = "The microfinance transaction is not found";
            return responseManager('not_fund', $notify);
        }
        
        $activeTemplateTrue = activeTemplate(true);
        $activeTemplate     = activeTemplate();

        $pdf      = Pdf::loadView($activeTemplate.'.user.microfinance.pdf', compact('pageTitle', 'microfinance', 'user','activeTemplateTrue', 'activeTemplate'));
        $fileName = "Microfinance Receipt - " . $microfinance->trx . ".pdf";
        return $pdf->download($fileName);
    }

}
