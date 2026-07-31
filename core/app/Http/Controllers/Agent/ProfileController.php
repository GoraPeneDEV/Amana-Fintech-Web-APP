<?php

namespace App\Http\Controllers\Agent;

use App\Http\Controllers\Controller;
use App\Rules\FileTypeValidate;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class ProfileController extends Controller
{
    public function profile()
    {
        $pageTitle = "Profile Setting";
        $agent     = auth('agent')->user();
        return view('Template::agent.profile_setting', compact('pageTitle', 'agent'));
    }

    public function submitProfile(Request $request)
    {
        $request->validate([
            'firstname' => 'required|string',
            'lastname'  => 'required|string',
            'image'     => ['nullable', 'image', new FileTypeValidate(['jpg', 'jpeg', 'png'])]
        ]);

        $agent = auth('agent')->user();

        $agent->firstname = $request->firstname;
        $agent->lastname  = $request->lastname;

        $agent->address = $request->address;
        $agent->city    = $request->city;
        $agent->state   = $request->state;
        $agent->zip     = $request->zip;


        if ($request->hasFile('image')) {
            $path = fileUploader($request->image, getFilePath('agentProfile'), getFileSize('agentProfile'), old('image'));
            $agent->image = $path;
        }


        $agent->save();
        $notify[] = ['success', 'Profile updated successfully'];
        return back()->withNotify($notify);
    }

    public function changePassword()
    {
        $pageTitle = 'Change PIN';
        return view('Template::agent.password', compact('pageTitle'));
    }

    public function submitPassword(Request $request)
    {

        $request->validate([
            'current_pin' => 'required',
            ...pinValidationRule(true)
        ]);

        $agent = auth('agent')->user();
        if (Hash::check($request->current_pin, $agent->password)) {
            $password = Hash::make($request->pin);
            $agent->password = $password;
            $agent->save();
            $notify[] = ['success', 'PIN changed successfully'];
            return back()->withNotify($notify);
        } else {
            $notify[] = ['error', 'The PIN doesn\'t match!'];
            return back()->withNotify($notify);
        }
    }
}
