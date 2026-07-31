<?php

namespace App\Http\Controllers\Agent;

use App\Constants\Status;
use App\Http\Controllers\Controller;
use App\Models\Deposit;
use App\Models\GatewayCurrency;
use Illuminate\Http\Request;

class AddMoneyController extends Controller
{

    public function create()
    {
        $gatewayCurrency = GatewayCurrency::whereHas('method', function ($gate) {
            $gate->where('status', Status::ENABLE);
        })->with('method')->orderby('method_code')->get();
        
        $pageTitle = 'Add Money';
        return view('Template::agent.add_money.create', compact('gatewayCurrency', 'pageTitle'));
    }

    public function store(Request $request)
    {

        $request->validate([
            'amount'   => 'required|numeric|gt:0',
            'gateway'  => 'required',
            'currency' => 'required',
        ]);

        $user = auth('agent')->user();

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

        $data = new Deposit();
        $data->agent_id        = $user->id;
        $data->method_code     = $gate->method_code;
        $data->method_currency = strtoupper($gate->currency);
        $data->amount          = $request->amount;
        $data->charge          = $charge;
        $data->rate            = $gate->rate;
        $data->final_amount    = $finalAmount;
        $data->btc_amount      = 0;
        $data->btc_wallet      = "";
        $data->trx             = getTrx();
        $data->success_url     = urlPath('agent.add.money.history');
        $data->failed_url      = urlPath('agent.add.money.history');
        $data->save();

        session()->put('Track', $data->trx);
        return to_route('agent.deposit.confirm');
    }

    
    public function history(Request $request)
    {
        $pageTitle = 'Add Money History';
        $deposits = auth('agent')->user()->deposits()->searchable(['trx'])->with(['gateway'])->orderBy('id', 'desc')->paginate(getPaginate());
        return view('Template::agent.add_money.index', compact('pageTitle', 'deposits'));
    }

}
