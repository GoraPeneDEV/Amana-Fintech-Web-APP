<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Lib\FormProcessor;
use App\Models\Form;
use App\Models\Ngo;
use App\Models\TransactionCharge;
use App\Rules\FileTypeValidate;
use Illuminate\Http\Request;

class NgoController extends Controller
{
    public function all()
    {
        $pageTitle  = 'NGO List';
        $baseQuery  = Ngo::searchable(['name'])->with('form')->orderBy('id', getOrderBy());

        if (request()->export) {
            return exportData($baseQuery, request()->export, "Ngo");
        }

        $microfinanceBills = $baseQuery->paginate(getPaginate());
        $charge            = TransactionCharge::where('slug', "microfinance_charge")->first();

        return view('admin.ngo.all', compact('pageTitle', 'microfinanceBills','charge'));
    }

    public function save(Request $request, $id = 0)
    {
        $imageValidation = $id ? 'nullable' : 'required';

        $request->validate([
            'name'           => 'required|max:255|unique:ngos,name,'.$id,
            'fixed_charge'   => 'nullable|numeric|min:0',
            'percent_charge' => 'nullable|numeric|min:0',
            'image'          => [$imageValidation, 'image', new FileTypeValidate(['jpg', 'jpeg', 'png'])],
        ]);

        if ($id) {
            $microfinance  = Ngo::findOrFail($id);
            $notify[] = ['success', 'NGO updated successfully'];
        } else {
            $microfinance  = new Ngo();
            $notify[] = ['success', 'NGO added successfully'];
        }

        if ($request->hasFile('image')) {
            try {
                $old         = $microfinance->image;
                $microfinance->image = fileUploader($request->image, getFilePath('microfinance'), getFileSize('microfinance'), $old);
            } catch (\Exception $exp) {
                $notify[] = ['error', 'Couldn\'t upload your image'];
                return back()->withNotify($notify);
            }
        }

        $microfinance->name           = $request->name;
        $microfinance->fixed_charge   = $request->fixed_charge;
        $microfinance->percent_charge = $request->percent_charge;
        $microfinance->save();

        return back()->withNotify($notify);
    }

    public function configure($id)
    {
        $microfinance   = Ngo::findOrFail($id);
        $pageTitle = 'Configure Microfinance - ' . $microfinance->name;
        $form      = Form::where('act', 'microfinance_' . $microfinance->id)->first();
        return view('admin.ngo.form', compact('pageTitle', 'microfinance', 'form'));
    }

    public function saveConfigure(Request $request, $id)
    {
        $setup               = Ngo::findOrFail($id);
        $formProcessor       = new FormProcessor();
        $generatorValidation = $formProcessor->generatorValidation();
        $request->validate($generatorValidation['rules'], $generatorValidation['messages']);
        $exist = Form::where('act', 'microfinance_' . $id)->first();
        $generatedForm = $formProcessor->generate('microfinance_' . $id, $exist, 'act');

        $setup->form_id = @$generatedForm->id ?? 0;
        $setup->save();

        $notify[] = ['success', 'NGO has been successfully configured'];
        return back()->withNotify($notify);
    }

    public function status($id)
    {
        return Ngo::changeStatus($id);
    }
}
