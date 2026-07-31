<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Investment;
use App\Models\InvestmentInterest;

class InvestmentHistoryController extends Controller
{
    public function all()
    {
        $pageTitle       = "Investment History";
        $query           = Investment::orderBy('id', getOrderBy());
        $totalPaidAmount = InvestmentInterest::sum('amount');

        $widget = [
            'total_invest_count'  => (clone $query)->count(),
            'total_invest_amount' => (clone $query)->sum('invest_amount'),
            'total_paid'          => $totalPaidAmount,
            'today_invest_amount' => (clone $query)->where('created_at', '>=', today())->sum('invest_amount'),
        ];

        $baseQuery = (clone $query)->with(['user', 'plan'])->searchable(['trx', 'user:username'])->dateFilter();

        $investments = (clone $baseQuery)->paginate(getPaginate());

        if (request()->export) {
            return exportData($baseQuery, request()->export, "investment", "A4 landscape");
        }

        return view('admin.investment.history', compact('investments', 'pageTitle', 'widget'));
    }

    public function details($id)
    {
        $pageTitle           = 'Investment Details';
        $investment          = Investment::with(['user', 'plan'])->findOrFail($id);
        $investmentInterests = InvestmentInterest::with('transactions')->where('investment_id', $id)->latest('id')->paginate(getPaginate());

        return view('admin.investment.details', compact('investment', 'pageTitle', 'investmentInterests'));
    }
}
