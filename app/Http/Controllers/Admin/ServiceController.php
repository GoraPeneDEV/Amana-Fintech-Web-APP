<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\CashIn;
use App\Models\CashOut;
use App\Models\Donation;
use App\Models\MakePayment;
use App\Models\MoneyRequest;
use App\Models\SendMoney;
use Illuminate\Http\Request;

class ServiceController extends Controller
{
    public function sendMoney(Request $request)
    {
        return $this->handleTransaction($request, SendMoney::query(), 'Send Money History', ['trx', 'user:username'], 'admin.service.send_money');
    }
    public function requestMoney(Request $request)
    {
        return $this->handleTransaction($request, MoneyRequest::query(), 'Request Money History', ['trx', 'requestSender:username', 'requestReceiver:username'], 'admin.service.request_money');
    }

    public function cashOut(Request $request)
    {
        return $this->handleTransaction($request, CashOut::query(), 'Cash out History', ['trx', 'user:username'], 'admin.service.cash_out', 'user_id', '!=');
    }

    public function cashIn(Request $request)
    {
        return $this->handleTransaction($request, CashIn::query(), 'Cash in History', ['trx', 'user:username', 'agent:username'], 'admin.service.cash_in', 'agent_id', '!=');
    }

    public function payment(Request $request)
    {
        return $this->handleTransaction($request, MakePayment::query(), 'Payment History', ['trx', 'user:username', 'merchant:username'], 'admin.service.payment', 'merchant_id', '!=');
    }

    public function donation(Request $request)
    {
        return $this->handleTransaction($request, Donation::query(), 'Donation History', ['trx', 'user:username'], 'admin.service.donation', 'user_id', '!=');
    }

    private function handleTransaction(Request $request, $query, string $pageTitle, array $searchable, string $viewName, string $filterColumn = null, string $filterOperator = null)
    {
        $baseQuery = $query->searchable($searchable)
            ->dateFilter()
            ->orderBy('id', getOrderBy());

        if ($filterColumn && $filterOperator) {
            $baseQuery->where($filterColumn, $filterOperator, 0);
        }

        if ($request->export) {
            return exportData($baseQuery, $request->export, class_basename($query->getModel()), "A4 landscape");
        }

        $totals = $this->getTransactionTotals(clone $baseQuery);
        $transactions = $baseQuery->with('user')->paginate(getPaginate());

        return view($viewName, compact('pageTitle', 'transactions', 'totals'));
    }

    private function getTransactionTotals($query): array
    {
        $totals = [
            'today'          => (clone $query)->whereDate('created_at', now()->today())->sum('amount'),
            'yesterday'      => (clone $query)->whereDate('created_at', now()->yesterday())->sum('amount'),
            'this_week'      => (clone $query)->whereBetween('created_at', [now()->startOfWeek(), now()->endOfWeek()])->sum('amount'),
            'previous_week'  => (clone $query)->whereBetween('created_at', [now()->subWeek()->startOfWeek(), now()->subWeek()->endOfWeek()])->sum('amount'),
            'this_month'     => (clone $query)->whereMonth('created_at', now()->month)->whereYear('created_at', now()->year)->sum('amount'),
            'previous_month' => (clone $query)->whereMonth('created_at', now()->subMonth()->month)->whereYear('created_at', now()->subMonth()->year)->sum('amount'),
            'this_year'      => (clone $query)->whereYear('created_at', now()->year)->sum('amount'),
            'all'            => (clone $query)->sum('amount'),
        ];

        if ($this->modelHasColumn($query, 'charge')) {
            $totals = array_merge($totals, [
                'today_charge'     => (clone $query)->whereDate('created_at', now()->today())->sum('charge'),
                'yesterday_charge' => (clone $query)->whereDate('created_at', now()->yesterday())->sum('charge'),
                'this_week_charge' => (clone $query)->whereBetween('created_at', [now()->startOfWeek(), now()->endOfWeek()])->sum('charge'),
                'all_charge'       => (clone $query)->sum('charge'),
            ]);
        }

        if ($this->modelHasColumn($query, 'commission')) {
            $totals = array_merge($totals, [
                'today_commission'     => (clone $query)->whereDate('created_at', now()->today())->sum('commission'),
                'yesterday_commission' => (clone $query)->whereDate('created_at', now()->yesterday())->sum('commission'),
                'this_week_commission' => (clone $query)->whereBetween('created_at', [now()->startOfWeek(), now()->endOfWeek()])->sum('commission'),
                'all_commission'       => (clone $query)->sum('commission'),
            ]);
        }

        return $totals;
    }

    private function modelHasColumn($query, $column): bool
    {
        $model = $query->getModel();
        return \Schema::hasColumn($model->getTable(), $column);
    }

}
