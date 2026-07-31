<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Charity;
use App\Models\Donation;
use App\Rules\FileTypeValidate;
use Illuminate\Http\Request;

class DonationController extends Controller
{
    public function donation()
    {
        $query  = Donation::orderBy('id', getOrderBy());
        $widget = [
            'today'      => (clone $query)->whereDate('created_at', now()->today())->sum('amount'),
            'yesterday'  => (clone $query)->whereDate('created_at', now()->yesterday())->sum('amount'),
            'all'        => (clone $query)->sum('amount'),
            'this_month' => (clone $query)->whereMonth('created_at', now()->month)->whereYear('created_at', now()->year)->sum('amount'),
        ];
        $transactions = $query->searchable(['trx', 'user:username'])->dateFilter()->paginate(getPaginate());
        $pageTitle    = "Donation History";

        if (request()->export) {
            return exportData($query, request()->export, "Donation", "A4 landscape");
        }

        return view('admin.donation.history', compact('transactions', 'pageTitle', 'widget'));
    }


    public function all()
    {
        $pageTitle = 'Charity List';
        $baseQuery = Charity::searchable(['name'])->orderBy('id', getOrderBy());

        if (request()->export) {
            return exportData($baseQuery, request()->export, "Charity");
        }

        $donations = $baseQuery->paginate(getPaginate());
        return view('admin.donation_setting.all', compact('pageTitle', 'donations'));
    }

    public function save(Request $request, $id = 0)
    {
        $imageValidation = $id ? 'nullable' : 'required';

        $request->validate([
            'name'    => 'required|max:255|unique:charities,name,' . $id,
            'details' => 'required',
            'image'   => [$imageValidation, 'image', new FileTypeValidate(['jpg', 'jpeg', 'png'])],
        ]);

        if ($id) {
            $operator = Charity::findOrFail($id);
            $notify[] = ['success', 'Charity updated successfully'];
        } else {
            $operator = new Charity();
            $notify[] = ['success', 'Charity added successfully'];
        }

        if ($request->hasFile('image')) {
            try {
                $old = $operator->image;
                $operator->image = fileUploader($request->image, getFilePath('donation'), getFileSize('donation'), $old);
            } catch (\Exception $exp) {
                $notify[] = ['error', 'Couldn\'t upload your image'];
                return back()->withNotify($notify);
            }
        }

        $operator->name    = $request->name;
        $operator->details = $request->details;
        $operator->save();

        return back()->withNotify($notify);
    }

    public function status($id)
    {
        return Charity::changeStatus($id);
    }


   
}
