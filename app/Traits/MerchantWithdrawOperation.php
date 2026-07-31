<?php

namespace App\Traits;

use App\Lib\FormProcessor;
use App\Models\WithdrawMethod;
use App\Models\WithdrawSaveAccount;
use Illuminate\Http\Request;

trait MerchantWithdrawOperation
{
    public function accountSetting()
    {
        $pageTitle       = "Withdrawal Methods Setting";
        $withdrawMethods = WithdrawMethod::orderBy('name')->active()->get();
        return view('Template::merchant.withdraw.method_list', compact('pageTitle', 'withdrawMethods'));
    }

    public function saveAccount($methodId)
    {
        $withdrawMethod = WithdrawMethod::active()->where('id', $methodId)->with('form')->firstOrFailWithApi("Withdraw method");
        $pageTitle      = "Saved Account | " . $withdrawMethod->name;
        $savedAccounts  = $withdrawMethod->saveAccounts()->where('merchant_id', merchant()->id)->get();
        $view           = "Template::merchant.withdraw.account_save";

        return responseManager("save_account", $pageTitle, 'success', compact('view', 'pageTitle', 'savedAccounts', 'withdrawMethod'));
    }

    public function saveAccountData(Request $request, $id = 0)
    {
        $request->validate([
            'name'      => 'required|string|max:40',
            'method_id' => 'required',
        ]);

        $method  = WithdrawMethod::active()->where('id', $request->method_id)->firstOrFailWithApi("withdraw method");

        $saveAccountDataExists = WithdrawSaveAccount::where('merchant_id', merchant()->id)
            ->where('withdraw_method_id', $method->id)
            ->where('name', $request->name)
            ->whereNot('id', $id)
            ->exists();

        if ($saveAccountDataExists) {
            $notify = 'Withdraw save account already exists';
            return responseManager('validation_error', $notify, 'error');
        }

        $formData           = @$method->form->form_data ?? [];
        $formProcessor      = new FormProcessor();
        $validationRule     = $formProcessor->valueValidation($formData, true);
        $request->validate($validationRule);
        $withdrawMethodData = $formProcessor->processFormData($request, $formData);

        if (empty($withdrawMethodData)) {
            $notify[] = 'Withdraw method data not found';
            return apiResponse('not_found', 'error', $notify);
        }

        if ($id) {
            $saveAccount = WithdrawSaveAccount::where('merchant_id', merchant()->id)->where('withdraw_method_id', $method->id)->where('id', $id)->firstOrFailWithApi("save account");
            $message     = 'Withdraw save account has been updated successfully';
        } else {
            $saveAccount = new WithdrawSaveAccount();
            $message     = 'Withdraw save account has been added successfully';
        }

        $saveAccount->merchant_id        = merchant()->id;
        $saveAccount->name               = $request->name;
        $saveAccount->withdraw_method_id = $method->id;
        $saveAccount->data               = $withdrawMethodData;
        $saveAccount->save();

        $notify[] = $message;
        return responseManager('success', $message, 'success');
    }

    public function editAccount($id)
    {
        $accountData  = WithdrawSaveAccount::where('merchant_id', merchant()->id)->where('id', $id)->firstOrFailWithApi("save account");

        $withdrawMethod = $accountData->withdrawMethod;
        $pageTitle      = "Edit Account - " . $accountData->name;
        $view           = "Template::merchant.withdraw.account_edit";

        return responseManager('save_account', $pageTitle, 'success', compact('view', 'pageTitle', 'withdrawMethod', 'accountData'));
    }

    public function getAccount($id)
    {
        $accountData  = WithdrawSaveAccount::with('withdrawMethod')->where('merchant_id', merchant()->id)->where('id', $id)->firstOrFailWithApi("save account");
        return apiResponse('save_account', 'success', [], ['saveAccount' => $accountData]);
    }

    public function deleteAccount($id)
    {
        $accountData = WithdrawSaveAccount::where('merchant_id', merchant()->id)->where('id', $id)->firstOrFailWithApi("save account");

        $accountData->delete();

        $notify = "The save account deleted successfully";

        return responseManager('not_found', $notify, 'success');
    }
}
