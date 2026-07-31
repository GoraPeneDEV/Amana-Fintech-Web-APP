<?php

namespace App\Http\Controllers\User;

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
        $pageTitle = "Virtual Card";
        $cards     = ModelsVirtualCard::where('user_id', auth()->id())->with("cardHolder")->orderBy('status')->orderBy('id','desc')->get();
        return view('Template::user.virtual_card.index', compact('pageTitle', 'cards'));
    }

    public function newCard()
    {
        $pageTitle   = "Create New Card";
        $user        = auth()->user();
        $cardHolders = VirtualCardHolder::where('user_id', $user->id)->get();
        return view('Template::user.virtual_card.create', compact('pageTitle', 'user', 'cardHolders'));
    }

    public function view($id)
    {
        $pageTitle    = "Card Details";
        $card         = ModelsVirtualCard::where('user_id', auth()->id())->where('id', $id)->firstOrFail();

        
        $user         = auth()->user();
        $transactions = Transaction::where('virtual_card_id', $card->id)->where('user_id', $user->id)->latest('id')->paginate(getPaginate());
        $charge       = TransactionCharge::where('slug', "virtual_card")->firstOrFail();

        if (request()->bg) {
            if (!in_array(request()->bg, ['bg-two', 'bg-one'])) {
                
                abort(404);
            }
            $bgClass = request()->bg;
        } else {
            $bgClass = "bg-two";
        }
        return view('Template::user.virtual_card.view', compact('pageTitle', 'card', 'user', 'transactions', 'bgClass', 'charge'));
    }
}
