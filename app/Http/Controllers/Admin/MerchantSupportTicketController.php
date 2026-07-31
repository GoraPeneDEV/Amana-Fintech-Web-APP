<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\SupportTicket;


class MerchantSupportTicketController extends Controller
{
    public function tickets()
    {
        $pageTitle = 'Merchant Support Tickets';
        $baseQuery = $this->baseQuery();

        if (request()->export) {
            return exportData($baseQuery, request()->export, "SupportTicket");
        }

        $tickets = $baseQuery->with('merchant')->paginate(getPaginate());
        return view('admin.support.tickets', compact('tickets', 'pageTitle'));
    }

    public function pendingTicket()
    {
        $pageTitle = 'Merchant Pending Tickets';
        $baseQuery = $this->baseQuery('pending');

        if (request()->export) {
            return exportData($baseQuery, request()->export, "SupportTicket");
        }
        $tickets = $baseQuery->with('merchant')->paginate(getPaginate());
        return view('admin.support.tickets', compact('tickets', 'pageTitle'));
    }

    public function closedTicket()
    {
        $pageTitle = 'Merchant Closed Tickets';
        $baseQuery = $this->baseQuery('closed');

        if (request()->export) {
            return exportData($baseQuery, request()->export, "SupportTicket");
        }
        $tickets = $baseQuery->with('merchant')->paginate(getPaginate());
        return view('admin.support.tickets', compact('tickets', 'pageTitle'));
    }

    public function answeredTicket()
    {
        $pageTitle = 'Merchant Answered Tickets';
        $baseQuery = $this->baseQuery('answered');

        if (request()->export) {
            return exportData($baseQuery, request()->export, "SupportTicket");
        }
        $tickets = $baseQuery->with('merchant')->paginate(getPaginate());

        return view('admin.support.tickets', compact('tickets', 'pageTitle'));
    }


    private function baseQuery($scope = 'query')
    {
        return SupportTicket::$scope()->searchable(['name', 'subject', 'ticket', 'merchant:username'])->filter(['status', 'priority'])
            ->where('merchant_id', '!=', 0)->orderBy('id', getOrderBy());
    }
}
