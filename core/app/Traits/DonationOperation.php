<?php

namespace App\Traits;

use App\Models\Charity;
use App\Models\Donation;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

trait DonationOperation
{

    public function create()
    {
        $pageTitle       = 'Donation';
        $user            = auth()->user();
        $view            = 'Template::user.donation.create';
        $latestDonation  = Donation::latest()->where('user_id', $user->id)->groupBy('charity_id')->take(2)->with('donationFor')->get();
        $allOrganization = Charity::active()->get();
        
        return responseManager("donation", $pageTitle, 'success', compact('view', 'pageTitle', 'latestDonation', 'allOrganization'));
    }


    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'charity_id'        => 'required|gt:0',
            'name'              => 'required',
            'email'             => 'required|email',
            'reference'         => 'nullable',
            'amount'            => 'required|numeric|gt:0',
            'remark'            => 'required|in:' . implode(",", getOtpRemark()),
            ...getOtpValidationRules()
        ]);

        if ($validator->fails()) {
            return apiResponse('validation_error', 'error', $validator->errors()->all());
        }

        $user = auth()->user();

        $charity = Charity::active()->where('id', $request->charity_id)->first();
        if (!$charity) {
            $notify[] = "Sorry, Charity not found";
            return apiResponse("validation_error", "error", $notify);
        }

        if ($request->amount > $user->balance) {
            $notify[] = "Sorry! Insufficient balance";
            return apiResponse("validation_error", "error", $notify);
        }

        $details = [
            'charity_id'    => $charity->id,
            'name'          => $request->name,
            'email'         => $request->email,
            'reference'     => $request->reference,
            'amount'        => $request->amount,
            'hide_identity' => $request->hide_identity
        ];
        return storeAuthorizedTransactionData('donation', $details);
    }



    public function history()
    {
        $pageTitle = 'Donation History';
        $user = auth()->user();
        $view = 'Template::user.donation.index';

        $donations = Donation::where('user_id', $user->id)
            ->with(['donationFor'])
            ->latest()
            ->searchable(['trx', 'donationFor:name'])
            ->paginate(getPaginate());

        return responseManager("donation_history", $pageTitle, 'success', compact('view', 'pageTitle', 'donations','user'));
        
    }


    public function details($id)
    {

        $pageTitle = 'Donation Details';
        $user      = auth()->user();
        $view      = 'Template::user.donation.details';
        $donation  = Donation::where('id', $id)->where('user_id', $user->id)->first();

        if (!$donation) {
            $notify = "The donation transaction is not found";
            return responseManager('not_fund', $notify);
        }

        return responseManager("donation_details", $pageTitle, 'success', compact('view', 'pageTitle', 'donation'));
    }



    public function pdf($id)
    {
        $pageTitle = "Donation Receipt";
        $user      = auth()->user();
        $donation = Donation::where('id', $id)->where('user_id', $user->id)->first();
        if (!$donation) {
            $notify = "The donation transaction is not found";
            return responseManager('not_fund', $notify);
        }

        $activeTemplateTrue = activeTemplate(true);
        $activeTemplate     = activeTemplate();

        $pdf      = Pdf::loadView($activeTemplate.'.user.donation.pdf', compact('pageTitle', 'donation', 'user','activeTemplateTrue', 'activeTemplate'));
        $fileName = "Donation Receipt - " . $donation->trx . ".pdf";
        return $pdf->download($fileName);
    }





}
