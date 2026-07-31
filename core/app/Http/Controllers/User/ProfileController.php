<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Rules\FileTypeValidate;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class ProfileController extends Controller
{
    public function profile()
    {
        $pageTitle = "Profile Setting";
        $user = auth()->user();
        return view('Template::user.profile_setting', compact('pageTitle', 'user'));
    }

    public function submitProfile(Request $request)
    {

        $request->validate([
            'firstname' => 'required|string',
            'lastname'  => 'required|string',
            'image'     => ['nullable', 'image', new FileTypeValidate(['jpg', 'jpeg', 'png'])]
        ], [
            'firstname.required' => 'The first name field is required',
            'lastname.required'  => 'The last name field is required'
        ]);


        $user = auth()->user();

        if ($request->hasFile('image')) {
            try {
                $old         = $user->image;
                $user->image = fileUploader($request->image, getFilePath('userProfile'), getFileSize('userProfile'), $old);
            } catch (\Exception $exp) {
                $notify[] = ['error', 'Couldn\'t upload your image'];
                return back()->withNotify($notify);
            }
        }

        $user->firstname = $request->firstname;
        $user->lastname = $request->lastname;

        $user->address = $request->address;
        $user->city = $request->city;
        $user->state = $request->state;
        $user->zip = $request->zip;

        $user->save();
        $notify[] = ['success', 'Profile updated successfully'];
        return back()->withNotify($notify);
    }

    public function changePassword()
    {
        $pageTitle = 'Change PIN';
        return view('Template::user.password', compact('pageTitle'));
    }

    public function submitPassword(Request $request)
    {

        $request->validate([
            'current_pin' => 'required',
            ...pinValidationRule(true),
        ]);

        $user = auth()->user();
        if (Hash::check($request->current_pin, $user->password)) {
            $password = Hash::make($request->pin);
            $user->password = $password;
            $user->save();
            $notify[] = ['success', 'PIN changed successfully'];
            return back()->withNotify($notify);
        } else {
            $notify[] = ['error', 'The PIN doesn\'t match!'];
            return back()->withNotify($notify);
        }
    }
}
