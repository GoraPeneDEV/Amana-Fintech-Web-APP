<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\InvestTime;
use Illuminate\Http\Request;

class InvestmentTimeController extends Controller
{

    public function all(Request $request)
    {
        $pageTitle = 'All Investment Time';
        $baseQuery = InvestTime::searchable(['name'])->orderBy('id', getOrderBy());

        if ($request->export) {
            return exportData($baseQuery, $request->export, "InvestTime");
        }
        $investTimes = $baseQuery->paginate(getPaginate());

        return view('admin.investment.time', compact('pageTitle', 'investTimes'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|max:40|unique:invest_times,name',
            'time' => 'required|integer',
        ]);

        $investTime       = new InvestTime();
        $investTime->name = $request->name;
        $investTime->time = $request->time;
        $investTime->save();

        $notify[] = ['success', 'Investment Time added successfully.'];
        return back()->withNotify($notify);
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'name' => 'required|max:40|unique:invest_times,name,' . $id,
            'time' => 'required|integer',
        ]);

        $investTime = InvestTime::findOrFail($id);

        $investTime->name = $request->name;
        $investTime->time = $request->time;
        $investTime->save();

        $notify[] = ['success', 'Investment Time updated successfully.'];
        return back()->withNotify($notify);
    }

    public function status($id)
    {
       
        return InvestTime::changeStatus($id);
    }
}
