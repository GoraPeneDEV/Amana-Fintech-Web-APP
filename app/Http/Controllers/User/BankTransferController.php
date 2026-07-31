<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Traits\BankTransferOperation;

class BankTransferController extends Controller
{
    use BankTransferOperation;
  
}
