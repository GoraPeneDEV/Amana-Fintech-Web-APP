<?php

namespace App\Http\Controllers;

use App\Traits\SupportTicketManager;

class TicketController extends Controller
{
    use SupportTicketManager;

    public function __construct()
    {
        if (auth('web')->check()) {
            $this->layout       = 'frontend';
            $this->redirectLink = 'ticket.view';
            $this->userType     = 'user';
            $this->column       = 'user_id';
            $this->user         = auth()->user();
            if ($this->user) {
                $this->layout = 'master';
            }
        } elseif (auth('merchant')->check()) {
            $this->layout       = 'frontend';
            $this->redirectLink = 'ticket.view';
            $this->userType     = 'merchant';
            $this->column       = 'merchant_id';
            $this->user         = auth('merchant')->user();
            if ($this->user) {
                $this->layout = 'merchant';
            }
        } elseif (auth('agent')->check()) {
            $this->layout       = 'frontend';
            $this->redirectLink = 'ticket.view';
            $this->userType     = 'agent';
            $this->column       = 'agent_id';
            $this->user         = auth('agent')->user();
            if ($this->user) {
                $this->layout = 'agent';
            }
        } else {
            $this->layout       = 'frontend';
            $this->redirectLink = 'ticket.view';
            $this->userType     = 'user';
            $this->column       = 'user_id';
            $this->user         = null;
        }
    }
}
