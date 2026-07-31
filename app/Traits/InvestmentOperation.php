<?php

namespace App\Traits;

use App\Constants\Status;
use App\Models\Investment;
use App\Models\InvestmentInterest;
use App\Models\InvestPlan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

trait InvestmentOperation
{

    public function all()
    {

        $pageTitle = 'Investment Plan';
        $view      = 'Template::user.investment.index';
        $plans     = InvestPlan::active()->with('investTime')->paginate(getPaginate());

        return responseManager("investment", $pageTitle, 'success', compact('view', 'pageTitle', 'plans'));
    }

    public function show($id)
    {

        $plan      = InvestPlan::active()->find($id);
        $pageTitle = 'Investment Plan Subscription';

        if (! $plan) {
            return responseManager('not_found', 'The plan card not found');
        }

        if (request('invest_amount')) {
            $request = request();
            if ($plan->invest_type == Status::INVEST_TYPE_FIXED) {
                if ($request->invest_amount != $plan->fixed_amount) {
                    return responseManager("invalid_amount", 'error', "The amount should be " . showAmount($plan->fixed_amount));
                }
            } else {
                if ($request->invest_amount < $plan->min_invest || $request->invest_amount > $plan->max_invest) {
                    $message = "The invest amount should be between " . showAmount($plan->min_invest) . " - " . showAmount($plan->max_invest);
                    return responseManager("invalid_amount", 'error', $message);
                }
            }
        }
        $view = 'Template::user.investment.show';
        $user = auth()->user();

        return responseManager("investment", "Investment", "success", [
            'view'      => $view,
            'pageTitle' => $pageTitle,
            'plan'      => $plan,
            'user'      => $user,
        ]);
    }

    public function store(Request $request)
    {

        $validator = Validator::make($request->all(), [
            'plan_id'       => "required|int",
            'invest_amount' => "required|numeric",
            'remark'        => 'required|in:' . implode(",", getOtpRemark()),
            ...getOtpValidationRules(),
        ]);

        if ($validator->fails()) {
            return apiResponse('validation_error', 'error', $validator->errors()->all());
        }

        $plan = InvestPlan::active()->find($request->plan_id);

        if (! $plan) {
            return apiResponse('not_found', 'error', ['The investment plan not found']);
        }

        if ($plan->invest_type == Status::INVEST_TYPE_FIXED) {
            if ($request->invest_amount != $plan->fixed_amount) {
                return apiResponse("invalid_amount", 'error', ["The amount should be " . showAmount($plan->fixed_amount)]);
            }
        } else {
            if ($request->invest_amount < $plan->min_invest || $request->invest_amount > $plan->max_invest) {
                $message[] = "The invest amount should be between " . showAmount($plan->min_invest) . " - " . showAmount($plan->max_invest);
                return apiResponse("invalid_amount", 'error', $message);
            }
        }

        $user = auth()->user();

        if ($request->invest_amount > $user->balance) {
            return apiResponse("insufficient", 'error', ['Sorry! Insufficient balance']);
        }

        $details = [
            'plan_id'       => $request->plan_id,
            'invest_amount' => $request->invest_amount,
        ];

        return storeAuthorizedTransactionData("investment", $details);
    }

    public function details($id)
    {

        $pageTitle = 'Investment Details';
        $user      = auth()->user();
        $view      = 'Template::user.investment.details';

        $investment = Investment::where('id', $id)->where('user_id', $user->id)->first();

        if (! $investment) {
            $notify = "The investment transaction is not found";
            return responseManager('not_fund', $notify);
        }

        $investmentInterests = InvestmentInterest::with('transactions')->where('investment_id', $id)->where('user_id', $user->id)->get();

        $totalPaid       = $investmentInterests->count();
        $totalPaidAmount = $investmentInterests->sum('amount');
        $shouldPay       = $investment->total_interest_amount - $totalPaidAmount;

        $lastPaidTime = InvestmentInterest::where('investment_id', $id)
            ->where('user_id', $user->id)
            ->latest('id')
            ->value('created_at');

        return responseManager("investment_details", $pageTitle, 'success', compact('view', 'pageTitle', 'investment', 'investmentInterests', 'totalPaid', 'totalPaidAmount', 'shouldPay', 'lastPaidTime'));
    }

    public function history(Request $request)
    {
        $pageTitle   = 'Investment History';
        $view        = 'Template::user.investment.history';
        $investments = auth()->user()->investments()->searchable(['trx'])->with(['plan'])->orderBy('id', 'desc')->paginate(getPaginate());

        return responseManager("investment_history", $pageTitle, 'success', compact('view', 'pageTitle', 'investments'));
    }
}
