<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Merchant;
use App\Models\Offer;
use App\Rules\FileTypeValidate;

class ManageOffersController extends Controller
{
    public function index()
    {
        $pageTitle     = "Manage Payment Offers";
        $offers        = Offer::searchable(['name'])->paginate(getPaginate());
        return view('admin.offers.index', compact('pageTitle', 'offers'));
    }

    public function create()
    {
        $pageTitle  = "Create Payment Offer";
        $merchants   = Merchant::active()->get();
        return view('admin.offers.create', compact('pageTitle', 'merchants'));
    }

    public function save(Request $request, $id)
    {
        $request->validate([
            "offer_name"       => 'required|string|max:40',
            "discount_type"    => 'required|integer|between:1,2',
            "merchant_id"      => 'required|integer|exists:merchants,id',
            "amount"           => 'required|numeric',
            "min_payment"      => 'required|numeric',
            "maximum_discount" => 'nullable|numeric',
            "start_date"       => 'required|date',
            "end_date"         => 'required|date',
            "link"             => 'nullable',
            "description"      => 'required|string|max:70',
            'image'            => ['nullable', 'image', new FileTypeValidate(['jpeg', 'jpg', 'png'])],
        ]);

        if ($id == 0) {
            $offer    = new Offer();
            $notify[] = ['success', 'Offer Created Successfully'];
        } else {
            $offer    = Offer::findOrFail($id);
            $notify[] = ['success', 'Offer Updated Successfully'];
        }

        $startDate = date('Y-m-d', strtotime($request->start_date));
        $endDate   = date('Y-m-d', strtotime($request->end_date));

        if ($request->hasFile('image')) {
            $oldImage     = $offer->image;
            $offer->image = fileUploader($request->image, getFilePath('offer'), getFileSize('offer'), $oldImage);
        }

        $offer->name               = $request->offer_name;
        $offer->merchant_id        = $request->merchant_id;
        $offer->discount_type      = $request->discount_type;
        $offer->amount             = $request->amount;
        $offer->start_date         = $startDate;
        $offer->end_date           = $endDate;
        $offer->description        = $request->description;
        $offer->link               = $request->link;
        $offer->min_payment        = $request->min_payment;
        $offer->maximum_discount   = $request->maximum_discount ?? 0;

        $offer->save();

        return redirect()->back()->withNotify($notify);
    }

    public function edit($id)
    {
        $pageTitle     = "Edit Offer";
        $offer         = Offer::with('merchant')->findOrFail($id);
        $merchants     = Merchant::active()->get();
        return view('admin.offers.create', compact('pageTitle', 'offer', 'merchants'));
    }

    public function status($id)
    {
        return Offer::changeStatus($id);
    }
}
