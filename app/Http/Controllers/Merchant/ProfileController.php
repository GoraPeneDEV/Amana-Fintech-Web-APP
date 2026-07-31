<?php

namespace App\Http\Controllers\Merchant;

use App\Http\Controllers\Controller;
use App\Rules\FileTypeValidate;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class ProfileController extends Controller
{
    public function profile()
    {
        $pageTitle = "Profile Setting";
        $merchant = auth('merchant')->user();
        return view('Template::merchant.profile_setting', compact('pageTitle', 'merchant'));
    }

    public function submitProfile(Request $request)
    {
        $request->validate([
            'firstname' => 'required|string',
            'lastname' => 'required|string',
            'image'    => ['nullable', 'image', new FileTypeValidate(['jpg', 'jpeg', 'png'])]
        ]);

        $merchant = auth('merchant')->user();
        $merchant->firstname = $request->firstname;
        $merchant->lastname = $request->lastname;
        $merchant->address = $request->address;
        $merchant->city = $request->city;
        $merchant->state = $request->state;
        $merchant->zip = $request->zip;

        if ($request->hasFile('image')) {
            $path = fileUploader($request->image, getFilePath('merchantProfile'), getFileSize('merchantProfile'), old('image'));
            $merchant->image = $path;
        }

        $merchant->save();
        $notify[] = ['success', 'Profile updated successfully'];
        return back()->withNotify($notify);
    }

    public function changePassword()
    {
        $pageTitle = 'Change PIN';
        return view('Template::merchant.password', compact('pageTitle'));
    }

    public function submitPassword(Request $request)
    {

        $request->validate([
            'current_pin' => 'required',
            ...pinValidationRule(true),
        ]);

        $merchant = auth('merchant')->user();
        if (Hash::check($request->current_pin, $merchant->password)) {
            $password = Hash::make($request->pin);
            $merchant->password = $password;
            $merchant->save();
            $notify[] = ['success', 'PIN changed successfully'];
            return back()->withNotify($notify);
        } else {
            $notify[] = ['error', 'The PIN doesn\'t match!'];
            return back()->withNotify($notify);
        }
    }
}
