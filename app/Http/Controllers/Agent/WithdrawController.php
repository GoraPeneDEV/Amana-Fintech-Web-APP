<?php

namespace App\Http\Controllers\Agent;

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

class WithdrawController extends Controller
{
    use AgentWithdrawOperation;

    public function withdrawMoney()
    {
        $withdrawMethod = WithdrawMethod::active()->with('saveAccounts', function ($query) {
            $query->where('agent_id', auth('agent')->id());
        })->get();
        $pageTitle = 'Withdraw Money';

        return view('Template::agent.withdraw.methods', compact('pageTitle', 'withdrawMethod'));
    }

    public function withdrawStore(Request $request)
    {
        $request->validate([
            'method_code' => 'required',
            'amount'      => 'required|numeric',

        ]);

        $method = WithdrawMethod::where('id', $request->method_code)->active()->firstOrFail();
        $user   = auth('agent')->user();

        if ($request->amount < $method->min_limit) {
            $notify[] = ['error', 'Your requested amount is smaller than minimum amount'];
            return back()->withNotify($notify)->withInput($request->all());
        }
        if ($request->amount > $method->max_limit) {
            $notify[] = ['error', 'Your requested amount is larger than maximum amount'];
            return back()->withNotify($notify)->withInput($request->all());
        }

        if ($request->amount > $user->balance) {
            $notify[] = ['error', 'Insufficient balance for withdrawal'];
            return back()->withNotify($notify)->withInput($request->all());
        }

        if ($request->save_account_id) {
            $accountExistsCheck = WithdrawSaveAccount::where('agent_id', agent()->id)->where('withdraw_method_id', $method->id)->where('id', $request->save_account_id)->exists();
            if (!$accountExistsCheck) {
                $notify[] = ['error', 'The saved withdraw account is not found'];
                return back()->withNotify($notify);
            }
        }

        $charge      = $method->fixed_charge + ($request->amount * $method->percent_charge / 100);
        $afterCharge = $request->amount - $charge;

        if ($afterCharge <= 0) {
            $notify[] = ['error', 'Withdraw amount must be sufficient for charges'];
            return back()->withNotify($notify)->withInput($request->all());
        }

        $finalAmount = $afterCharge * $method->rate;

        $withdraw                  = new Withdrawal();
        $withdraw->method_id       = $method->id;                     // wallet method ID
        $withdraw->agent_id        = $user->id;
        $withdraw->amount          = $request->amount;
        $withdraw->currency        = $method->currency;
        $withdraw->rate            = $method->rate;
        $withdraw->charge          = $charge;
        $withdraw->final_amount    = $finalAmount;
        $withdraw->after_charge    = $afterCharge;
        $withdraw->trx             = getTrx();
        $withdraw->save_account_id = $request->save_account_id ?? 0;
        $withdraw->save();

        session()->put('wtrx', $withdraw->trx);
        return to_route('agent.withdraw.preview');
    }

    public function withdrawPreview()
    {
        $withdraw       = Withdrawal::with('method', 'agent')->where('trx', session()->get('wtrx'))->where('status', Status::PAYMENT_INITIATE)->orderBy('id', 'desc')->firstOrFail();
        $pageTitle      = 'Withdraw Preview';
        $saveAccountData = WithdrawSaveAccount::where('agent_id', $withdraw->agent_id)
            ->where('withdraw_method_id', $withdraw->method_id)
            ->where('id', $withdraw->save_account_id)
            ->first()?->data ?? [];

        return view('Template::agent.withdraw.preview', compact('pageTitle', 'withdraw', 'saveAccountData'));
    }

    public function withdrawSubmit(Request $request)
    {
        $withdraw = Withdrawal::with('method', 'agent')->where('trx', session()->get('wtrx'))->where('status', Status::PAYMENT_INITIATE)->orderBy('id', 'desc')->firstOrFail();
        $method   = $withdraw->method;

        if ($method->status == Status::DISABLE) {
            abort(404);
        }

        $formData = @$method->form->form_data ?? [];

        $formProcessor  = new FormProcessor();
        $validationRule = $formProcessor->valueValidation($formData);
        $request->validate($validationRule);

        $userData = $formProcessor->processFormData($request, $formData);

        $user = auth('agent')->user();

        if ($user->ts) {
            $response = verifyG2fa($user, $request->authenticator_code);
            if (!$response) {
                $notify[] = ['error', 'Wrong verification code'];
                return back()->withNotify($notify)->withInput($request->all());
            }
        }

        if ($withdraw->amount > $user->balance) {
            $notify[] = ['error', 'Your request amount is larger then your current balance.'];
            return back()->withNotify($notify)->withInput($request->all());
        }

        $withdraw->status = Status::PAYMENT_PENDING;
        $withdraw->withdraw_information = $userData;
        $withdraw->save();
        $user->balance  -=  $withdraw->amount;
        $user->save();

        $transaction               = new Transaction();
        $transaction->agent_id     = $withdraw->agent_id;
        $transaction->amount       = $withdraw->amount;
        $transaction->post_balance = $user->balance;
        $transaction->charge       = $withdraw->charge;
        $transaction->trx_type     = '-';
        $transaction->details      = 'Withdraw request via ' . $withdraw->method->name;
        $transaction->trx          = $withdraw->trx;
        $transaction->remark       = 'withdraw';
        $transaction->save();

        $adminNotification            = new AdminNotification();
        $adminNotification->agent_id  = $user->id;
        $adminNotification->title     = 'New withdraw request from ' . $user->username;
        $adminNotification->click_url = urlPath('admin.withdraw.data.details', $withdraw->id);
        $adminNotification->save();

        notify($user, 'WITHDRAW_REQUEST', [
            'method_name' => $withdraw->method->name,
            'method_currency' => $withdraw->currency,
            'method_amount' => showAmount($withdraw->final_amount, currencyFormat: false),
            'amount' => showAmount($withdraw->amount, currencyFormat: false),
            'charge' => showAmount($withdraw->charge, currencyFormat: false),
            'rate' => showAmount($withdraw->rate, currencyFormat: false),
            'trx' => $withdraw->trx,
            'post_balance' => showAmount($user->balance, currencyFormat: false),
        ]);

        $notify[] = ['success', 'Withdraw request sent successfully'];
        return to_route('agent.withdraw.history')->withNotify($notify);
    }

    public function withdrawLog(Request $request)
    {
        $pageTitle = "Withdrawal Log";
        $withdraws = Withdrawal::where('agent_id', auth('agent')->id())->where('status', '!=', Status::PAYMENT_INITIATE);
        if ($request->search) {
            $withdraws = $withdraws->where('trx', $request->search);
        }
        $withdraws = $withdraws->with('method')->orderBy('id', 'desc')->paginate(getPaginate());
        return view('Template::agent.withdraw.log', compact('pageTitle', 'withdraws'));
    }
}
