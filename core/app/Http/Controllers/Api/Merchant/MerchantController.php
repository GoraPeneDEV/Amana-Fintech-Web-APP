<?php

namespace App\Http\Controllers\Api\Merchant;

use App\Constants\Status;
use App\Http\Controllers\Controller;
use App\Lib\FormProcessor;
use App\Lib\GoogleAuthenticator;
use App\Models\AdminNotification;
use App\Models\DeviceToken;
use App\Models\Form;
use App\Models\MakePayment;
use App\Models\NotificationLog;
use App\Models\QrCode;
use App\Models\Transaction;
use App\Rules\FileTypeValidate;
use Barryvdh\DomPDF\Facade\Pdf;
use GuzzleHttp\Client;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Intervention\Image\Drivers\Gd\Driver;
use Intervention\Image\ImageManager;

class MerchantController extends Controller
{

    public function dashboard()
    {
        $merchant = auth()->user()->makeVisible('balance');
        $notify[] = 'Merchant Dashboard';
        return apiResponse("dashboard", "success", $notify, [
            'merchant' => $merchant,
            'kyc_data' => $merchant->kyc_data,
        ]);
    }

    public function submitProfile(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'firstname' => 'required',
            'lastname'  => 'required',
            'image'     => ['image', new FileTypeValidate(['jpg', 'jpeg', 'png'])],
        ], [
            'firstname.required' => 'The first name field is required',
            'lastname.required' => 'The last name field is required'
        ]);

        if ($validator->fails()) {
            return apiResponse("validation_error", "error", $validator->errors()->all());
        }

        $merchant = auth()->user('merchant');

        $merchant->firstname = $request->firstname;
        $merchant->lastname  = $request->lastname;
        $merchant->address   = $request->address;
        $merchant->city      = $request->city;
        $merchant->state     = $request->state;
        $merchant->zip       = $request->zip;

        if ($request->hasFile('image')) {
            try {
                $old         = $merchant->image;
                $merchant->image = fileUploader($request->image, getFilePath('merchantProfile'), getFileSize('merchantProfile'), $old);
            } catch (\Exception $exp) {
                $notify[] = "Couldn't upload your image";
                return apiResponse("validation_error", "error", $notify);
            }
        }

        $merchant->save();

        $notify[] = 'Profile updated successfully';
        return apiResponse("profile_updated", "success", $notify);
    }

    public function submitPassword(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'current_pin' => 'required',
            ...pinValidationRule(true)
        ]);

        if ($validator->fails()) {
            return apiResponse("validation_error", "error", $validator->errors()->all());
        }

        $merchant = auth()->user();
        if (Hash::check($request->current_pin, $merchant->password)) {
            $password       = Hash::make($request->pin);
            $merchant->password = $password;
            $merchant->save();
            $notify[] = 'PIN changed successfully';
            return apiResponse("password_changed", "success", $notify);
        } else {
            $notify[] = "The PIN doesn't match!";
            return apiResponse("not_match", "validation_error", $notify);
        }
    }

    public function transactions(Request $request)
    {
        $remarks      = Transaction::where('merchant_id', '!=', 0)->distinct('remark')->get('remark');
        $transactions = Transaction::where('merchant_id', auth()->user()->id);

        if ($request->search) {
            $transactions = $transactions->where('trx', $request->search);
        }

        if ($request->type) {
            $type         = $request->type == 'plus' ? '+' : '-';
            $transactions = $transactions->where('trx_type', $type);
        }

        if ($request->remark) {
            $transactions = $transactions->where('remark', $request->remark);
        }

        $transactions = $transactions->orderBy('id', 'desc')->paginate(getPaginate());

        $transactions->getCollection()->transform(function ($transaction) {

            $otherData = $this->remarkWiseTransaction($transaction->remark, $transaction);
            return [
                'id'              => $transaction->id,
                'trx'             => $transaction->trx,
                'trx_type'        => $transaction->trx_type,
                'amount'          => $transaction->amount,
                'charge'          => $transaction->charge,
                'total_amount'    => (string) $transaction->total_amount,
                'remark'          => $transaction->remark,
                'other_data'      => $otherData,
                'details'         => $transaction->details,
                'created_at'      => $transaction->created_at,
                'created_at_diff' => diffForHumans($transaction->created_at),
            ];
        });


        $notify[]     = 'Transactions data';

        return apiResponse("transactions", "success", $notify, [
            'transactions' => $transactions,
            'remarks'      => $remarks,
        ]);
    }


    public function kycForm()
    {
        if (auth()->user('merchant')->kv == Status::KYC_PENDING) {
            $notify[] = 'Your KYC is under review';
            return apiResponse("under_review", "error", $notify, [
                'kyc_data' => auth()->user('merchant')->kyc_data,
                'path' => getFilePath('verify')
            ]);
        }
        
        if (auth()->user('merchant')->kv == Status::KYC_VERIFIED) {
            $notify[] = 'You are already KYC verified';
            return apiResponse("already_verified", "error", $notify);
        }

        $form     = Form::where('act', 'merchant_kyc')->first();
        $notify[] = 'KYC field is below';
        return apiResponse("kyc_form", "success", $notify, [
            'form' => @$form->form_data ?? []
        ]);
    }

    public function kycSubmit(Request $request)
    {
        $form = Form::where('act', 'merchant_kyc')->first();
        if (!$form) {
            $notify[] = 'Invalid KYC request';
            return apiResponse("invalid_request", "error", $notify);
        }

        $formData       = $form->form_data;
        $formProcessor  = new FormProcessor();
        $validationRule = $formProcessor->valueValidation($formData);

        $validator = Validator::make($request->all(), $validationRule);

        if ($validator->fails()) {
            return apiResponse("validation_error", "error", $validator->errors()->all());
        }
        $merchant = auth()->user('merchant');
        foreach (@$merchant->kyc_data ?? [] as $kycData) {
            if ($kycData->type == 'file') {
                fileManager()->removeFile(getFilePath('verify') . '/' . $kycData->value);
            }
        }
        $merchantData = $formProcessor->processFormData($request, $formData);

        $merchant->kyc_data             = $merchantData;
        $merchant->kyc_rejection_reason = null;
        $merchant->kv                   = Status::KYC_PENDING;
        $merchant->save();

        $notify[] = 'KYC data submitted successfully';
        return apiResponse("kyc_submitted", "success", $notify);
    }

    public function userDataSubmit(Request $request)
    {
        $merchant = auth()->user();

        if ($merchant->profile_complete == Status::YES) {
            $notify[] = "You've already completed your profile";
            return apiResponse("already_completed", "error", $notify);
        }

        $validator = Validator::make($request->all(), [
            'username'  => 'required|unique:merchants|min:6',
            'email'     => 'required|email|unique:merchants',
            'firstname' => 'required',
            'lastname'  => 'required',
            ...pinValidationRule(true)
        ]);

        if ($validator->fails()) {
            return apiResponse("validation_error", "error", $validator->errors()->all());
        }

        if (preg_match("/[^a-z0-9_]/", trim($request->username))) {
            $notify[] = 'No special character, space or capital letters in username';
            return apiResponse("validation_error", "error", $notify);
        }

        $merchant->email     = $request->email;
        $merchant->username  = $request->username;
        $merchant->firstname = $request->firstname;
        $merchant->lastname  = $request->lastname;
        $merchant->password  = bcrypt($request->pin);
        $merchant->address   = $request->address;
        $merchant->city      = $request->city;
        $merchant->state     = $request->state;
        $merchant->zip       = $request->zip;
        $merchant->kv        = gs('kv') ? Status::NO : Status::YES;
        $merchant->ev        = gs('ev') ? Status::NO : Status::YES;
        $merchant->ts        = Status::DISABLE;
        $merchant->tv        = Status::ENABLE;

        $merchant->profile_complete = Status::YES;
        $merchant->save();

        $notify[] = 'Profile completed successfully';
        return apiResponse("profile_completed", "success", $notify, [
            'merchant' => $merchant
        ]);
    }

    public function addDeviceToken(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'token' => 'required',
        ]);

        if ($validator->fails()) {
            return apiResponse("validation_error", "error", $validator->errors()->all());
        }

        $deviceToken = DeviceToken::where('token', $request->token)->where('merchant_id', auth()->user()->id)->first();

        if ($deviceToken) {
            $notify[] = 'Token Already exists';
            return apiResponse("token_exists", "error", $notify);
        }

        $deviceToken               = new DeviceToken();
        $deviceToken->merchant_id  = auth()->user()->id;
        $deviceToken->token        = $request->token;
        $deviceToken->is_app       = Status::YES;
        $deviceToken->save();

        $notify[] = 'Token save successfully';
        return apiResponse("token_saved", "success", $notify);
    }

    public function show2faData()
    {
        $ga        = new GoogleAuthenticator();
        $merchant      = auth()->user('merchant');
        $secret    = $ga->createSecret();
        $qrCodeUrl = $ga->getQRCodeGoogleUrl($merchant->username . '@' . gs('site_name'), $secret);

        $notify[]  = '2FA Qr';

        return apiResponse("2fa_qr", "success", $notify, [
            'secret'      => $secret,
            'qr_code_url' => $qrCodeUrl,
        ]);
    }

    public function create2fa(Request $request)
    {

        $validator = Validator::make($request->all(), [
            'secret'  => 'required',
            'code'    => 'required',
        ]);

        if ($validator->fails()) {
            return apiResponse("validation_error", "error", $validator->errors()->all());
        }

        $merchant     = auth()->user('merchant');

        $response = verifyG2fa($merchant, $request->code, $request->secret);
        if ($response) {
            $merchant->tsc = $request->secret;
            $merchant->ts  = Status::ENABLE;
            $merchant->save();

            $notify[] = 'Google authenticator activated successfully';

            return apiResponse("2fa_qr", "success", $notify);
        } else {
            $notify[] = 'Wrong verification code';

            return apiResponse("wrong_verification", "error", $notify);
        }
    }

    public function disable2fa(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'code' => 'required',
        ]);

        if ($validator->fails()) {
            return apiResponse("validation_error", "error", $validator->errors()->all());
        }

        $merchant     = auth()->user('merchant');
        $response = verifyG2fa($merchant, $request->code);
        if ($response) {
            $merchant->tsc = null;
            $merchant->ts  = Status::DISABLE;
            $merchant->save();

            $notify[] = 'Two factor authenticator deactivated successfully';

            return apiResponse("2fa_qr", "success", $notify);
        } else {
            $notify[] = 'Wrong verification code';

            return apiResponse("wrong_verification", "error", $notify);
        }
    }

    public function pushNotifications()
    {
        $notifications = NotificationLog::where('merchant_id', auth()->user()->id)->where('sender', 'firebase')->orderBy('id', 'desc')->paginate(getPaginate());
        $notify[]      = 'Push notifications';
        return apiResponse("notifications", "success", $notify, [
            'notifications' => $notifications,
        ]);
    }


    public function pushNotificationsRead($id)
    {
        $notification = NotificationLog::where('merchant_id', auth()->user()->id)->where('sender', 'firebase')->find($id);
        if (!$notification) {
            $notify[] = 'Notification not found';
            return apiResponse("notification_not_found", "error", $notify);
        }
        $notify[]                = 'Notification marked as read successfully';
        $notification->user_read = 1;
        $notification->save();

        return apiResponse("notification_read", "success", $notify);
    }


    public function userInfo()
    {
        $notify[] = 'Merchant information';
        return apiResponse("merchant_info", "success", $notify, [
            'merchant' => auth()->user()->makeVisible('balance')
        ]);
    }

    public function notificationSettings()
    {
        $notify[] = 'Notification settings';
        return apiResponse("notification_settings", "success", $notify, [
            'merchant' => auth()->user(),
        ]);
    }

    public function notificationSettingsUpdate(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'en'                          => 'required',
            'sn'                          => 'required',
            'pn'                          => 'required',
            'is_allow_promotional_notify' => 'required',
        ]);

        if ($validator->fails()) {
            return apiResponse("validation_error", "error", $validator->errors()->all());
        }

        $merchant                              = auth()->user();
        $merchant->en                          = $request->en ? Status::ENABLE : Status::DISABLE;
        $merchant->sn                          = $request->sn ? Status::ENABLE : Status::DISABLE;
        $merchant->pn                          = $request->pn ? Status::ENABLE : Status::DISABLE;
        $merchant->is_allow_promotional_notify = $request->is_allow_promotional_notify ? Status::YES : Status::NO;
        $merchant->save();

        $notify[] = 'Notification settings updated successfully';
        return apiResponse("notification_settings_update", "success", $notify, [
            'merchant' => $merchant
        ]);
    }

    public function accountDelete(Request $request)
    {
        $validator = Validator::make($request->all(), [
            ...pinValidationRule()
        ]);

        if ($validator->fails()) {
            return apiResponse("validation_error", "error", $validator->errors()->all());
        }

        $merchant = auth()->user();

        if (!Hash::check($request->pin, $merchant->password)) {
            $notify[] = 'The provided pin is invalid';
            return apiResponse("invalid_password", "error", $notify);
        }

        $merchant              = auth()->user();
        $merchant->status      = Status::USER_DELETE;
        $merchant->mobile      = 'deleted_' . $merchant->mobile;
        $merchant->username    = 'deleted_' . $merchant->username;
        $merchant->email       = 'deleted_' . $merchant->email;
        $merchant->provider_id = 'deleted_' . $merchant->provider_id;
        $merchant->save();

        $merchant->tokens()->delete();

        $adminNotification              = new AdminNotification();
        $adminNotification->merchant_id = $merchant->id;
        $adminNotification->title       = $merchant->username . ' merchant deleted his account.';
        $adminNotification->click_url   = urlPath('admin.merchants.detail', $merchant->id);
        $adminNotification->save();

        $notify[] = 'Account deleted successfully';
        return apiResponse("account_deleted", "success", $notify);
    }


    public function apiKey()
    {
        $merchant = auth()->user();

        if (!$merchant->public_api_key || !$merchant->secret_api_key) {
            $merchant->public_api_key = keyGenerator();
            $merchant->secret_api_key = keyGenerator();
            $merchant->save();
        }

        $notify[] = 'Business Api Key';

        return apiResponse("business_api_key", "success", $notify, [
            'public_api_key' => $merchant->public_api_key,
            'secret_api_key' => $merchant->secret_api_key,
        ]);
    }

    public function generateApiKey()
    {
        $merchant  = auth()->user();
        $publicKey = keyGenerator();
        $secretKey = keyGenerator();

        $merchant->public_api_key = $publicKey;
        $merchant->secret_api_key = $secretKey;
        $merchant->save();

        $notify[] = 'New API key generated successfully';

        return apiResponse("key_generated", "success", $notify, [
            'public_api_key' => $merchant->public_api_key,
            'secret_api_key' => $merchant->secret_api_key,
        ]);
    }

    public function qrCode()
    {
        $notify[] = 'QR Code';
        $merchant     = auth()->user();
        $qrCode   = $merchant->qrCode;

        if (!$qrCode) {
            $qrCode              = new QrCode();
            $qrCode->merchant_id = $merchant->id;
            $qrCode->unique_code = keyGenerator(15);
            $qrCode->save();
        }
        $uniqueCode = $qrCode->unique_code;
        $qrCode     = cryptoQR($uniqueCode);

        return apiResponse("qr_code", "success", $notify, [
            'qr_code' => $qrCode
        ]);
    }

    public function qrCodeDownload()
    {
        $merchant     = auth()->user();
        $qrCode   = $merchant->qrCode()->first();
        $general  = gs();

        $file     = cryptoQR($qrCode->unique_code);
        $filename = $qrCode->unique_code . '.jpg';

        $manager  = new ImageManager(new Driver());
        $template = $manager->read('assets/images/qr_code_template/' . $general->qr_code_template);

        $client       = new Client();
        $response     = $client->get($file);
        $imageContent = $response->getBody()->getContents();

        $qrCode = $manager->read($imageContent)->cover(2000, 2000);
        $template->place($qrCode, 'center');
        $image  = $template->encode();

        $headers = [
            'Content-Type'        => 'image/jpeg',
            'Content-Disposition' => 'attachment; filename=' . $filename,
        ];

        return response()->stream(function () use ($image) {
            echo $image;
        }, 200, $headers);
    }

    public function qrCodeRemove(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'file_name' => 'required',
        ]);

        if ($validator->fails()) {
            return apiResponse("validation_error", "error", $validator->errors()->all());
        }

        $file = getFilePath('temporary') . '/' . $request->file_name;

        if (file_exists($file)) {
            unlink($file);

            $notify[] = "QR code removed successfully";
            return apiResponse("qr_code_remove", "success", $notify);
        }

        $notify[] = "Already removed";
        return apiResponse("qr_code_remove", "success", $notify);
    }

    public function validatePin(Request $request)
    {
        $validator = Validator::make($request->all(), [
            ...pinValidationRule()
        ]);

        if ($validator->fails()) {
            return apiResponse("validation_error", "error", $validator->errors()->all());
        }

        $merchant = auth()->user();

        if (!Hash::check($request->pin, $merchant->password)) {
            $notify[] = 'Provided PIN does not correct';
            return apiResponse("validation_error", "error", $notify);
        }

        $notify[] = 'Provided PIN is correct';
        return apiResponse("valid_pin", "success", $notify);
    }

    public function statements(Request $request)
    {
        $request->validate([
            'month' => 'required|integer|min:1|max:12',
            'year'  => 'required|integer|digits:4',
        ]);

        $startingBalance = Transaction::where('merchant_id', auth()->id())
            ->whereYear('created_at', $request->year)
            ->whereMonth('created_at', '<', $request->month)
            ->orderBy('created_at', 'desc')
            ->value('post_balance') ?? 0;

        $transactions = Transaction::where('merchant_id', auth()->id())
            ->whereYear('created_at', $request->year)
            ->whereMonth('created_at', $request->month);

        $totalTransactionAmount = $transactions->sum('amount');
        $totalTransactionCount  = $transactions->count();
        $totalCharge            = $transactions->where('remark', 'receive_payment')->sum('charge');

        $notify[] = 'Statements retrieved successfully';

        return apiResponse("statements", "success", $notify, [
            'starting_balance'         => $startingBalance,
            'total_transaction_amount' => $totalTransactionAmount,
            'total_transaction_count'  => $totalTransactionCount,
            'total_charge'             => $totalCharge,
            'current_balance'          => auth()->user()->balance,
        ]);
    }

    public function remarkWiseTransaction($remark, $transaction)
    {
        if ($remark === "receive_payment") {
            $payment = $transaction->payment;
            return [
                'title'     => $payment->user->fullname,
                'subtitle'  => $payment->user->mobileNumber,
                'image_src' => $payment->user->image_src,
            ];
        }

        if ($remark === "withdraw") {
            $method = $transaction->merchantWithdrawal->method;
            return [
                'title'     => @$method->name,
                'image_src' => getImage(getFilePath('withdrawMethod') . '/' . $method->image),
            ];
        }

        if ($remark === "withdraw_reject") {
            $method = $transaction->merchantWithdrawal->method;
            return [
                'title'     => @$method->name,
                'image_src' => getImage(getFilePath('withdrawMethod') . '/' . $method->image),
                'feedback'  => @$transaction->merchantWithdrawal->admin_feedback
            ];
        }

        return (object)[];
    }

    public function paymentList()
    {
        $merchant  = auth()->user();
        $payments  = MakePayment::where('merchant_id', $merchant->id)->latest('id')->with('user')->paginate(getPaginate());
        $notify[] = 'Payment list';
        return apiResponse("payment_list", "success", $notify, [
            'payments' => $payments
        ]);
    }

    public function paymentDetails($id)
    {
        $merchant  = auth()->user();
        $payment   = MakePayment::where('id', $id)->with('user')->where('merchant_id', $merchant->id)->first();
        if (!$payment) {
            $notify[] = 'The payment is not found';
            return apiResponse("payment_details", "error", $notify);
        }

        $notify[] = 'Payment details';
        return apiResponse("payment_details", "success", $notify, [
            'payment' => $payment
        ]);
    }


    public function paymentPdf($id)
    {
        $pageTitle = "Payment Receipt";
        $merchant  = auth()->user();
        $payment   = MakePayment::where('id', $id)->where('merchant_id', $merchant->id)->first();
        if (!$payment) {
            $notify[] = 'The payment is not found';
            return apiResponse("payment_pdf", "error", $notify);
        }
        $activeTemplateTrue = activeTemplate(true);
        $activeTemplate     = activeTemplate();

        $pdf      = Pdf::loadView($activeTemplate . '.merchant.payment_pdf', compact('pageTitle', 'payment', 'merchant', 'activeTemplateTrue', 'activeTemplate'));
        $fileName = "Payment Receipt - " . $payment->trx . ".pdf";
        return $pdf->download($fileName);
    }
}
