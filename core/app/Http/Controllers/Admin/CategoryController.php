<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Rules\FileTypeValidate;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    public function all(Request $request)
    {
        $pageTitle  = 'All Categories';
        $baseQuery  = Category::searchable(['name'])->orderBy('id', getOrderBy());

        if (request()->export) {
            return exportData($baseQuery, request()->export, "Category");
        }

        $categories = $baseQuery->paginate(getPaginate());

        return view('admin.category.list', compact('pageTitle', 'categories'));
    }

    public function save(Request $request, $id = 0)
    {
        $imageValidate = $id ? 'nullable' : 'required';
        $validate      = [
            'name'           => 'required|max: 40|unique:categories,name,' . $id,
            'image'          => [$imageValidate, new FileTypeValidate(['jpg', 'jpeg', 'png'])],
        ];
        $request->validate($validate);

        if ($id == 0) {
            $category     = new Category();
            $notification = 'Category added successfully.';
        } else {
            $category         = Category::findOrFail($id);
            $notification     = 'Category updated successfully';
        }

        if ($request->hasFile('image')) {
            $oldImage = $category->image;
            try {
                $category->image = fileUploader($request->image, getFilePath('category'), getFileSize('category'), $oldImage);
            } catch (\Exception $e) {
                $notify[] = ['error', 'Image could not be uploaded'];
                return back()->withNotify($notify);
            }
        }

        $category->name           = $request->name;
        $category->save();

        $notify[] = ['success', $notification];
        return back()->withNotify($notify);
    }

    public function status($id)
    {
        return Category::changeStatus($id);
    }
}
