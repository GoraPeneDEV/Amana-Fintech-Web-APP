<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\InvestmentInterest;

class InvestmentInterestController extends Controller
{
    public function all()
    {
        $pageTitle = 'Interest History';
        $baseQuery = InvestmentInterest::orderBy('id', getOrderBy())->searchable(['trx'])->dateFilter();

        $investmentInterests = $baseQuery->paginate(getPaginate());

        if (request()->export) {
            return exportData($baseQuery, request()->export, "investmentInterest", "A4 landscape");
        }

        return view('admin.investment.all_interest', compact('pageTitle', 'investmentInterests'));
    }
}
