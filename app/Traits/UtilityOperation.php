<?php

namespace App\Traits;

use App\Constants\Status;
use App\Models\TransactionCharge;
use Illuminate\Http\Request;
use App\Models\BillCategory;
use App\Models\Company;
use App\Models\Country;
use App\Models\UserCompany;
use App\Models\UtilityBill;
use Barryvdh\DomPDF\Facade\Pdf;
use Exception;

trait UtilityOperation
{
    public function create()
    {
        $pageTitle = 'Utility Bill';
        $view      = 'Template::user.utility_bill.create';

        $billCategory = BillCategory::active()->with("company", function ($q) {
            $q->active();
        })->get();

        $userCompanies = UserCompany::where('user_id', auth()->user()->id)->with('company', function ($q) {
            $q->active();
        })->get();

        $utilityCharge = TransactionCharge::where('slug', 'utility_charge')->first();
        $companies     = Company::active()->with('form')->get();
        $userCompanies = UserCompany::where('user_id', auth()->user()->id)->with('company')->get();
        $counties      = Country::utilityBill()->get();

        return responseManager("utility_bill", $pageTitle, 'success', compact('view', 'pageTitle', 'billCategory', 'utilityCharge', 'companies', 'userCompanies', 'counties', 'userCompanies'), ['companies']);
    }

    public function form()
    {
        $hideFile  = request()->hide_file ?? 'no';
        $content   = view('Template::user.utility_bill.form', compact('hideFile'))->render();
        $message[] = 'Utility Bill Form';

        return apiResponse('success', 'success', $message, ['content' => $content]);
    }





    public function deleteUserCompany($id)
    {
        $user        = auth()->user();
        $userCompany = UserCompany::where('id', $id)->where('user_id', $user->id)->first();

        if (!$userCompany) {
            return responseManager('error', 'The user company not found');
        }

        $userCompany->delete();

        return responseManager("success", "The utility bill account deleted successfully", 'success');
    }

    public function store(Request $request)
    {
        $request->validate([
            'amount'          => 'required',
            'company_id'      => 'required',
            'reference'       => 'required|string|max:255',
        ]);

        $user     = auth()->user();
        $company  = Company::active()->where('id', $request->company_id)->firstOrFailWithApi('Company');
        $uniqueId = null;

        if ($request->user_company_id) {
            $userCompany = UserCompany::with(['company' => function ($q) {
                $q->active()->with('form');
            }])->where('id', $request->user_company_id)
                ->where('user_id', $user->id)
                ->where('company_id', $company->id)
                ->first();

            if (!$userCompany) {
                $notify = 'Sorry, The save account is not found';
                return responseManager('error', $notify);
            }

            $uniqueId = $userCompany->unique_id;
        } else {
            $uniqueId = request()->unique_id;
        }

        if (!$uniqueId) {
            $notify[] = "The unique ID field is required for the processing utility bill payment";
            return apiResponse("validation_error", "error", $notify);
        }

        $reference     = $request->reference;

        if (UtilityBill::where('user_id', $user->id)->where('reference', $reference)->exists()) {
            $notify[] = "The reference number is already exists";
            return apiResponse("validation_error", "error", $notify);
        }

        $utilityCharge = TransactionCharge::where('slug', 'utility_charge')->first();

        if (!$utilityCharge) {
            $notify[] = "Sorry, Transaction charge not found";
            return apiResponse("validation_error", "error", $notify);
        }

        $amountId = 0;
        if ($company->denomination_type == 'RANGE') {

            $minAmount = is_null($company->minimum_amount) ? ($utilityCharge->min_limit) :  $company->minimum_amount;
            $maxAmount = is_null($company->maximum_amount) ? ($utilityCharge->max_limit) :  $company->maximum_amount;

            if ($request->amount < $minAmount) {
                $notify[] = "The minium pay bill amount is " . getAmount($minAmount) . " " . $company->currency_code;
                return apiResponse("validation_error", "error", $notify);
            }

            if ($request->amount > $maxAmount) {
                $notify[] = "The maximum pay bill amount is " . getAmount($maxAmount) . " " . $company->currency_code;
                return apiResponse("validation_error", "error", $notify);
            }
        } else {

            if (!$request->amount_id) {
                $notify[] = "The amount field is required for the processing utility bill payment";
                return apiResponse("validation_error", "error", $notify);
            }
            $amountCheck = collect($company->fixed_amounts)->where('id', $request->amount_id)->first();

            if (!$amountCheck) {
                $notify[] = "Invalid amount is selected";
                return apiResponse("validation_error", "error", $notify);
            }
            $amountId = $amountCheck->id;
        }


        $fixedCharge   = is_null($company->fixed_charge) ?  ($utilityCharge->fixed_charge) :  $company->fixed_charge;
        $percentCharge = is_null($company->percent_charge) ?  $utilityCharge->percent_charge :  $company->percent_charge;

        $percentCharge = $request->amount * $percentCharge / 100;
        $totalCharge   = $fixedCharge + $percentCharge;
        $cap           = $utilityCharge->cap;

        if ($cap != -1 && $totalCharge > $cap) {
            $totalCharge = $cap;
        }

        $totalAmount = getAmount($request->amount + $totalCharge);

        if ($totalAmount > $user->balance) {
            $notify[] = 'Sorry! Insufficient balance';
            return apiResponse("validation_error", "error", $notify);
        }


        $user    = auth()->user();
        $company = Company::where('id', $request->company_id)->first();

        if (!$company) {
            return responseManager('error', 'Company not found');
        }

        $userCompanyExists = UserCompany::where('company_id', $request->company_id)->where('unique_id', $request->unique_id)->where('user_id', $user->id)->exists();

        if (!$userCompanyExists && $request->save_information) {
            $userCompany             = new UserCompany();
            $userCompany->user_id    = $user->id;
            $userCompany->company_id = $company->id;
            $userCompany->unique_id  = $request->unique_id;
            $userCompany->save();
        }

        $details = [
            'amount'       => $request->amount,
            'total_amount' => $totalAmount,
            'total_charge' => $totalCharge,
            'company_id'   => $company->id,
            'unique_id'    => $uniqueId,
            'reference'    => $reference,
            'rate'         => $company->rate,
            'amount_id'    => $amountId
        ];

        return storeAuthorizedTransactionData("utility_bill", $details);
    }

    public function history()
    {
        $pageTitle = 'Utility Bill History';
        $user      = auth()->user();
        $view      = 'Template::user.utility_bill.index';

        $utilityBills = UtilityBill::where('user_id', $user->id)
            ->with(['company'])
            ->latest()
            ->searchable(['trx', 'company:name'])
            ->paginate(getPaginate());

        return responseManager("utility_bill_history", $pageTitle, 'success', compact('view', 'pageTitle', 'user', 'utilityBills'));
    }

    public function details($id)
    {
        $pageTitle   = 'Utility Bill Details';
        $user        = auth()->user();
        $view        = 'Template::user.utility_bill.details';
        $utilityBill = UtilityBill::where('id', $id)->where('user_id', $user->id)->first();

        if (!$utilityBill) {
            $notify = "The utility bill transaction is not found";
            return responseManager('not_fund', $notify);
        }

        if ($utilityBill->status == Status::UTILITY_BILL_PROCESSING) {

            $provider = $utilityBill->apiProvider;

            try {
                $className = 'App\\Lib\\UtilityBillProvider\\' . $provider->alias;
                (new $className())->payBillUpdate($utilityBill);
                $utilityBill = UtilityBill::where('id', $id)->where('user_id', $user->id)->first();
            } catch (Exception $ex) {
            }
        }

        return responseManager("utility_bill_details", $pageTitle, 'success', compact('view', 'pageTitle', 'utilityBill'));
    }

    public function pdf($id)
    {
        $pageTitle   = "Utility Bill Receipt";
        $user        = auth()->user();
        $utilityBill = UtilityBill::where('id', $id)->where('user_id', $user->id)->first();

        if (!$utilityBill) {
            $notify = "The utility bill transaction is not found";
            return responseManager('not_fund', $notify);
        }

        $activeTemplateTrue = activeTemplate(true);
        $activeTemplate     = activeTemplate();

        $pdf      = Pdf::loadView($activeTemplate . '.user.utility_bill.pdf', compact('pageTitle', 'utilityBill', 'user', 'activeTemplateTrue', 'activeTemplate'));
        $fileName = "Utility Bill Receipt - " . $utilityBill->trx . ".pdf";
        return $pdf->download($fileName);
    }

    public function getCompanies(Request $request)
    {

        $request->validate([
            'country_id'  => 'nullable',
            'category_id' => 'required',
        ]);

        $companies = Company::where('country_id', $request->country_id)
            ->where('category_id', $request->category_id)
            ->active()
            ->whereHas('country', function ($q) {
                $q->active();
            })
            ->whereHas('category', function ($q) {
                $q->active();
            })
            ->latest('id')
            ->get();

        return apiResponse('success', 'success', [], ['companies' => $companies]);
    }
}
