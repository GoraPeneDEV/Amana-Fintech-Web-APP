<?php

namespace App\Http\Controllers\Admin;

use App\Constants\Status;
use App\Http\Controllers\Controller;
use App\Models\InvestPlan;
use App\Models\InvestTime;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class InvestmentPlanController extends Controller
{
    public function all(Request $request)
    {
        $pageTitle = 'All Investment Plan';
        $baseQuery = InvestPlan::searchable(['name'])
            ->orderBy('invest_type')
            ->orderBy('min_invest')
            ->orderBy('id', getOrderBy());

        if ($request->export) {
            return exportData($baseQuery, $request->export, "InvestPlan");
        }

        $investPlans = $baseQuery->paginate(getPaginate());
        $investTimes = InvestTime::active()->get();


        return view('admin.investment.plan', compact('pageTitle', 'investPlans', 'investTimes'));
    }
    public function store(Request $request)
    {
        $this->validation($request);

        $investPlan                  = new InvestPlan();
        $investPlan->name            = $request->name;
        $investPlan->description     = $request->description;
        $investPlan->invest_type     = $request->invest_type;
        $investPlan->fixed_amount    = $request->fixed_amount ?? 0;
        $investPlan->min_invest      = $request->min_invest ?? 0;
        $investPlan->max_invest      = $request->max_invest ?? 0;
        $investPlan->interest_type   = $request->interest_type;
        $investPlan->interest_amount = $request->interest_amount;
        $investPlan->time_id         = $request->time_id;
        $investPlan->return_type     = $request->return_type;
        $investPlan->repeat_times    = $request->repeat_times ?? 0;
        $investPlan->capital_back    = $request->capital_back;
        $investPlan->save();

        $notify[] = ['success', 'Plan added successfully.'];
        return back()->withNotify($notify);
    }

    public function update(Request $request, $id)
    {

        $this->validation($request, $id);

        $investPlan           = InvestPlan::findOrFail($id);

        $investPlan->name            = $request->name;
        $investPlan->description     = $request->description;
        $investPlan->invest_type     = $request->invest_type;
        $investPlan->fixed_amount    = $request->fixed_amount ?? 0;
        $investPlan->min_invest      = $request->min_invest ?? 0;
        $investPlan->max_invest      = $request->max_invest ?? 0;
        $investPlan->interest_type   = $request->interest_type;
        $investPlan->interest_amount = $request->interest_amount;
        $investPlan->time_id         = $request->time_id;
        $investPlan->return_type     = $request->return_type;
        $investPlan->repeat_times    = $request->repeat_times ?? 0;
        $investPlan->capital_back    = $request->capital_back;

        if ($investPlan->return_type == Status::INVEST_LIFETIME) {
            $investPlan->capital_back = Status::NO;
        }

        $investPlan->save();

        $notify[] = ['success', 'Plan updated successfully.'];
        return back()->withNotify($notify);
    }

    public function status($id)
    {
        return InvestPlan::changeStatus($id);
    }

    private function validation($request, $id = 0)
    {
        $request->validate([
            'name'            => 'required|max:40|unique:invest_plans,name,' . $id,
            'description'     => 'required',
            'invest_type'     => ['required', Rule::in(Status::INVEST_TYPE_FIXED, Status::INVEST_TYPE_RANGE)],
            'fixed_amount'    => 'nullable|numeric|gte:0',
            'min_invest'      => 'nullable|numeric|gte:0',
            'max_invest'      => 'nullable|numeric|gte:0',
            'interest_type'   => ['required', Rule::in(Status::INTEREST_TYPE_FIXED, Status::INTEREST_TYPE_PERCENT)],
            'interest_amount' => 'required|numeric|gte:0',
            'time_id'         => 'required|integer|exists:invest_times,id',
            'return_type'     => ['required', Rule::in(Status::INVEST_LIFETIME, Status::INVEST_REPEAT)],
            'repeat_times'    => 'nullable|numeric|gte:0',
            'capital_back'    => ['nullable', Rule::in(Status::YES, Status::NO)],
        ]);
    }
}
