<?php

namespace App\Traits;


use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Lib\FormProcessor;
use App\Models\Category;
use App\Models\EducationFee;
use App\Models\Form;
use App\Models\Institution;
use App\Models\TransactionCharge;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;

trait EducationFeeOperation
{

    public function create()
    {
        $pageTitle  = 'Education Fee';
        $user       = auth()->user();
        $view       = 'Template::user.education_fee.create';
        $categories = Category::active()->with('institute', function ($q) {
            $q->active()->with('form');
        })->get();
        $educationCharge = TransactionCharge::where('slug', 'education_charge')->first();
        $institutions    = Institution::active()->with('form')->get();

        return responseManager("education_fee", $pageTitle, 'success', compact('view', 'pageTitle', 'categories', 'educationCharge', 'institutions'), ['institutions']);
    }

    public function form($id)
    {
        $form = Form::where('id', $id)->first();
        if (!$form) {
            $notify[] = 'Education Fee Form not found';
            return apiResponse('not_found', 'error', $notify);
        }
        $content = view('Template::user.education_fee.form', compact('form'))->render();

        $message[] = 'Education Fee Form';
        return apiResponse('success', 'success', $message, ['content' => $content]);
    }


    public function store(Request $request)
    {
        $user = auth()->user();
        $fee  = Institution::active()->where('id', $request->institution_id)->first();

        if (!$fee) {
            $notify[] = 'Sorry, Institution not found';
            return apiResponse("validation_error", "error", $notify);
        }

        $formData       = $fee->form->form_data;
        $formProcessor  = new FormProcessor();
        $validationRule = $formProcessor->valueValidation($formData);
        $userData       = $formProcessor->processFormData($request, $formData);

        $validator = Validator::make($request->all(), array_merge([
            'amount'         => 'required|gt:0',
            'institution_id' => 'required',
        ], $validationRule));

        if ($validator->fails()) {
            return apiResponse("validation_error", "error", $validator->errors()->all());
        }


        $educationCharge = TransactionCharge::where('slug', 'education_charge')->first();
        if (!$educationCharge) {
            $notify[] = "Sorry, Transaction charge not found";
            return apiResponse("validation_error", "error", $notify);
        }

        if ($request->amount < $educationCharge->min_limit || $request->amount > $educationCharge->max_limit) {
            $notify[] = "Please Follow the education fee limit";
            return apiResponse("validation_error", "error", $notify);
        }

        $dailyTransaction = EducationFee::where('user_id', $user->id)
            ->whereDate('created_at', Carbon::today())
            ->sum('amount');

        if ($educationCharge->daily_limit != -1 && ($dailyTransaction + $request->amount) > $educationCharge->daily_limit) {
            $notify[] = 'Your daily education fee limit exceeded';
            return apiResponse("validation_error", "error", $notify);
        }

        $monthlyTransaction = EducationFee::where('user_id', $user->id)
            ->whereMonth('created_at', Carbon::now()->month)
            ->whereYear('created_at', Carbon::now()->year)
            ->sum('amount');

        if ($educationCharge->monthly_limit != -1 && ($monthlyTransaction + $request->amount) > $educationCharge->monthly_limit) {
            $notify[] = 'Your monthly education fee limit exceeded';
            return apiResponse("validation_error", "error", $notify);
        }


        if ($fee->fixed_charge > 0 || $fee->percent_charge > 0) {
            $fixedCharge   = $fee->fixed_charge;
            $percentCharge = $request->amount * $fee->percent_charge / 100;
            $totalCharge   = $fixedCharge + $percentCharge;
        } else {
            $fixedCharge   = $educationCharge->fixed_charge;
            $percentCharge = $request->amount * $educationCharge->percent_charge / 100;
            $totalCharge   = $fixedCharge + $percentCharge;
        }

        $cap = $educationCharge->cap;

        if ($cap != -1 && $totalCharge > $cap) {
            $totalCharge = $cap;
        }


        $totalAmount = getAmount($request->amount + $totalCharge);

        if ($totalAmount > $user->balance) {
            $notify[] = 'Sorry! Insufficient balance';
            return apiResponse("validation_error", "error", $notify);
        }

        $details  = [
            'amount'         => $request->amount,
            'total_amount'   => $totalAmount,
            'total_charge'   => $totalCharge,
            'institution_id' => $fee->id,
            'user_data'      => $userData
        ];


        return storeAuthorizedTransactionData("education_fee", $details);
    }

    public function history()
    {
        $pageTitle = 'Education Fee History';
        $user      = auth()->user();
        $view      = 'Template::user.education_fee.index';

        $educationFees = EducationFee::where('user_id', $user->id)
            ->with(['institution'])
            ->latest()
            ->searchable(['trx', 'institution:name'])
            ->paginate(getPaginate());

        return responseManager("education_fee_history", $pageTitle, 'success', compact('view', 'pageTitle', 'user', 'educationFees'));
    }


    public function details($id)
    {
        $pageTitle    = 'Education Fee Details';
        $user         = auth()->user();
        $view         = 'Template::user.education_fee.details';
        $educationFee = EducationFee::where('id', $id)->where('user_id', $user->id)->first();

        if (!$educationFee) {
            $notify = "The education fee transaction is not found";
            return responseManager('not_fund', $notify);
        }
        return responseManager("education_fee_details", $pageTitle, 'success', compact('view', 'pageTitle', 'user', 'educationFee'));
    }


    public function pdf($id)
    {
        $pageTitle   = "Utility Bill Receipt";
        $user        = auth()->user();
        $educationFee = EducationFee::where('id', $id)->where('user_id', $user->id)->first();
        if (!$educationFee) {
            $notify ="The education fee transaction is not found";
            return responseManager('not_fund',$notify);
        }
 
        $activeTemplateTrue = activeTemplate(true);
        $activeTemplate     = activeTemplate();

        $pdf      = Pdf::loadView($activeTemplate.'.user.education_fee.pdf', compact('pageTitle', 'educationFee', 'user','activeTemplateTrue', 'activeTemplate'));
        $fileName = "Education Fee Receipt - " . $educationFee->trx . ".pdf";
        return $pdf->download($fileName);
    }


    
}
