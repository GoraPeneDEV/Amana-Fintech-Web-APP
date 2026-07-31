<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Lib\FormProcessor;
use App\Models\Bank;
use App\Models\Form;
use App\Models\TransactionCharge;
use App\Rules\FileTypeValidate;
use Illuminate\Http\Request;

class BankController extends Controller
{
    public function all()
    {
        $pageTitle = 'Bank List';
        $baseQuery = Bank::searchable(['name'])->with('form')->orderBy('id', getOrderBy());
        if (request()->export) {
            return exportData($baseQuery, request()->export, "Bank");
        }

        $banks  = $baseQuery->paginate(getPaginate());
        $charge = TransactionCharge::where('slug', "bank_transfer")->first();
        return view('admin.bank.all', compact('pageTitle', 'banks', 'charge'));
    }

    public function save(Request $request, $id = 0)
    {
        $imageValidation = $id ? 'nullable' : 'required';

        $request->validate([
            'name'           => 'required|max:255|unique:banks,name,' . $id,
            'fixed_charge'   => 'nullable|numeric|min:0',
            'percent_charge' => 'nullable|numeric|min:0',
            'image'          => [$imageValidation, 'image', new FileTypeValidate(['jpg', 'jpeg', 'png'])],
        ]);

        if ($id) {
            $bank     = Bank::findOrFail($id);
            $notify[] = ['success', 'Bank updated successfully'];
        } else {
            $bank     = new Bank();
            $notify[] = ['success', 'Bank added successfully'];
        }

        if ($request->hasFile('image')) {
            try {
                $bank->image = fileUploader($request->image, getFilePath('bank_transfer'), getFileSize('bank_transfer'), $bank->image);
            } catch (\Exception $exp) {
                $notify[] = ['error', 'Couldn\'t upload your image'];
                return back()->withNotify($notify);
            }
        }

        $bank->name           = $request->name;
        $bank->fixed_charge   = $request->fixed_charge;
        $bank->percent_charge = $request->percent_charge;
        $bank->save();

        return back()->withNotify($notify);
    }

    public function configure($id)
    {
        $bank      = Bank::findOrFail($id);
        $pageTitle = 'Configure Bank Transfer form - ' . $bank->name;
        $form      = Form::where('act', 'bank_transfer_' . $bank->id)->first();
        return view('admin.bank.form', compact('pageTitle', 'bank', 'form'));
    }

    public function saveConfigure(Request $request, $id)
    {
        $setup          = Bank::findOrFail($id);
        $formProcessor  = new FormProcessor();
        $generatorValidation = $formProcessor->generatorValidation();
        $request->validate($generatorValidation['rules'], $generatorValidation['messages']);
        $exist = Form::where('act', 'bank_transfer_' . $id)->first();
        $generatedForm  = $formProcessor->generate('bank_transfer_' . $id, $exist, 'act');
        $setup->form_id = @$generatedForm->id ?? 0;
        $setup->save();

        $notify[] = ['success', 'Bank transfer configuration successful'];
        return back()->withNotify($notify);
    }

    public function status($id)
    {
        return Bank::changeStatus($id);
    }
}
