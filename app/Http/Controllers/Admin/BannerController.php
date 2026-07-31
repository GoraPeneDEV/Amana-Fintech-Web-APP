<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Banner;
use App\Models\ModuleSetting;
use App\Rules\FileTypeValidate;
use Illuminate\Http\Request;

class BannerController extends Controller
{
    public function all(Request $request)
    {
        $pageTitle = 'All Banners';
        $baseQuery = Banner::orderBy('id', getOrderBy());

        if (request()->export) {
            return exportData($baseQuery, request()->export, "Banner");
        }

        $modules = ModuleSetting::active()->where('user_type', 'USER')->get();
        $banners = $baseQuery->paginate(getPaginate());

        return view('admin.banner.list', compact('pageTitle', 'banners', 'modules'));
    }

    public function save(Request $request, $id = 0)
    {
        $imageValidate = $id ? 'nullable' : 'required';
        $validate      = [
            'link'         => 'nullable',
            'link_type'    => 'required|in:1,2',
            'description'  => 'required',
            'image'        => [$imageValidate, new FileTypeValidate(['jpg', 'jpeg', 'png'])],
        ];

        $request->validate($validate);

        if ($id == 0) {
            $banner       = new Banner();
            $notification = 'Banner added successfully.';
        } else {
            $banner       = Banner::findOrFail($id);
            $notification = 'Banner updated successfully';
        }

        if ($request->hasFile('image')) {
            $oldImage = $banner->image;
            try {
                $banner->image = fileUploader($request->image, getFilePath('banner'), getFileSize('banner'), $oldImage);
            } catch (\Exception $e) {
                $notify[] = ['error', 'Image could not be uploaded'];
                return back()->withNotify($notify);
            }
        }

        $banner->link = $request->link_type == 1 ? $request->link : $request->module;
        $banner->type    = $request->link_type;
        $banner->description  = $request->description;
        $banner->save();

        $notify[] = ['success', $notification];
        return back()->withNotify($notify);
    }

    public function status($id)
    {
        return Banner::changeStatus($id);
    }
}
