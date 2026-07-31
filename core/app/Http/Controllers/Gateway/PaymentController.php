<?php

namespace App\Http\Controllers\Gateway;

use App\Constants\Status;
use App\Http\Controllers\Controller;
use App\Lib\FormProcessor;
use App\Models\AdminNotification;
use App\Models\Agent;
use App\Models\Deposit;
use App\Models\GatewayCurrency;
use App\Models\Transaction;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PaymentController extends Controller
{
    public function deposit()
    {
        $gatewayCurrency = GatewayCurrency::whereHas('method', function ($gate) {
            $gate->where('status', Status::ENABLE);
        })->with('method')->orderby('method_code')->get();

        $pageTitle = 'Add Money';
        return view('Template::user.payment.deposit', compact('gatewayCurrency', 'pageTitle'));
    }

    public function depositInsert(Request $request)
    {

        $request->validate([
            'amount'   => 'required|numeric|gt:0',
            'gateway'  => 'required',
            'currency' => 'required',
        ]);

        $user = auth()->user();


        $gate = GatewayCurrency::whereHas('method', function ($gate) {
            $gate->where('status', Status::ENABLE);
        })->where('method_code', $request->gateway)->where('currency', $request->currency)->first();

        if (!$gate) {
            $notify[] = ['error', 'Invalid gateway'];
            return back()->withNotify($notify);
        }

        if ($gate->min_amount > $request->amount || $gate->max_amount < $request->amount) {
            $notify[] = ['error', 'Please follow deposit limit'];
            return back()->withNotify($notify);
        }

        $charge      = $gate->fixed_charge + ($request->amount * $gate->percent_charge / 100);
        $payable     = $request->amount + $charge;
        $finalAmount = $payable * $gate->rate;

        $data                  = new Deposit();
        $data->user_id         = $user->id;
        $data->method_code     = $gate->method_code;
        $data->method_currency = strtoupper($gate->currency);
        $data->amount          = $request->amount;
        $data->charge          = $charge;
        $data->rate            = $gate->rate;
        $data->final_amount    = $finalAmount;
        $data->btc_amount      = 0;
        $data->btc_wallet      = "";
        $data->trx             = getTrx();
        $data->success_url     = urlPath('user.deposit.history');
        $data->failed_url      = urlPath('user.deposit.history');
        $data->save();

        session()->put('Track', $data->trx);

        return to_route('user.deposit.confirm');
    }


    public function appDepositConfirm($hash)
    {
        try {
            $id = decrypt($hash);
        } catch (\Exception $ex) {
            abort(404);
        }
        $data = Deposit::where('id', $id)->where('status', Status::PAYMENT_INITIATE)->orderBy('id', 'DESC')->firstOrFail();

        if ($data->user_id != 0) {
            $user = User::findOrFail($data->user_id);
            Auth::login($user);
            logoutAnother('user');
        } elseif ($data->agent_id != 0) {
            $user = Agent::findOrFail($data->agent_id);

            Auth::guard('agent')->login($user);
            logoutAnother('agent');
        }

        session()->put('Track', $data->trx);
        return to_route(strtolower(userGuardType()['type']) . '.deposit.confirm');
    }

    public function depositConfirm()
    {

        $track = session()->get('Track');

        $deposit = Deposit::where('trx', $track)->where('status', Status::PAYMENT_INITIATE)->orderBy('id', 'DESC')->with('gateway')->firstOrFail();

        if ($deposit->method_code >= 1000) {
            return to_route(strtolower(userGuardType()['type']) . '.deposit.manual.confirm');
        }


        $dirName = $deposit->gateway->alias;
        $new = __NAMESPACE__ . '\\' . $dirName . '\\ProcessController';

        $data = $new::process($deposit);
        $data = json_decode($data);


        if (isset($data->error)) {
            $notify[] = ['error', $data->message];
            return back()->withNotify($notify);
        }
        if (isset($data->redirect)) {
            return redirect($data->redirect_url);
        }

        // for Stripe V3
        if (@$data->session) {
            $deposit->btc_wallet = $data->session->id;
            $deposit->save();
        }


        if ($deposit->agent_id) {
            $view = str_replace('user', 'agent', $data->view);
        } else {
            $view = $data->view;
        }

        if ($deposit->from_api) {
            $layouts     = "app";
            $section     = "app-content";
            $rowClass    = "row justify-content-center";
            $extraClass  = "py-120";
        } else {
            $layouts     = $deposit->agent_id ? 'agent' :   "master";
            $section     = "content";
            $rowClass    = "row";
            $extraClass  = "";
        }

        $pageTitle = 'Payment Confirm';
        return view("Template::$view", compact('data', 'pageTitle', 'deposit', 'layouts', 'section', 'rowClass', 'extraClass'));
    }


    public static function userDataUpdate($deposit, $isManual = null)
    {

        if ($deposit->user_id != 0) {


            if ($deposit->status == Status::PAYMENT_INITIATE || $deposit->status == Status::PAYMENT_PENDING) {
                $deposit->status = Status::PAYMENT_SUCCESS;
                $deposit->save();

                $user = User::find($deposit->user_id);

                $user->balance += $deposit->amount;
                $user->save();

                $methodName = $deposit->methodName();

                $transaction               = new Transaction();
                $transaction->user_id      = $user->id;
                $transaction->amount       = $deposit->amount;
                $transaction->post_balance = $user->balance;
                $transaction->charge       = $deposit->charge;
                $transaction->trx_type     = '+';
                $transaction->details      = 'Add money via ' . $methodName;
                $transaction->trx          = $deposit->trx;
                $transaction->remark       = 'add_money';
                $transaction->save();

                if (!$isManual) {
                    $adminNotification = new AdminNotification();
                    $adminNotification->user_id = $user->id;
                    $adminNotification->title = 'Add Money successful via ' . $methodName;
                    $adminNotification->click_url = urlPath('admin.deposit.successful');
                    $adminNotification->save();
                }

                notify($user, $isManual ? 'DEPOSIT_APPROVE' : 'DEPOSIT_COMPLETE', [
                    'method_name' => $methodName,
                    'method_currency' => $deposit->method_currency,
                    'method_amount' => showAmount($deposit->final_amount, currencyFormat: false),
                    'amount' => showAmount($deposit->amount, currencyFormat: false),
                    'charge' => showAmount($deposit->charge, currencyFormat: false),
                    'rate' => showAmount($deposit->rate, currencyFormat: false),
                    'trx' => $deposit->trx,
                    'post_balance' => showAmount($user->balance)
                ]);
            }
        } elseif ($deposit->agent_id != 0) {

            if ($deposit->status == Status::PAYMENT_INITIATE || $deposit->status == Status::PAYMENT_PENDING) {
                $deposit->status = Status::PAYMENT_SUCCESS;
                $deposit->save();

                $agent = Agent::find($deposit->agent_id);

                $agent->balance += $deposit->amount;
                $agent->save();

                $methodName = $deposit->methodName();

                $transaction               = new Transaction();
                $transaction->agent_id     = $agent->id;
                $transaction->amount       = $deposit->amount;
                $transaction->post_balance = $agent->balance;
                $transaction->charge       = $deposit->charge;
                $transaction->trx_type     = '+';
                $transaction->details      = 'Add money via ' . $methodName;
                $transaction->trx          = $deposit->trx;
                $transaction->remark       = 'add_money';
                $transaction->save();

                if (!$isManual) {
                    $adminNotification = new AdminNotification();
                    $adminNotification->agent_id  = $agent->id;
                    $adminNotification->title = 'Add Money successful via ' . $methodName;
                    $adminNotification->click_url = urlPath('admin.deposit.successful');
                    $adminNotification->save();
                }

                notify($agent, $isManual ? 'DEPOSIT_APPROVE' : 'DEPOSIT_COMPLETE', [
                    'method_name' => $methodName,
                    'method_currency' => $deposit->method_currency,
                    'method_amount' => showAmount($deposit->final_amount, currencyFormat: false),
                    'amount' => showAmount($deposit->amount, currencyFormat: false),
                    'charge' => showAmount($deposit->charge, currencyFormat: false),
                    'rate' => showAmount($deposit->rate, currencyFormat: false),
                    'trx' => $deposit->trx,
                    'post_balance' => showAmount($agent->balance)
                ]);
            }
        }
    }

    public function manualDepositConfirm()
    {
        $track = session()->get('Track');
        $data = Deposit::with('gateway')->where('status', Status::PAYMENT_INITIATE)->where('trx', $track)->first();
        abort_if(!$data, 404);
        if ($data->method_code > 999) {

            $pageTitle = 'Confirm Add Money';
            $method    = $data->gatewayCurrency();
            $gateway   = $method->method;


            if ($data->from_api) {
                $layouts     = "app";
                $section     = "app-content";
                $rowClass    = "row justify-content-center";
                $extraClass  = "py-120";
            } else {
                $layouts     = $data->agent_id ? 'agent' :   "master";
                $section     = "content";
                $rowClass    = "row";
                $extraClass  = "";
            }


            if ($data->user_id != 0) {
                return view('Template::user.payment.manual', compact('data', 'pageTitle', 'method', 'gateway', 'layouts', 'section', 'rowClass', 'extraClass'));
            } elseif ($data->agent_id != 0) {
                return view('Template::agent.payment.manual', compact('data', 'pageTitle', 'method', 'gateway', 'layouts', 'section', 'rowClass', 'extraClass'));
            }
        }
        abort(404);
    }

    public function manualDepositUpdate(Request $request)
    {
        $track = session()->get('Track');
        $data = Deposit::with('gateway')->where('status', Status::PAYMENT_INITIATE)->where('trx', $track)->first();
        abort_if(!$data, 404);
        $gatewayCurrency = $data->gatewayCurrency();
        $gateway = $gatewayCurrency->method;
        $formData = $gateway->form->form_data;

        $formProcessor = new FormProcessor();
        $validationRule = $formProcessor->valueValidation($formData);
        $request->validate($validationRule);
        $userData = $formProcessor->processFormData($request, $formData);


        $data->detail = $userData;
        $data->status = Status::PAYMENT_PENDING;
        $data->save();

        if ($data->user_id != 0) {
            $user = User::find($data->user_id);
            $idColoumn = 'user_id';
        } elseif ($data->agent_id != 0) {
            $user = Agent::find($data->agent_id);
            $idColoumn = 'agent_id';
        }

        $adminNotification = new AdminNotification();
        $adminNotification->$idColoumn = $user->id;
        $adminNotification->title = 'Add Money request from ' . $user->username;
        $adminNotification->click_url = urlPath('admin.deposit.details', $data->id);
        $adminNotification->save();

        notify($user, 'DEPOSIT_REQUEST', [
            'method_name' => $data->gatewayCurrency()->name,
            'method_currency' => $data->method_currency,
            'method_amount' => showAmount($data->final_amount, currencyFormat: false),
            'amount' => showAmount($data->amount, currencyFormat: false),
            'charge' => showAmount($data->charge, currencyFormat: false),
            'rate' => showAmount($data->rate, currencyFormat: false),
            'trx' => $data->trx
        ]);

        $notify[] = ['success', 'You have deposit request has been taken'];

        if ($data->user_id != 0) {
            return to_route(strtolower(userGuardType()['type']) . '.deposit.history')->withNotify($notify);
        } elseif ($data->agent_id != 0) {
            return to_route(strtolower(userGuardType()['type']) . '.add.money.history')->withNotify($notify);
        }
    }
}
