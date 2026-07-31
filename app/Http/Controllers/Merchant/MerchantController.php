<?php

namespace App\Http\Controllers\Merchant;

use App\Constants\Status;
use App\Http\Controllers\Controller;
use App\Lib\FormProcessor;
use App\Lib\GoogleAuthenticator;
use App\Models\DeviceToken;
use App\Models\Form;
use App\Models\MakePayment;
use App\Models\Transaction;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;


class MerchantController extends Controller
{
    public function home()
    {
        $pageTitle    = 'Dashboard';
        $merchant     = auth('merchant')->user();
        $transactions = Transaction::where('merchant_id', $merchant->id)->searchable(['trx'])->orderBy('id', 'desc')->take(5)->get();
        $qrCodeUrl    = getQrCodeUrl('merchant');
        return view('Template::merchant.dashboard', compact('pageTitle', 'transactions', 'qrCodeUrl'));
    }

    public function show2faForm()
    {
        $ga        = new GoogleAuthenticator();
        $merchant  = auth('merchant')->user();
        $secret    = $ga->createSecret();
        $qrCodeUrl = $ga->getQRCodeGoogleUrl($merchant->username . '@' . gs('site_name'), $secret);
        $pageTitle = '2FA Security';
        return view('Template::merchant.twofactor', compact('pageTitle', 'secret', 'qrCodeUrl'));
    }

    public function create2fa(Request $request)
    {
        $merchant = auth('merchant')->user();
        $request->validate([
            'key' => 'required',
            'code' => 'required',
        ]);
        $response = verifyG2fa($merchant, $request->code, $request->key);
        if ($response) {
            $merchant->tsc = $request->key;
            $merchant->ts = Status::ENABLE;
            $merchant->save();
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

        $merchant = auth('merchant')->user();
        $response = verifyG2fa($merchant, $request->code);
        if ($response) {
            $merchant->tsc = null;
            $merchant->ts = Status::DISABLE;
            $merchant->save();
            $notify[] = ['success', 'Two factor authenticator deactivated successfully'];
        } else {
            $notify[] = ['error', 'Wrong verification code'];
        }
        return back()->withNotify($notify);
    }



    public function kycForm()
    {
        if (auth('merchant')->user()->kv == Status::KYC_PENDING) {
            $notify[] = ['error', 'Your KYC is under review'];
            return to_route('merchant.home')->withNotify($notify);
        }
        if (auth('merchant')->user()->kv == Status::KYC_VERIFIED) {
            $notify[] = ['error', 'You are already KYC verified'];
            return to_route('merchant.home')->withNotify($notify);
        }
        $pageTitle = 'KYC Form';
        $form = Form::where('act', 'merchant_kyc')->first();
        return view('Template::merchant.kyc.form', compact('pageTitle', 'form'));
    }

    public function kycData()
    {
        $merchant = auth('merchant')->user();
        $pageTitle = 'KYC Data';
        abort_if($merchant->kv == Status::VERIFIED, 403);
        return view('Template::merchant.kyc.info', compact('pageTitle', 'merchant'));
    }

    public function kycSubmit(Request $request)
    {
        $form = Form::where('act', 'merchant_kyc')->firstOrFail();
        $formData = $form->form_data;
        $formProcessor = new FormProcessor();
        $validationRule = $formProcessor->valueValidation($formData);
        $request->validate($validationRule);
        $merchant = auth('merchant')->user();
        foreach (@$merchant->kyc_data ?? [] as $kycData) {
            if ($kycData->type == 'file') {
                fileManager()->removeFile(getFilePath('verify') . '/' . $kycData->value);
            }
        }
        $merchantData = $formProcessor->processFormData($request, $formData);
        $merchant->kyc_data = $merchantData;
        $merchant->kyc_rejection_reason = null;
        $merchant->kv = Status::KYC_PENDING;
        $merchant->save();

        $notify[] = ['success', 'KYC data submitted successfully'];
        return to_route('merchant.home')->withNotify($notify);
    }

    public function userData()
    {
        $merchant = auth('merchant')->user();
        if ($merchant->profile_complete == Status::YES) {
            return to_route('merchant.home');
        }
        $pageTitle  = 'User Data';
        return view('Template::merchant.user_data', compact('pageTitle', 'merchant'));
    }

    public function userDataSubmit(Request $request)
    {
        $merchant = auth('merchant')->user();
        if ($merchant->profile_complete == Status::YES) {
            return to_route('merchant.home');
        }

        $request->validate([
            'firstname' => 'required',
            'lastname'  => 'required',
            'email'     => 'required|string|email|unique:users',
            ...pinValidationRule(true),
            'username'  => 'required|unique:merchants|min:6',
        ]);

        if (preg_match("/[^a-z0-9_]/", trim($request->username))) {
            $notify[] = ['info', 'Username can contain only small letters, numbers and underscore.'];
            $notify[] = ['error', 'No special character, space or capital letters in username.'];
            return back()->withNotify($notify)->withInput($request->all());
        }

        $merchant->firstname        = $request->firstname;
        $merchant->lastname         = $request->lastname;
        $merchant->email            = $request->email;
        $merchant->username         = $request->username;
        $merchant->address          = $request->address;
        $merchant->city             = $request->city;
        $merchant->state            = $request->state;
        $merchant->zip              = $request->zip;
        $merchant->password         = Hash::make($request->pin);
        $merchant->profile_complete = Status::YES;
        $merchant->kv = gs('kv') ? Status::NO : Status::YES;
        $merchant->ev = gs('ev') ? Status::NO : Status::YES;
        $merchant->ts = Status::DISABLE;
        $merchant->tv = Status::ENABLE;
        $merchant->save();

        return to_route('merchant.home');
    }


    public function addDeviceToken(Request $request)
    {

        $validator = Validator::make($request->all(), [
            'token' => 'required',
        ]);

        if ($validator->fails()) {
            return ['success' => false, 'errors' => $validator->errors()->all()];
        }

        $deviceToken = DeviceToken::where('token', $request->token)->where('merchant_id', auth('merchant')->user()->id)->first();

        if ($deviceToken) {
            return ['success' => true, 'message' => 'Already exists'];
        }

        $deviceToken          = new DeviceToken();
        $deviceToken->user_id = auth('merchant')->user()->id;
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
        $remarks      = Transaction::distinct('remark')->orderBy('remark')->where('merchant_id', '!=', 0)->whereNotNull('remark')->get('remark');
        $transactions = Transaction::where('merchant_id', auth('merchant')->id())->searchable(['trx'])->filter(['trx_type', 'remark'])->orderBy('id', 'desc')->paginate(getPaginate());

        return view('Template::merchant.transactions', compact('pageTitle', 'transactions', 'remarks'));
    }


    public function statements(Request $request)
    {
        $pageTitle = 'Statements';
        $merchant = auth('merchant')->user();

        $year  = $request->year ?? now()->year;
        $month = $request->month ?? now()->month;

        $startingBalance = Transaction::where('merchant_id', $merchant->id)
            ->whereYear('created_at', $year)
            ->whereMonth('created_at', '<', $month)
            ->orderBy('created_at', 'desc')
            ->value('post_balance') ?? 0;

        $transactions = Transaction::where('merchant_id', $merchant->id)
            ->whereYear('created_at', $year)
            ->whereMonth('created_at', $month);

        $totalTransactionAmount = (clone $transactions)->sum('amount');
        $totalTransactionCount  = (clone $transactions)->count();
        $totalCharge            = (clone $transactions)->where('remark', 'receive_payment')->sum('charge');
        $transactionHistory     = (clone $transactions)->orderBy('id', 'desc')->searchable(['trx'])->paginate(getPaginate());
        
        return view('Template::merchant.statements', compact('pageTitle', 'startingBalance', 'totalTransactionAmount', 'totalTransactionCount', 'totalCharge', 'year', 'month', 'transactionHistory'));
    }


    public function paymentList()
    {
        $pageTitle = 'All Payments';
        $merchant  = auth('merchant')->user();
        $payments  = MakePayment::where('merchant_id', $merchant->id)->latest('id')->with('user')->searchable(['trx', 'user:mobile'])->paginate(getPaginate());
        return view('Template::merchant.payment_list', compact('pageTitle', 'payments'));
    }

    public function paymentDetails($id)
    {
        $pageTitle = 'Payment Details';
        $payment   = MakePayment::where('id', $id)->with('user')->first();
        if (!$payment) {
            $notify[] = ['error', "The payment is not found"];
            return back()->withNotify($notify);
        }
        return view('Template::merchant.payment_details', compact('pageTitle', 'payment'));
    }


    public function paymentPdf($id)
    {
        $pageTitle = "Payment Receipt";
        $merchant  = auth('merchant')->user();
        $payment   = MakePayment::where('id', $id)->where('merchant_id', $merchant->id)->first();

        if (!$payment) {
            $notify[] = ['error', "The payment is not found"];
            return back()->withNotify($notify);
        }
        $pdf      = Pdf::loadView('Template::merchant.payment_pdf', compact('pageTitle', 'payment', 'merchant'));
        $fileName = "Payment Receipt - " . $payment->trx . ".pdf";
        return $pdf->download($fileName);
    }

    public function notificationSetting()
    {
        $pageTitle = 'Notification Setting';
        $merchant  = auth('merchant')->user();

        return view('Template::merchant.notification_setting', compact('pageTitle', 'merchant'));
    }

    public function notificationSettingsUpdate(Request $request)
    {
        $merchant                              = auth('merchant')->user();
        $merchant->en                          = $request->en ? Status::ENABLE : Status::DISABLE;
        $merchant->sn                          = $request->sn ? Status::ENABLE : Status::DISABLE;
        $merchant->pn                          = $request->pn ? Status::ENABLE : Status::DISABLE;
        $merchant->is_allow_promotional_notify = $request->is_allow_promotional_notify ? Status::YES : Status::NO;
        $merchant->save();

        $notify[] = ['success', 'Notification settings updated successfully'];

        return back()->withNotify($notify);
    }
}
