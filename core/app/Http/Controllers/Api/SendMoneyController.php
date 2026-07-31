<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Traits\SendMoneyOperation;

class SendMoneyController extends Controller
{
    use SendMoneyOperation;
}
