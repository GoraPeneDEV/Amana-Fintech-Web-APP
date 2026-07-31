<?php

namespace App\Http\Controllers\Agent;

use App\Constants\Status;
use App\Http\Controllers\Controller;
use App\Lib\FormProcessor;
use App\Lib\GoogleAuthenticator;
use App\Models\DeviceToken;
use App\Models\Form;
use App\Models\Transaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;


class AgentController extends Controller
{
    public function home()
    {
        $pageTitle    = 'Dashboard';
        $agent        = auth('agent')->user();
        $transactions = Transaction::where('agent_id', $agent->id)->searchable(['trx'])->orderBy('id', 'desc')->take(5)->get();
        $qrCodeUrl    = getQrCodeUrl('agent');

        return view('Template::agent.dashboard', compact('pageTitle', 'transactions', 'qrCodeUrl'));
    }


    public function show2faForm()
    {
        $ga = new GoogleAuthenticator();
        $agent = auth('agent')->user();
        $secret = $ga->createSecret();
        $qrCodeUrl = $ga->getQRCodeGoogleUrl($agent->username . '@' . gs('site_name'), $secret);
        $pageTitle = '2FA Security';
        return view('Template::agent.twofactor', compact('pageTitle', 'secret', 'qrCodeUrl'));
    }

    public function create2fa(Request $request)
    {
        $agent = auth('agent')->user();
        $request->validate([
            'key' => 'required',
            'code' => 'required',
        ]);
        $response = verifyG2fa($agent, $request->code, $request->key);
        if ($response) {
            $agent->tsc = $request->key;
            $agent->ts = Status::ENABLE;
            $agent->save();
            $notify[] = ['success', 'Two factor authenticator activated successfully'];
            return back()->withNotify($notify);
        } else {
            $notify[] = ['error', 'Wrong verification code'];
            return back()->withNotify($notify);
        }
    }

    public function disable2fa(Request $request)
    {
        $request->validate([
            'code' => 'required',
        ]);

        $agent = auth('agent')->user();
        $response = verifyG2fa($agent, $request->code);
        if ($response) {
            $agent->tsc = null;
            $agent->ts = Status::DISABLE;
            $agent->save();
            $notify[] = ['success', 'Two factor authenticator deactivated successfully'];
        } else {
            $notify[] = ['error', 'Wrong verification code'];
        }
        return back()->withNotify($notify);
    }


    public function kycForm()
    {
        if (auth('agent')->user()->kv == Status::KYC_PENDING) {
            $notify[] = ['error', 'Your KYC is under review'];
            return to_route('agent.home')->withNotify($notify);
        }
        if (auth('agent')->user()->kv == Status::KYC_VERIFIED) {
            $notify[] = ['error', 'You are already KYC verified'];
            return to_route('agent.home')->withNotify($notify);
        }
        $pageTitle = 'KYC Form';
        $form = Form::where('act', 'agent_kyc')->first();
        return view('Template::agent.kyc.form', compact('pageTitle', 'form'));
    }

    public function kycData()
    {
        $agent = auth('agent')->user();
        $pageTitle = 'KYC Data';
        abort_if($agent->kv == Status::VERIFIED, 403);
        return view('Template::agent.kyc.info', compact('pageTitle', 'agent'));
    }

    public function kycSubmit(Request $request)
    {
        $form = Form::where('act', 'agent_kyc')->firstOrFail();
        $formData = $form->form_data;
        $formProcessor = new FormProcessor();
        $validationRule = $formProcessor->valueValidation($formData);
        $request->validate($validationRule);
        $agent = auth('agent')->user();
        foreach (@$agent->kyc_data ?? [] as $kycData) {
            if ($kycData->type == 'file') {
                fileManager()->removeFile(getFilePath('verify') . '/' . $kycData->value);
            }
        }
        $agentData = $formProcessor->processFormData($request, $formData);
        $agent->kyc_data = $agentData;
        $agent->kyc_rejection_reason = null;
        $agent->kv = Status::KYC_PENDING;
        $agent->save();

        $notify[] = ['success', 'KYC data submitted successfully'];
        return to_route('agent.home')->withNotify($notify);
    }

    public function userData()
    {
        $agent = auth('agent')->user();

        if ($agent->profile_complete == Status::YES) {
            return to_route('agent.home');
        }
        $pageTitle  = 'User Data';
        return view('Template::agent.user_data', compact('pageTitle'));
    }

    public function userDataSubmit(Request $request)
    {
        $agent = auth('agent')->user();
        if ($agent->profile_complete == Status::YES) {
            return to_route('agent.home');
        }

        $request->validate([
            'firstname' => 'required',
            'lastname'  => 'required',
            'email'     => 'required|string|email|unique:users',
            ...pinValidationRule(true),
            'username'  => 'required|unique:agents|min:6',
        ]);

        if (preg_match("/[^a-z0-9_]/", trim($request->username))) {
            $notify[] = ['info', 'Username can contain only small letters, numbers and underscore.'];
            $notify[] = ['error', 'No special character, space or capital letters in username.'];
            return back()->withNotify($notify)->withInput($request->all());
        }

        $agent->firstname        = $request->firstname;
        $agent->lastname         = $request->lastname;
        $agent->email            = $request->email;
        $agent->username         = $request->username;
        $agent->address          = $request->address;
        $agent->city             = $request->city;
        $agent->state            = $request->state;
        $agent->zip              = $request->zip;
        $agent->password         = Hash::make($request->pin);
        $agent->profile_complete = Status::YES;
        $agent->kv               = gs('kv') ? Status::NO : Status::YES;
        $agent->ev               = gs('ev') ? Status::NO : Status::YES;
        $agent->ts               = Status::DISABLE;
        $agent->tv               = Status::ENABLE;
        $agent->save();

        return to_route('agent.home');
    }


    public function addDeviceToken(Request $request)
    {

        $validator = Validator::make($request->all(), [
            'token' => 'required',
        ]);

        if ($validator->fails()) {
            return ['success' => false, 'errors' => $validator->errors()->all()];
        }

        $deviceToken = DeviceToken::where('token', $request->token)->where('agent_id', auth('agent')->user()->id)->first();

        if ($deviceToken) {
            return ['success' => true, 'message' => 'Already exists'];
        }

        $deviceToken          = new DeviceToken();
        $deviceToken->user_id = auth('agent')->user()->id;
        $deviceToken->token   = $request->token;
        $deviceToken->is_app  = Status::NO;
        $deviceToken->save();

        return ['success' => true, 'message' => 'Token saved successfully'];
    }

    public function downloadAttachment($fileHash)
    {
        $filePath = decrypt($fileHash);
        $extension = pathinfo($filePath, PATHINFO_EXTENSION);
        $title = slug(gs('site_name')) . '- attachments.' . $extension;
        try {
            $mimetype = mime_content_type($filePath);
        } catch (\Exception $e) {
            $notify[] = ['error', 'File does not exists'];
            return back()->withNotify($notify);
        }
        header('Content-Disposition: attachment; filename="' . $title);
        header("Content-Type: " . $mimetype);
        return readfile($filePath);
    }



    public function transactions()
    {
        $pageTitle    = 'Transactions';
        $remarks      = Transaction::distinct('remark')->orderBy('remark')->where('agent_id', '!=', 0)->whereNotNull('remark')->get('remark');
        $transactions = Transaction::where('agent_id', auth('agent')->id())->searchable(['trx'])->filter(['trx_type', 'remark'])->orderBy('id', 'desc')->paginate(getPaginate());

        return view('Template::agent.transactions', compact('pageTitle', 'transactions', 'remarks'));
    }


    public function statements(Request $request)
    {
        $pageTitle = 'Statements';
        $agent     = auth('agent')->user();

        $year  = $request->year ?? now()->year;
        $month = $request->month ?? now()->month;


        $startingBalance = Transaction::where('agent_id', $agent->id)
            ->whereYear('created_at', $year)
            ->whereMonth('created_at', '<', $month)
            ->orderBy('created_at', 'desc')
            ->value('post_balance') ?? 0;

        $transactions = Transaction::where('agent_id', $agent->id)
            ->whereYear('created_at', $year)
            ->whereMonth('created_at', $month);

        $totalTransactionAmount = (clone $transactions)->sum('amount');
        $totalTransactionCount  = (clone $transactions)->count();
        $totalCommission        = (clone $transactions)->whereIn('remark', ['cash_out_commission', 'cash_in_commission'])->sum('amount');
        $transactionHistory     = (clone $transactions)->orderBy('id', 'desc')->searchable(['trx'])->paginate(getPaginate());

        return view('Template::agent.statements', compact('pageTitle', 'startingBalance', 'totalTransactionAmount', 'totalTransactionCount', 'totalCommission', 'year', 'month', 'transactionHistory'));
    }

    public function notificationSetting()
    {
        $pageTitle = 'Notification Setting';
        $agent     = auth('agent')->user();
        return view('Template::agent.notification_setting', compact('pageTitle', 'agent'));
    }

    public function notificationSettingsUpdate(Request $request)
    {
        $agent                              = auth('agent')->user();
        $agent->en                          = $request->en ? Status::ENABLE : Status::DISABLE;
        $agent->sn                          = $request->sn ? Status::ENABLE : Status::DISABLE;
        $agent->pn                          = $request->pn ? Status::ENABLE : Status::DISABLE;
        $agent->is_allow_promotional_notify = $request->is_allow_promotional_notify ? Status::YES : Status::NO;
        $agent->save();

        $notify[] = ['success', 'Notification settings updated successfully'];

        return back()->withNotify($notify);
    }
}
