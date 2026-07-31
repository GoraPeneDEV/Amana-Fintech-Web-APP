<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\MoneyRequest;
use Illuminate\Http\Request;

class RequestMoneyController extends Controller
{
    public function history()
    {

        $query  = MoneyRequest::orderBy('id', getOrderBy());

        $requestMoneys = $query->with(['requestSender', 'requestReceiver'])
        ->searchable(['requestSender:username', 'requestReceiver:username','trx'])
        ->paginate(getPaginate());

        $pageTitle    = "Request Money History";
        if (request()->export) {
            return exportData($query, request()->export, "MoneyRequest", "A4 landscape");
        }

        return view('admin.request_money.history', compact('pageTitle', 'requestMoneys'));

        
    }
}
