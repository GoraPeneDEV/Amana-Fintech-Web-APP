<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Traits\SupportTicketManager;

class TicketController extends Controller
{
    use SupportTicketManager;

    public function __construct()
    {
        $this->user = auth()->user();
        $this->apiRequest = true;
        $this->setUserTypeAndColumn();
    }

    public function setUserTypeAndColumn()
    {
        $user = auth()->user();
        $userType = substr($user->getTable(), 0, -1);

        [$this->userType, $this->column] = match ($userType) {
            'user'     => ['user', 'user_id'],
            'agent'    => ['agent', 'agent_id'],
            'merchant' => ['merchant', 'merchant_id'],
            default    => ['user', 'user_id'],
        };
    }
}
