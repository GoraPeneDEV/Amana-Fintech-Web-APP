<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Company;
use App\Traits\UtilityOperation;

class PayBillController extends Controller
{
    use UtilityOperation;

    public function companyDetails($id)
    {

        $company   = Company::where('id', $id)->active()->with('form')->firstOrFailWithApi('Company');
        $message[] = "Company Details";

        return apiResponse('company_details', 'success', $message, [
            'company' => $company
        ]);
    }
}
