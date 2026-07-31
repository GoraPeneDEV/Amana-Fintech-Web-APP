<?php

namespace App\Traits;

use App\Lib\FormProcessor;
use App\Models\WithdrawMethod;
use App\Models\WithdrawSaveAccount;
use Illuminate\Http\Request;

trait AgentWithdrawOperation
{
    public function accountSetting()
    {
        $pageTitle       = "Withdrawal Methods Setting";
        $withdrawMethods = WithdrawMethod::orderBy('name')->active()->get();

        return view('Template::agent.withdraw.method_list', compact('pageTitle', 'withdrawMethods'));
    }

    public function saveAccount($id)
    {
        $withdrawMethod = WithdrawMethod::active()->where('id', $id)->with('form')->firstOrFailWithApi("Withdraw method");
        $pageTitle      = "Save Account | " . $withdrawMethod->name;
        $savedAccounts  = WithdrawSaveAccount::where('agent_id', agent()->id)->where('withdraw_method_id', $withdrawMethod->id)->get();
        $view           = "Template::agent.withdraw.account_save";

        return responseManager('save_account', $pageTitle, 'success', compact('view', 'pageTitle', 'withdrawMethod', 'savedAccounts'));
    }

    public function saveAccountData(Request $request, $id = 0)
    {
        $request->validate([
            'name'      => 'required|string|max:40',
            'method_id' => 'required',
        ]);

        $method   = WithdrawMethod::active()->where('id', $request->method_id)->firstOrFailWithApi("withdraw method");

        $accountExists = WithdrawSaveAccount::where('agent_id', agent()->id)
            ->where('withdraw_method_id', $method->id)
            ->where('name', $request->name)
            ->where('id', '!=', $id)
            ->exists();

        if ($accountExists) {
            $notify = "Withdraw save account data already exists";
            return responseManager('validation_error', $notify);
        }

        $formData       = @$method->form->form_data ?? [];
        $formProcessor  = new FormProcessor();
        $validationRule = $formProcessor->valueValidation($formData, true);
        $request->validate($validationRule);
        $withdrawMethodData = $formProcessor->processFormData($request, $formData);

        if (empty($withdrawMethodData)) {
            $notify = "Withdraw save account data not found";
            return responseManager('not_found', $notify);
        }

        if ($id) {
            $account = WithdrawSaveAccount::where('agent_id', agent()->id)->where('withdraw_method_id', $method->id)->find($id);
            if (!$account) {
                $notify = "Withdraw save account not found";
                return responseManager('not_found', $notify);
            }
            $message = 'Withdraw save account has been updated successfully';
        } else {
            $account = new WithdrawSaveAccount();
            $message    = 'Withdraw save account has been added successfully';
        }

        $account->agent_id           = agent()->id;
        $account->name               = $request->name;
        $account->withdraw_method_id = $method->id;
        $account->data               = $withdrawMethodData;
        $account->save();

        return responseManager('save_account', $message, 'success', ['saveAccount' => $account]);
    }

    public function editAccount($id)
    {
        $accountData  = WithdrawSaveAccount::where('agent_id', agent()->id)->where('id', $id)->firstOrFailWithApi("save account");

        $withdrawMethod = $accountData->withdrawMethod;
        $form           = $withdrawMethod->form;
        $pageTitle      = "Edit Save Account - " . $accountData->name;
        $view           = "Template::agent.withdraw.account_edit";

        return responseManager('save_account', $pageTitle, 'success', compact('view', 'pageTitle', 'withdrawMethod', 'accountData', 'form'));
    }

    public function getAccount($id)
    {
        $accountData  = WithdrawSaveAccount::where('agent_id', agent()->id)->where('id', $id)->firstOrFailWithApi("save account");
        return apiResponse('save_account', 'success', [], ['saveAccount' => $accountData]);
    }

    public function deleteAccount($id)
    {
        $accountData = WithdrawSaveAccount::where('agent_id', agent()->id)->where('id', $id)->firstOrFailWithApi("save account");

        $accountData->delete();

        $notify = "The save account deleted successfully";

        return responseManager('not_found', $notify, 'success');
    }
}
