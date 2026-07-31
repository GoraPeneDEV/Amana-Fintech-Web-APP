<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ModuleSetting;

class ModuleSettingController extends Controller
{

    public function index()
    {
        $pageTitle = "Modules Settings";
        $modules   = ModuleSetting::get();

        return view('admin.setting.module_setting', compact('pageTitle', 'modules'));
    }

    public function update($id)
    {
        $module = ModuleSetting::findOrFail($id);

        if ($module->status == 1) {
            $module->status = 0;
        } else {
            $module->status = 1;
        }

        $module->save();

        return response()->json([
            'success'    => true,
            'new_status' => $module->status
        ]);
    }
}
