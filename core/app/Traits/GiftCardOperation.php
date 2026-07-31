<?php

namespace App\Traits;

use App\Constants\Status;
use App\Models\ApiProvider;
use App\Models\Country;
use App\Models\GiftCardCategory;
use App\Models\GiftCard;
use App\Models\GiftCardPurchase;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

trait GiftCardOperation
{
    public function create()
    {
        $pageTitle = 'Gift Card';
        $view      = 'Template::user.gift_card.create';

        $countries = Country::where('is_gift_card', Status::YES)->active()->with('products', function ($q) {
            $q->active();
        })->get();

        $categories = GiftCardCategory::active()->with('products', function ($q) {
            $q->active();
        })->get();

        $products = GiftCard::active()->searchable(['product_name'])->filter(['country_id', 'category_id'])->paginate(getPaginate());

        return responseManager("gift_card","Gift Card","success",[
            'view'       => $view,
            'pageTitle'  => $pageTitle,
            'countries'  => $countries,
            'categories' => $categories,
            'products'   => $products
        ]);
    }

    public function show($id)
    {
        $pageTitle = 'Gift Card Purchase';
        $product   = GiftCard::active()->find($id);

        if (!$product) {
            return responseManager('not_found', 'The gift card not found');
        }
        $view      = 'Template::user.gift_card.show';
        $user = auth()->user();
        
        return responseManager("gift_card","Gift Card","success",[
            'view'      => $view,
            'pageTitle' => $pageTitle,
            'product'   => $product,
            'user'      => $user
        ]);
    }

    public function purchase(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'amount'     => 'required|numeric|gt:0',
            'email'      => 'required|email',
            'name'       => 'required|string',
            'quantity'   => 'required|numeric',
            ...getOtpValidationRules()
        ]);

        if ($validator->fails()) {
            return apiResponse("validation_error", "error", $validator->errors()->all());
        }

        $user     = auth()->user();
        $product = GiftCard::active()->where('id', $id)->first();

        if (!$product) {
            $notify[] = 'The Gift card is not found';
            return apiResponse("not_found", "error", $notify);
        }


        if ($product->denomination_type == 'FIXED') {
            $fixedAmounts = $product->fixed_recipient_denominations;

            if (!in_array($request->amount, $fixedAmounts)) {
                $notify[] = 'Invalid amount selected';
                return apiResponse("invalid_amount", "error", $notify);
            }
        } else {
            $minAmount = $product->min_recipient_denomination;
            $maxAmount = $product->max_recipient_denomination;

            if ($request->amount < $minAmount) {
                $notify[] = 'Thew amount should be greater than ' . showAmount($minAmount);
                return apiResponse("invalid_amount", "error", $notify);
            }

            if ($request->amount > $maxAmount) {
                $notify[] = 'Amount should be less than ' . showAmount($maxAmount);
                return apiResponse("invalid_amount", "error", $notify);
            }
        }

        $exchangeRate = $product->recipient_to_sender_exchange_rate;
        $unitPrice    = $exchangeRate * $request->amount;
        $quantity     = $request->quantity;
        $fee          = $product->sender_fee + ($request->amount * ($product->sender_fee_percentage / 100));

        $subtotal = $unitPrice * $quantity;
        $totalFee = $fee * $quantity;
        $total    = $totalFee + $subtotal;

        if ($total  > $user->balance) {
            $notify[] = 'Insufficient Balance';
            return apiResponse("insufficient_balance", "error", $notify);
        }

        $provider = ApiProvider::whereJsonContains('module', 'gift_card')->where('status->gift_card', Status::YES)->first();

        if (!$provider) {
            $notify[] = 'Sorry! Gift card provider not found';
            return apiResponse("not_found", "error", $notify);
        }

        $details = [
            'product_id' => $product->id,
            'email'      => $request->email,
            'name'       => $request->name,
            'quantity'   => $request->quantity,
            'amount'     => $request->amount,
            'rate'       => $product->recipient_to_sender_exchange_rate,
            'unit_price' => $unitPrice,
            'subtotal'   => $subtotal,
            'total_fee'  => $totalFee,
            'total'      => $total
        ];

        return storeAuthorizedTransactionData('gift_card', $details);
    }

    public function history()
    {
        $pageTitle         = 'Gift Card Purchase History';
        $view              = 'Template::user.gift_card.index';
        $giftCardPurchases = GiftCardPurchase::where('user_id', auth()->id())
            ->latest('id')
            ->searchable(['trx', 'giftCard:product_name', 'recipient_email'])
            ->with(['giftCard', 'user'])
            ->paginate(getPaginate());

        return responseManager("gift_card_history", "Gift Card History", "success",[
            'view'              => $view,
            'pageTitle'         => $pageTitle,
            'giftCardPurchases' => $giftCardPurchases
        ]);
    }

    public function details($id)
    {
        $pageTitle        = 'Gift Card Purchase Details';
        $user             = auth()->user();
        $view             = 'Template::user.gift_card.details';
        $giftCardPurchase = GiftCardPurchase::where('id', $id)->where('user_id', $user->id)->first();

        if (!$giftCardPurchase) {
            $notify = "The gift card purchase not found";
            return responseManager('not_fund', $notify);
        }

        return responseManager("gift_card_details", "Gift Card Details", "success",[
            'view'              => $view,
            'pageTitle'         => $pageTitle,
            'giftCardPurchase'  => $giftCardPurchase
        ]);
    }

    public function pdf($id)
    {
        $pageTitle        = "Gift Card Purchase Receipt";
        $user             = auth()->user();
        $giftCardPurchase = GiftCardPurchase::where('id', $id)->where('user_id', $user->id)->first();

        if (!$giftCardPurchase) {
            $notify = "The gift card purchase transaction is not found";
            return responseManager('not_fund', $notify);
        }

        $activeTemplateTrue = activeTemplate(true);
        $activeTemplate     = activeTemplate();

        $pdf      = Pdf::loadView($activeTemplate . 'user.gift_card.pdf', compact('pageTitle', 'giftCardPurchase', 'user', 'activeTemplateTrue', 'activeTemplate'));
        $fileName = "Gift Card Purchase Receipt - " . $giftCardPurchase->trx . ".pdf";
        return $pdf->download($fileName);
    }
}
