<?php

namespace App\Http\Controllers\Api\Merchant;

use App\Constants\Status;
use App\Http\Controllers\Controller;
use App\Lib\FormProcessor;
use App\Models\AdminNotification;
use App\Models\Transaction;
use App\Models\Withdrawal;
use App\Models\WithdrawMethod;
use App\Models\WithdrawSaveAccount;
use App\Traits\MerchantWithdrawOperation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class MerchantWithdrawController extends Controller
{
    use MerchantWithdrawOperation;
    public function withdrawMethod()
    {
        $withdrawMethod = WithdrawMethod::active()->with('saveAccounts', function ($q) {
            $q->where('merchant_id', merchant()->id);
        })->with('form')->get();

        $merchant       = auth()->user();
        $notify[]       = 'Withdrawals methods';

        return apiResponse("withdraw_methods", "success", $notify, [
            'withdraw_methods' => $withdrawMethod,
            'merchant'         => $merchant,
            'balance'          => $merchant->balance
        ]);
    }

    public function withdrawStore(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'method_code' => 'required',
            'amount'      => 'required|numeric'
        ]);

        if ($validator->fails()) {
            return apiResponse("validation_error", "error", $validator->errors()->all());
        }

        $method = WithdrawMethod::where('id', $request->method_code)->active()->first();
        if (!$method) {
            $notify[] = 'Withdraw method not found.';
            return apiResponse("not_found", "error", $notify);
        }

        $user = auth()->user();

        if ($request->amount < $method->merchant_min_limit) {
            $notify[] = 'Your requested amount is smaller than minimum amount';
            return apiResponse("cross_limit", "error", $notify);
        }
        if ($request->amount > $method->merchant_max_limit) {
            $notify[] = 'Your requested amount is larger than maximum amount';
            return apiResponse("cross_limit", "error", $notify);
        }

        if ($request->amount > $user->balance) {
            $notify[] = 'Insufficient balance for withdrawal';
            return apiResponse("insufficient_balance", "error", $notify);
        }

        if ($request->save_account_id) {
            $accountExistsCheck = WithdrawSaveAccount::where('merchant_id', merchant()->id)->where('withdraw_method_id', $method->id)->where('id', $request->save_account_id)->exists();
            if (!$accountExistsCheck) {
                $notify[] = 'The saved withdraw account is not found';
                return apiResponse("not_found", "error", $notify);
            }
        }

        $charge      = $method->merchant_fixed_charge + ($request->amount * $method->merchant_percent_charge / 100);
        $afterCharge = $request->amount - $charge;

        if ($afterCharge <= 0) {
            $notify[] = 'Withdraw amount must be sufficient for charges';
            return apiResponse("charge", "error", $notify);
        }

        $finalAmount = $afterCharge * $method->rate;

        $withdraw               = new Withdrawal();
        $withdraw->method_id    = $method->id; // wallet method ID
        $withdraw->merchant_id  = $user->id;
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
        return apiResponse("withdraw_request_created", "success", $notify, [
            'trx'           => $withdraw->trx,
            'withdraw_data' => $withdraw,
            'form'          => $method->form->form_data,
            'authenticator' => (bool) $user->ts
        ]);
    }

    public function withdrawSubmit(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'trx' => 'required',
            ...pinValidationRule()
        ]);

        if ($validator->fails()) {
            return apiResponse("validation_error", "error", $validator->errors()->all());
        }

        $user = auth()->user();

        if (!Hash::check($request->pin, $user->password)) {
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

        $userData = $formProcessor->processFormData($request, $formData);

        if ($user->ts) {
            if (!$request->authenticator_code) {
                $notify[] = 'Google authentication is required';
                return apiResponse("required", "error", $notify);
            }
            $response = verifyG2fa($user, $request->authenticator_code);
            if (!$response) {
                $notify[] = 'Wrong verification code';
                return apiResponse("validation_error", "error", $notify);
            }
        }

        if ($withdraw->amount > $user->balance) {
            $notify[] = 'Your request amount is larger then your current balance';
            return apiResponse("insufficient_balance", "error", $notify);
        }

        $withdraw->status               = Status::PAYMENT_PENDING;
        $withdraw->withdraw_information = $userData;
        $withdraw->save();

        $user->balance -= $withdraw->amount;
        $user->save();

        $transaction               = new Transaction();
        $transaction->merchant_id  = $withdraw->merchant_id;
        $transaction->amount       = $withdraw->amount;
        $transaction->post_balance = $user->balance;
        $transaction->charge       = $withdraw->charge;
        $transaction->trx_type     = '-';
        $transaction->details      = 'Withdraw request via ' . $withdraw->method->name;
        $transaction->trx          = $withdraw->trx;
        $transaction->remark       = 'withdraw';
        $transaction->save();

        $adminNotification              = new AdminNotification();
        $adminNotification->merchant_id = $user->id;
        $adminNotification->title       = 'New withdraw request from ' . $user->username;
        $adminNotification->click_url   = urlPath('admin.withdraw.merchant.data.details', $withdraw->id);
        $adminNotification->save();

        notify($user, 'WITHDRAW_REQUEST', [
            'method_name'     => $withdraw->method->name,
            'method_currency' => $withdraw->currency,
            'method_amount'   => showAmount($withdraw->final_amount, currencyFormat: false),
            'amount'          => showAmount($withdraw->amount, currencyFormat: false),
            'charge'          => showAmount($withdraw->charge, currencyFormat: false),
            'rate'            => showAmount($withdraw->rate, currencyFormat: false),
            'trx'             => $withdraw->trx,
            'post_balance'    => showAmount($user->balance, currencyFormat: false),
        ]);

        $notify[] = 'Withdraw request sent successfully';
        return apiResponse("withdraw_confirmed", "success", $notify, [
            'withdraw'             => $transaction,
            'withdraw_information' => $withdraw->withdraw_information,
        ]);
    }

    public function withdrawLog(Request $request)
    {
        $withdraws = Withdrawal::where('merchant_id', auth()->id());
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
