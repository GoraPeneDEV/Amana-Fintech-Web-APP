<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Lib\FormProcessor;
use App\Models\Category;
use App\Models\Institution;
use App\Models\Form;
use App\Models\TransactionCharge;
use App\Rules\FileTypeValidate;
use Illuminate\Http\Request;

class InstitutionController extends Controller
{
    public function all()
    {
        $pageTitle = 'Institutions List';
        $baseQuery = Institution::searchable(['name'])->with('form')->orderBy('id', getOrderBy());

        if (request()->export) {
            return exportData($baseQuery, request()->export, "Institution");
        }

        $institutions = $baseQuery->paginate(getPaginate());
        $categories   = Category::active()->get();
        $charge       = TransactionCharge::where('slug', "education_charge")->first();
        return view('admin.institution.all', compact('pageTitle', 'institutions', 'categories', 'charge'));
    }

    public function save(Request $request, $id = 0)
    {
        $imageValidation = $id ? 'nullable' : 'required';

        $request->validate([
            'name'           => 'required|max:255',
            'fixed_charge'   => 'nullable|numeric|min:0',
            'percent_charge' => 'nullable|numeric|min:0',
            'category_id'    => 'required|integer',
            'image'          => [$imageValidation, 'image', new FileTypeValidate(['jpg', 'jpeg', 'png'])],
        ]);

        $exist = Institution::where('id', '!=', $id)->where('category_id', $request->category_id)->where('name', $request->name)->exists();

        if ($exist) {
            $notify[] = ['error', 'The institution name already has been taken'];
            return back()->withNotify($notify);
        }
        if ($id) {
            $fee      = Institution::findOrFail($id);
            $notify[] = ['success', 'Institution updated successfully'];
        } else {
            $fee      = new Institution();
            $notify[] = ['success', 'Institution added successfully'];
        }

        if ($request->hasFile('image')) {
            try {
                $old        = $fee->image;
                $fee->image = fileUploader($request->image, getFilePath('education_fee'), getFileSize('education_fee'), $old);
            } catch (\Exception $exp) {
                $notify[] = ['error', 'Couldn\'t upload your image'];
                return back()->withNotify($notify);
            }
        }

        $fee->name           = $request->name;
        $fee->fixed_charge   = $request->fixed_charge;
        $fee->percent_charge = $request->percent_charge;
        $fee->category_id    = $request->category_id;
        $fee->save();

        return back()->withNotify($notify);
    }

    public function configure($id)
    {
        $fee       = Institution::findOrFail($id);
        $pageTitle = 'Configure Education fee - ' . $fee->name;
        $form      = Form::where('act', 'education_fee_' . $fee->id)->first();
        return view('admin.institution.form', compact('pageTitle', 'fee', 'form'));
    }

    public function saveConfigure(Request $request, $id)
    {
        $setup               = Institution::findOrFail($id);
        $formProcessor       = new FormProcessor();
        $generatorValidation = $formProcessor->generatorValidation();
        $request->validate($generatorValidation['rules'], $generatorValidation['messages']);
        $exist = Form::where('act', 'education_fee_' . $id)->first();
        $generatedForm = $formProcessor->generate('education_fee_' . $id, $exist, 'act');

        $setup->form_id = @$generatedForm->id ?? 0;
        $setup->save();

        $notify[] = ['success', 'Institution has been configured successfully'];
        return back()->withNotify($notify);
    }

    public function status($id)
    {
        return Institution::changeStatus($id);
    }
}
