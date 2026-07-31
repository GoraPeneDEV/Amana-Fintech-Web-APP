<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Transaction;
use App\Models\TransactionCharge;
use App\Models\VirtualCard as ModelsVirtualCard;
use App\Models\VirtualCardHolder;
use App\Traits\VirtualCardTrait;

class VirtualCardController extends Controller
{
    use VirtualCardTrait;

    public function list()
    {
        $cards = ModelsVirtualCard::where('user_id', auth()->id())->with("cardHolder")->orderBy('status')->orderBy('id','desc')->get();
        
        return apiResponse('card_list', 'success', ["Card List"], [
            'cards' => $cards,
        ]);
    }

    public function transaction()
    {
        $transactions = Transaction::with('virtualCard')->where('user_id', auth()->id())->where('virtual_card_id', '!=', 0)->paginate(getPaginate());
        return apiResponse('transaction_lit', 'success', ['virtual card transaction list'], [
            'transactions' => $transactions,
        ]);
    }

    public function newCard()
    {
        $cardHolders = VirtualCardHolder::where('user_id', auth()->id())->get();

        return apiResponse('create_card', 'success', ["Create new card"], [
            'card_holders'          => $cardHolders,
            'supported_file_format' => ['jpg', 'jpeg', 'png', 'pdf'],
            'max_file_size'         => "10MB"
        ]);
    }

    public function view($id)
    {
        $user = auth()->user();
        $card = ModelsVirtualCard::where('user_id', $user->id)->where('id', $id)->with("cardHolder")->first();

        if (!$card) {
            $notify[] = "The card is not available";
            return apiResponse('card_not_found', 'error', $notify);
        }

        $transactions = Transaction::where('virtual_card_id', $card->id)->where('user_id', $user->id)->with('virtualCard')->latest('id')->get();
        $charge       = TransactionCharge::where('slug', "virtual_card")->first();

        return apiResponse('card_details', 'success', ["Card view"], [
            'card'            => $card,
            'transactions'    => $transactions,
            'current_balance' => $user->balance,
            'charge'          => $charge
        ]);
    }
}
