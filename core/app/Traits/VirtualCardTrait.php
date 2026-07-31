<?php

namespace App\Traits;

use Illuminate\Http\Request;
use App\Constants\Status;
use App\Lib\VirtualCard;
use App\Models\Transaction;
use App\Models\TransactionCharge;
use App\Models\VirtualCard as ModelsVirtualCard;
use App\Rules\FileTypeValidate;
use Exception;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;


trait VirtualCardTrait
{
    public function store(Request $request)
    {
        $charge = TransactionCharge::where('slug', "virtual_card")->first();

        if (!$charge) {
            $notify[] =  "Something went wrong";
            return apiResponse('not_found', 'error', $notify);
        }

        $maxCardGenerate = $charge->maximum_card_generate;

        if (!$maxCardGenerate != -1) {
            $userCardCount = ModelsVirtualCard::where('user_id', auth()->id())->count();
            if ($userCardCount >= $maxCardGenerate) {
                $notify[] = 'You have reached the maximum of virtual card generation';
                return apiResponse('limit', 'error', $notify);
            }
        }

        $isRequiredCardHolderFiled = $request->card_holder_type == Status::VIRTUAL_CARD_HOLDER_NEW ? 'required' : 'nullable';

        $validator = Validator::make($request->all(), [
            'usability_type'   => ['required', 'integer', Rule::in(
                Status::VIRTUAL_CARD_REUSEABLE,
                Status::VIRTUAL_CARD_ONETIME
            )],
            'card_holder_type'   => ['required', 'integer', Rule::in(
                Status::VIRTUAL_CARD_HOLDER_EXISTING,
                Status::VIRTUAL_CARD_HOLDER_NEW
            )],
            'first_name'     => "$isRequiredCardHolderFiled|string|max:255",
            'last_name'      => "$isRequiredCardHolderFiled|string|max:255",
            'card_name'      => "$isRequiredCardHolderFiled|string|max:255",
            'email'          => "$isRequiredCardHolderFiled|email|max:255|unique:virtual_card_holders,email",
            'mobile_number'  => "$isRequiredCardHolderFiled|unique:virtual_card_holders,phone_number",
            'address'        => "$isRequiredCardHolderFiled|string|max:255",
            'state'          => "$isRequiredCardHolderFiled|string|max:255",
            'zip_code'       => "$isRequiredCardHolderFiled|string|max:20",
            'city'           => "$isRequiredCardHolderFiled|string|max:255",
            'birthday_month' => "$isRequiredCardHolderFiled|string|max:2",
            'birthday_year'  => "$isRequiredCardHolderFiled|string|max:4",
            'birthday'       => "$isRequiredCardHolderFiled|string|max:2",
            'document_front' => [$isRequiredCardHolderFiled, new FileTypeValidate(['jpg', 'jpeg', 'png', 'pdf']), 'max:10240'],   // stripe  allow maximum 10 mb
            'document_back'  => [$isRequiredCardHolderFiled, new FileTypeValidate(['jpg', 'jpeg', 'png', 'pdf']), 'max:10240'],   // stripe  allow maximum 10 mb
            'card_holder'    => "required_if:card_holder_type," . Status::VIRTUAL_CARD_HOLDER_EXISTING,
        ], [
            'card_holder.required_if' => 'The card holder filed is required'
        ]);

        if ($validator->fails()) {
            return apiResponse('validation_error', 'error', $validator->errors()->all());
        }

        return (new VirtualCard())->newCard($request);
    }
    public function addFund(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'amount' => 'required|numeric|gt:0'
        ]);

        if ($validator->fails()) {
            return apiResponse('validation_error', 'error', $validator->errors()->all());
        }

        $user = auth()->user();
        $card = ModelsVirtualCard::where('id', $id)->where('user_id', $user->id)->first();

        if (!$card) {
            $notify[] =  "The card is not available";
            return apiResponse('not_found', 'error', $notify);
        }

        if ($card->status != Status::VIRTUAL_CARD_ACTIVE) {
            $notify[] =  "The card is not available for add fund";
            return apiResponse('not_found', 'error', $notify);
        }

        $charge = TransactionCharge::where('slug', "virtual_card")->first();

        if (!$charge) {
            $notify[] =  "The transaction charge is not available";
            return apiResponse('not_found', 'error', $notify);
        }


        $amount     = $request->amount;
        $minAmount  = $charge->min_limit;
        $maxAmount  = $charge->max_limit;

        if ($amount < $minAmount) {
            $notify[] = "The minimum amount must be " . showAmount($minAmount);
            return apiResponse('limit', 'error', $notify);
        }

        if ($amount > $maxAmount) {
            $notify[] = "The maximum amount allowed is " . showAmount($maxAmount);
            return apiResponse('limit', 'error', $notify);
        }

        $fixedCharge   = $charge->fixed_charge;
        $percentCharge = $charge->percent_charge;
        $totalCharge   = $fixedCharge + ($amount * $percentCharge / 100);
        $totalAmount   = $amount + $totalCharge;


        if ($totalAmount > $user->balance) {
            $notify[] = 'Insufficient balance for adding funds to the card';
            return apiResponse('insufficient', 'error', $notify);
        }

        try {

            $trx = getTrx();

            $user->balance -= $totalAmount;
            $user->save();

            $transaction                      = new Transaction();
            $transaction->user_id             = $user->id;
            $transaction->virtual_card_id     = 0;
            $transaction->for_virtual_card_id = $card->id;
            $transaction->amount              = $amount;
            $transaction->post_balance        = $user->balance;
            $transaction->charge              = $totalCharge;
            $transaction->trx_type            = '-';
            $transaction->details             = 'Balance deducted for adding funds to virtual card';
            $transaction->trx                 = $trx;
            $transaction->remark              = 'virtual_card_add_fund';
            $transaction->save();

            $card->balance += $amount;
            $card->save();


            $virtualCardLib = new VirtualCard();
            $virtualCardLib->updateSpendingLimit($card->card_id, $amount);

            $transaction                      = new Transaction();
            $transaction->user_id             = $user->id;
            $transaction->virtual_card_id     = $card->id;
            $transaction->for_virtual_card_id = $card->id;
            $transaction->amount              = $amount;
            $transaction->post_balance        = $user->balance;
            $transaction->charge              = 0;
            $transaction->trx_type            = '+';
            $transaction->details             = 'Funds added to virtual card';
            $transaction->trx                 = $trx;
            $transaction->remark              = 'virtual_card_add_fund';
            $transaction->save();

            $notify[] = "Fund successfully added to your virtual card";

            return apiResponse('fund_added', 'success', $notify);
        } catch (Exception  $ex) {
            $notify[] = "Funds failed to add to your virtual card";
            return apiResponse('exception', 'error', $notify);
        }
    }
    public function cancel($id)
    {

        $user = auth()->user();
        $card = ModelsVirtualCard::where('id', $id)->where('user_id', $user->id)->first();

        if (!$card) {
            $notify =  "The card is not available";
            return responseManager("not_found", $notify);
        }

        if ($card->status == Status::VIRTUAL_CARD_CLOSED) {
            $notify =  "The card is not available for closed";
            return responseManager("canceled", $notify);
        }

        try {

            $virtualCardLib = new VirtualCard();
            $virtualCardLib->cancel($card->card_id);

            $card->status = Status::VIRTUAL_CARD_CLOSED;
            $card->save();

            $notify = "The virtual card is closed successfully";
            return responseManager("card_canceled", $notify, 'success');
        } catch (Exception  $ex) {
            $notify = "The card cancelled failed";
            return responseManager("exception", $notify);
        }
    }

    public function confidential(Request $request, $id)
    {
        $validator  = Validator::make($request->all(), [
            ...pinValidationRule()
        ]);

        if ($validator->fails()) {
            return apiResponse('validation_error', 'error', $validator->errors()->all());
        }

        $user = auth()->user();

        if (!Hash::check($request->pin, $user->password)) {
            $notify[] = "The PIN doesn\'t match!";
            return apiResponse("not_match", "error", $notify);
        }

        $card = ModelsVirtualCard::where('id', $id)->where('user_id', $user->id)->first();

        if (!$card) {
            $notify[] =  "The card is not available";
            return apiResponse('not_found', 'error', $notify);
        }

        try {
            $cardLib = new VirtualCard();
            return $cardLib->getCardConfidential($card->card_id);
        } catch (Exception $ex) {
            $notify[] = "Failed to fetch card confidential data. Please try later";
            return apiResponse('exception', 'error', $notify);
        }
    }
}
