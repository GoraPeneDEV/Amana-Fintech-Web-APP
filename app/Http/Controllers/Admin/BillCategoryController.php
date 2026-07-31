<?php

namespace App\Http\Controllers\Admin;

use App\Constants\Status;
use App\Http\Controllers\Controller;
use App\Models\ApiProvider;
use App\Models\BillCategory;
use App\Rules\FileTypeValidate;
use Exception;
use Illuminate\Http\Request;

class BillCategoryController extends Controller
{
    public function all()
    {
        $pageTitle = 'Bill Category List';
        $baseQuery = BillCategory::searchable(['name'])->orderBy('id', getOrderBy());

        if (request()->export) {
            return exportData($baseQuery, request()->export, "BillCategory");
        }

        $utilityBills = $baseQuery->paginate(getPaginate());
        $provider     = ApiProvider::whereJsonContains('module', 'utility_bill')->first();

        return view('admin.company.category', compact('pageTitle', 'utilityBills', 'provider'));
    }

    public function save(Request $request, $id = 0)
    {
        $imageValidation = $id ? 'nullable' : 'required';

        $request->validate([
            'name'           => 'required|max:255',
            'image'          => [$imageValidation, 'image', new FileTypeValidate(['jpg', 'jpeg', 'png'])],
        ]);

        $utility  = BillCategory::findOrFail($id);
        $notify[] = ['success', 'Utility bill setting updated successfully'];

        if ($request->hasFile('image')) {
            try {
                $old         = $utility->image;
                $utility->image = fileUploader($request->image, getFilePath('utility'), getFileSize('utility'), $old);
            } catch (\Exception $exp) {
                $notify[] = ['error', 'Couldn\'t upload your image'];
                return back()->withNotify($notify);
            }
        }

        $utility->name  = $request->name;
        $utility->save();

        return back()->withNotify($notify);
    }

    public function status($id)
    {
        return BillCategory::changeStatus($id);
    }

    public function import()
    {
        $provider = ApiProvider::whereJsonContains('module', 'utility_bill')->where('status->utility_bill', Status::YES)->first();

        if (!$provider) {
            $notify[] = ['error', 'At least one automated utility bill provider integration is required to import biller categories.'];
            return back()->withNotify($notify);
        }

        $className = 'App\\Lib\\UtilityBillProvider\\' . $provider->alias;
        $totalImport = (new $className())->importBillerCategory();

        try {

            $notify[] = ['success', 'Successfully imported ' . $totalImport . ' biller categories.'];
            return back()->withNotify($notify);
        } catch (Exception  $ex) {
            $notify[] = ['error', $ex->getMessage()];
            return back()->withNotify($notify);
        }
    }
}
