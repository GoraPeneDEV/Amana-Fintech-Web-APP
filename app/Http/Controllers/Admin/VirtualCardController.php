<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Gateway;
use App\Models\Transaction;
use App\Models\TransactionCharge;
use App\Models\VirtualCard;
use App\Rules\FileTypeValidate;
use Illuminate\Http\Request;

class VirtualCardController extends Controller
{

    public function list()
    {
        $pageTitle = 'All Virtual cards';
        $cards     = VirtualCard::orderBy('id', getOrderBy())->with('user')->paginate(getPaginate());
        return view('admin.virtual_card.list', compact('pageTitle', 'cards'));
    }
    public function providerConfiguration()
    {
        $pageTitle  = 'Virtual Card Provider Configuration';
        $gateway    = Gateway::automatic()->with('currencies', 'currencies.method')->where('alias', "StripeV3")->firstOrFail();
        $parameters = collect(json_decode($gateway->gateway_parameters));

        return view('admin.virtual_card.provider', compact('pageTitle', 'gateway', 'parameters'));
    }

    public function providerConfigurationUpdate(Request $request, $code)
    {

        $gateway    = Gateway::where('code', $code)->firstOrFail();
        $parameters = collect(json_decode($gateway->gateway_parameters));

        $validationRule = [
            'image' => ['nullable', 'image', new FileTypeValidate(['jpg', 'jpeg', 'png'])],
        ];

        foreach ($parameters as $key => $pram) {
            if ($pram->global) {
                $validationRule[$key] = "required";
            } else {
                $validationRule["currency.*.$key"] = "required";
            }
        }
        $request->validate($validationRule);

        foreach ($parameters as $key => $pram) {
            if ($pram->global) {
                $validationRule[$key] = "required";
            } else {
                $validationRule["currency.*.$key"] = "required";
            }
        }

        $request->validate($validationRule);

        foreach ($parameters->where('global', true) as $key => $pram) {
            $parameters[$key]->value = $request->$key;
        }

        $filename = $gateway->image;

        if ($request->hasFile('image')) {
            try {
                $filename = fileUploader($request->image, getFilePath('gateway'), old: $filename);
            } catch (\Exception $exp) {
                $notify[] = ['errors', 'Image could not be uploaded'];
                return back()->withNotify($notify);
            }
        }

        $gateway->gateway_parameters = json_encode($parameters);
        $gateway->image              = $filename;
        $gateway->save();

        $notify[] = ['success', "The Stripe configuration updated successfully"];
        return back()->withNotify($notify);
    }

    public function chargeAndOtherSetting()
    {
        $pageTitle = 'Charge and Other Settings';
        $charge    = TransactionCharge::where('slug', "virtual_card")->first();
        return view('admin.virtual_card.charge', compact('pageTitle', 'charge'));
    }

    public function chargeAndOtherSettingUpdate(Request $request)
    {
        $request->validate([
            'min_load_amount'       => 'required|numeric|gte:0',
            'max_load_amount'       => 'required|numeric|gte:min_load_amount',
            'fixed_charge'          => 'required|numeric|gte:0',
            'percent_charge'        => 'required|gte:0|lte:100',
            'maximum_card_generate' => 'required|gte:-1'
        ]);

        $charge = TransactionCharge::where('slug', 'virtual_card')->first();

        if (!$charge) {
            $charge       = new TransactionCharge();
            $charge->slug = "virtual_card";
        }

        $charge->percent_charge        = $request->percent_charge;
        $charge->fixed_charge          = $request->fixed_charge;
        $charge->min_limit             = $request->min_load_amount;
        $charge->max_limit             = $request->max_load_amount;
        $charge->maximum_card_generate = $request->maximum_card_generate;
        $charge->save();

        $notify[] = ['success', "The virtual card charge & other configuration updated successfully"];
        return back()->withNotify($notify);
    }


    public function detail($id)
    {   
        $pageTitle = 'Virtual Card Details';
        $card = VirtualCard::with(['user','cardHolder'])->findOrFail($id);
        $basedQuery = Transaction::where('virtual_card_id', $card->id);
        $transactions = (clone $basedQuery)
        ->orderBy('id', 'desc')
        ->searchable(['trx'])
        ->filter(['trx_type','remark'])
        ->dateFilter()
        ->paginate(getPaginate());
        
        $widget['current_balance'] = $card->balance;
        $widget['total_deposit']   = (clone $basedQuery)->where('trx_type', '+')->sum('amount');
        $widget['total_payment']   = (clone $basedQuery)->where('trx_type', '-')->sum('amount');
        $widget['trx_count']       = (clone $basedQuery)->count();

        return view('admin.virtual_card.detail', compact('card','pageTitle','transactions','widget'));
    }
}
