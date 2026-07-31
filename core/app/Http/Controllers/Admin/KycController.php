<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Form;
use App\Lib\FormProcessor;
use Illuminate\Http\Request;

class KycController extends Controller
{
    public function kycType()
    {
        $pageTitle = 'Select User Type for KYC';
        return view('admin.kyc.type', compact('pageTitle'));
    }

    public function setting()
    {
        $pageTitle = 'KYC Setting';
        $type      = request()->type;
        $form      = Form::where('act', $type . '_kyc')->first();
        return view('admin.kyc.setting', compact('pageTitle', 'form', 'type'));
    }

    public function settingUpdate(Request $request, $type)
    {

        $formProcessor       = new FormProcessor();
        $generatorValidation = $formProcessor->generatorValidation();

        $request->validate($generatorValidation['rules'], $generatorValidation['messages']);
        $exist = Form::where('act', $type .'_kyc')->first();
        $formProcessor->generate($type .'_kyc', $exist, 'act');

        $notify[] = ['success', 'KYC data updated successfully'];
        return back()->withNotify($notify);
    }
}
