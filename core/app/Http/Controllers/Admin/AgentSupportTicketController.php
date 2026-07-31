<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\SupportTicket;

class AgentSupportTicketController extends Controller
{

    public function tickets()
    {
        $pageTitle = 'Agent Support Tickets';
        $baseQuery = $this->baseQuery();

        if (request()->export) {
            return exportData($baseQuery, request()->export, "SupportTicket");
        }

        $tickets = $baseQuery->with('agent')->paginate(getPaginate());
        return view('admin.support.tickets', compact('tickets', 'pageTitle'));
    }

    public function pendingTicket()
    {
        $pageTitle = 'Agent Pending Tickets';
        $baseQuery = $this->baseQuery('pending');

        if (request()->export) {
            return exportData($baseQuery, request()->export, "SupportTicket");
        }
        $tickets = $baseQuery->with('agent')->paginate(getPaginate());
        return view('admin.support.tickets', compact('tickets', 'pageTitle'));
    }

    public function closedTicket()
    {
        $pageTitle = 'Agent Closed Tickets';
        $baseQuery = $this->baseQuery('closed');

        if (request()->export) {
            return exportData($baseQuery, request()->export, "SupportTicket");
        }
        $tickets = $baseQuery->with('agent')->paginate(getPaginate());
        return view('admin.support.tickets', compact('tickets', 'pageTitle'));
    }

    public function answeredTicket()
    {
        $pageTitle = 'Agent Answered Tickets';
        $baseQuery = $this->baseQuery('answered');

        if (request()->export) {
            return exportData($baseQuery, request()->export, "SupportTicket");
        }
        $tickets = $baseQuery->with('agent')->paginate(getPaginate());

        return view('admin.support.tickets', compact('tickets', 'pageTitle'));
    }

    private function baseQuery($scope = 'query')
    {
        return SupportTicket::$scope()->searchable(['name', 'subject', 'ticket', 'agent:username'])->filter(['status', 'priority'])
            ->agentTicket()->orderBy('id', getOrderBy());
    }
}
