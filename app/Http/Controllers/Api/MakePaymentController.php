<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Traits\MakePaymentOperation;

class MakePaymentController extends Controller
{
   use MakePaymentOperation;
}
