<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Traits\BankTransferOperation;

class BankTransferController extends Controller
{
   use BankTransferOperation;
}
