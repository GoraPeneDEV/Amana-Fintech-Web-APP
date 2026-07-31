<?php

namespace App\Http\Controllers\Api\Agent;

use App\Constants\Status;
use App\Http\Controllers\Controller;
use App\Lib\FormProcessor;
use App\Models\AdminNotification;
use App\Models\Transaction;
use App\Models\Withdrawal;
use App\Models\WithdrawMethod;
use App\Models\WithdrawSaveAccount;
use App\Traits\AgentWithdrawOperation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class AgentWithdrawController extends Controller
{
    use AgentWithdrawOperation;

    public function withdrawMethod()
    {
        $agent          = auth()->user();
        $withdrawMethod = WithdrawMethod::active()->with('saveAccounts', function ($query) use ($agent) {
            $query->where('agent_id', $agent->id);
        })->with('form')->get();
        $notify[]       = 'Withdrawals methods';

        return apiResponse("withdraw_methods", "success", $notify, [
            'withdraw_methods' => $withdrawMethod,
            'agent'            => $agent,
            'balance'          => $agent->balance,
        ]);
    }

    public function withdrawStore(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'method_code' => 'required',
            'amount'      => 'required|numeric',
        ]);

        if ($validator->fails()) {
            return apiResponse("validation_error", "error", $validator->errors()->all());
        }

        $agent   = auth()->user();
        $method = WithdrawMethod::where('id', $request->method_code)->active()->first();

        if (!$method) {
            $notify[] = 'Withdraw method not found.';
            return apiResponse("not_found", "error", $notify);
        }

        if ($request->amount < $method->min_limit) {
            $notify[] = 'Your requested amount is smaller than minimum amount';
            return apiResponse("cross_limit", "error", $notify);
        }
        if ($request->amount > $method->max_limit) {
            $notify[] = 'Your requested amount is larger than maximum amount';
            return apiResponse("cross_limit", "error", $notify);
        }

        if ($request->amount > $agent->balance) {
            $notify[] = 'Insufficient balance for withdrawal';
            return apiResponse("insufficient_balance", "error", $notify);
        }

        if ($request->save_account_id) {
            $accountExistsCheck = WithdrawSaveAccount::where('agent_id', agent()->id)->where('withdraw_method_id', $method->id)->where('id', $request->save_account_id)->exists();
            if (!$accountExistsCheck) {
                $notify[] = 'The saved withdraw account is not found';
                return apiResponse("not_found", "error", $notify);
            }
        }

        $charge      = $method->fixed_charge + ($request->amount * $method->percent_charge / 100);
        $afterCharge = $request->amount - $charge;

        if ($afterCharge <= 0) {
            $notify[] = 'Withdraw amount must be sufficient for charges';
            return apiResponse("charge", "error", $notify);
        }

        $finalAmount = $afterCharge * $method->rate;

        $withdraw               = new Withdrawal();
        $withdraw->method_id    = $method->id;   // wallet method ID
        $withdraw->agent_id     = $agent->id;
        $withdraw->amount       = $request->amount;
        $withdraw->currency     = $method->currency;
        $withdraw->rate         = $method->rate;
        $withdraw->charge       = $charge;
        $withdraw->final_amount = $finalAmount;
        $withdraw->after_charge = $afterCharge;
        $withdraw->trx          = getTrx();
        $withdraw->save_account_id = $request->save_account_id ?? 0;
        $withdraw->save();

        $notify[] = 'Withdraw request created';

        return apiResponse("withdraw_request_created", "success", $notify,  [
            'trx'           => $withdraw->trx,
            'withdraw_data' => $withdraw,
            'form'          => $method->form->form_data,
            'authenticator' => (bool) $agent->ts
        ]);
    }

    public function withdrawSubmit(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'trx'      => 'required',
            ...pinValidationRule()
        ]);

        if ($validator->fails()) {
            return apiResponse("validation_error", "error", $validator->errors()->all());
        }

        $agent = auth()->user();

        if (!Hash::check($request->pin, $agent->password)) {
            $notify[] = 'Provided PIN does not correct';
            return apiResponse("validation_error", "error", $notify);
        }

        $withdraw = Withdrawal::with('method', 'user')
            ->where('trx', $request->trx)
            ->where('status', Status::PAYMENT_INITIATE)
            ->orderBy('id', 'desc')
            ->first();

        if (!$withdraw) {
            $notify[] = 'Withdrawal request not found';
            return apiResponse("not_found", "error", $notify);
        }

        $method = $withdraw->method;
        if ($method->status == Status::DISABLE) {
            $notify[] = 'Withdraw method not found.';
            return apiResponse("not_found", "error", $notify);
        }

        $formData       = $method->form->form_data;
        $formProcessor  = new FormProcessor();
        $validationRule = $formProcessor->valueValidation($formData);
        $validator      = Validator::make($request->all(), $validationRule);

        if ($validator->fails()) {
            return apiResponse("validation_error", "error", $validator->errors()->all());
        }

        $agentData = $formProcessor->processFormData($request, $formData);

        if ($agent->ts) {
            if (!$request->authenticator_code) {
                $notify[] = 'Google authentication is required';
                return apiResponse("required", "error", $notify);
            }
            $response = verifyG2fa($agent, $request->authenticator_code);
            if (!$response) {
                $notify[] = 'Wrong verification code';
                return apiResponse("validation_error", "error", $notify);
            }
        }

        if ($withdraw->amount > $agent->balance) {
            $notify[] = 'Your request amount is larger then your current balance';
            return apiResponse("insufficient_balance", "error", $notify);
        }

        $withdraw->status               = Status::PAYMENT_PENDING;
        $withdraw->withdraw_information = $agentData;
        $withdraw->save();

        $agent->balance -= $withdraw->amount;
        $agent->save();

        $transaction               = new Transaction();
        $transaction->agent_id     = $withdraw->agent_id;
        $transaction->amount       = $withdraw->amount;
        $transaction->post_balance = $agent->balance;
        $transaction->charge       = $withdraw->charge;
        $transaction->trx_type     = '-';
        $transaction->details      = 'Withdraw request via ' . $withdraw->method->name;
        $transaction->trx          = $withdraw->trx;
        $transaction->remark       = 'withdraw';
        $transaction->save();

        $adminNotification            = new AdminNotification();
        $adminNotification->user_id   = $agent->id;
        $adminNotification->title     = 'New withdraw request from agent: ' . $agent->username;
        $adminNotification->click_url = urlPath('admin.withdraw.data.details', $withdraw->id);
        $adminNotification->save();

        $notify[] = 'Withdraw request submitted successfully';

        notify($agent, 'WITHDRAW_REQUEST', [
            'method_name'     => $withdraw->method->name,
            'method_currency' => $withdraw->currency,
            'method_amount'   => showAmount($withdraw->final_amount, currencyFormat: false),
            'amount'          => showAmount($withdraw->amount, currencyFormat: false),
            'charge'          => showAmount($withdraw->charge, currencyFormat: false),
            'rate'            => showAmount($withdraw->rate, currencyFormat: false),
            'trx'             => $withdraw->trx,
            'post_balance'    => showAmount($agent->balance, currencyFormat: false),
        ]);

        $notify[] = 'Withdraw request sent successfully';
        return apiResponse("withdraw_confirmed", "success", $notify, [
            'withdraw' => $withdraw->load('method')
        ]);
    }

    public function withdrawLog(Request $request)
    {
        $withdraws = Withdrawal::where('agent_id', auth()->id());
        if ($request->search) {
            $withdraws = $withdraws->where('trx', $request->search);
        }
        $withdraws = $withdraws->where('status', '!=', Status::PAYMENT_INITIATE)->with('method')->orderBy('id', 'desc')->paginate(getPaginate());
        $notify[]  = 'Withdrawals';

        return apiResponse("withdrawals", "success", $notify, [
            'withdrawals' => $withdraws
        ]);
    }
}
