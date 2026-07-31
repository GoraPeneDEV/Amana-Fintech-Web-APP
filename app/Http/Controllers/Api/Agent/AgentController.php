<?php

namespace App\Http\Controllers\Api\Agent;

use App\Constants\Status;
use App\Http\Controllers\Controller;
use App\Lib\FormProcessor;
use App\Lib\GoogleAuthenticator;
use App\Models\AdminNotification;
use App\Models\DeviceToken;
use App\Models\Form;
use App\Models\NotificationLog;
use App\Models\QrCode;
use App\Models\Transaction;
use App\Models\TransactionCharge;
use App\Rules\FileTypeValidate;
use GuzzleHttp\Client;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Intervention\Image\Drivers\Gd\Driver;
use Intervention\Image\ImageManager;

class AgentController extends Controller
{

    public function home()
    {
        $notify[] = 'Agent Dashboard';
        $agent = auth()->user()->makeVisible('balance');
        return apiResponse("dashboard", "success", $notify, [
            'agent' => $agent,
            'kyc_data' => $agent->kyc_data,
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
            'lastname.required'  => 'The last name field is required'
        ]);

        if ($validator->fails()) {
            return apiResponse("validation_error", "error", $validator->errors()->all());
        }

        $agent = auth()->user();

        $agent->firstname = $request->firstname;
        $agent->lastname  = $request->lastname;
        $agent->address   = $request->address;
        $agent->city      = $request->city;
        $agent->state     = $request->state;
        $agent->zip       = $request->zip;

        if ($request->hasFile('image')) {
            try {
                $old         = $agent->image;
                $agent->image = fileUploader($request->image, getFilePath('agentProfile'), getFileSize('agentProfile'), $old);
            } catch (\Exception $exp) {
                $notify[] = "Couldn't upload your image";
                return apiResponse("validation_error", "error", $notify);
            }
        }

        $agent->save();

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

        $agent = auth()->user();
        if (Hash::check($request->current_pin, $agent->password)) {
            $password       = Hash::make($request->pin);
            $agent->password = $password;
            $agent->save();
            $notify[] = 'Pin changed successfully';
            return apiResponse("password_changed", "success", $notify);
        } else {
            $notify[] = "The pin doesn't match!";
            return apiResponse("not_match", "validation_error", $notify);
        }
    }

    public function transactions(Request $request)
    {
        $remarks      = Transaction::where('agent_id', '!=', 0)->distinct('remark')->get('remark');
        $transactions = Transaction::where('agent_id', auth()->user()->id);

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

    public function statements(Request $request)
    {

        $validator = Validator::make($request->all(), [
            'month' => 'required|integer|min:1|max:12',
            'year'  => 'required|integer|digits:4',
        ]);

        if ($validator->fails()) {
            return apiResponse("validation_error", "error", $validator->errors()->all());
        }

        $startingBalance = Transaction::where('agent_id', auth()->id())
            ->whereYear('created_at', $request->year)
            ->whereMonth('created_at', '<', $request->month)
            ->orderBy('created_at', 'desc')
            ->value('post_balance') ?? 0;

        $transactions = Transaction::where('agent_id', auth()->id())
            ->whereYear('created_at', $request->year)
            ->whereMonth('created_at', $request->month);

        $totalTransactionAmount = $transactions->sum('amount');
        $totalTransactionCount  = $transactions->count();
        $totalCommission        = $transactions->whereIn('remark', ['cash_out_commission', 'cash_in_commission'])->sum('amount');

        $notify[] = 'Statements retrieved successfully';
        
        return apiResponse("statements", "success", $notify, [
            'starting_balance'         => $startingBalance,
            'total_transaction_amount' => $totalTransactionAmount,
            'total_transaction_count'  => $totalTransactionCount,
            'total_commission'         => $totalCommission,
            'current_balance'          => auth()->user()->balance,
        ]);
    }

    public function addMoneyHistory(Request $request)
    {
        $addMoney = auth()->user()->deposits();

        if ($request->search) {
            $addMoney = $addMoney->where('trx', $request->search);
        }
        $addMoney = $addMoney->with(['gateway'])->orderBy('id', 'desc')->paginate(getPaginate());
        $notify[] = 'Add Money data';

        return apiResponse("add_money_history", "success", $notify, [
            'histories' => $addMoney
        ]);
    }


    public function kycForm()
    {
        if (auth()->user()->kv == Status::KYC_PENDING) {
            $notify[] = 'Your KYC is under review';
            return apiResponse("under_review", "error", $notify, [
                'kyc_data' => auth()->user()->kyc_data,
                'path'     => getFilePath('verify')
            ]);
        }
        if (auth()->user()->kv == Status::KYC_VERIFIED) {
            $notify[] = 'You are already KYC verified';
            return apiResponse("already_verified", "error", $notify);
        }

        $form     = Form::where('act', 'agent_kyc')->first();
        $notify[] = 'KYC field is below';

        return apiResponse("kyc_form", "success", $notify, [
            'form' => @$form->form_data ?? []
        ]);
    }

    public function kycSubmit(Request $request)
    {
        $form = Form::where('act', 'agent_kyc')->first();
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
        $agent = auth()->user();
        foreach (@$agent->kyc_data ?? [] as $kycData) {
            if ($kycData->type == 'file') {
                fileManager()->removeFile(getFilePath('verify') . '/' . $kycData->value);
            }
        }
        $agentData = $formProcessor->processFormData($request, $formData);

        $agent->kyc_data             = $agentData;
        $agent->kyc_rejection_reason = null;
        $agent->kv                   = Status::KYC_PENDING;
        $agent->save();

        $notify[] = 'KYC data submitted successfully';
        return apiResponse("kyc_submitted", "success", $notify);
    }

    public function userDataSubmit(Request $request)
    {
        $agent = auth()->user();

        if ($agent->profile_complete == Status::YES) {
            $notify[] = "You've already completed your profile";
            return apiResponse("already_completed", "error", $notify);
        }

        $validator = Validator::make($request->all(), [
            'username'  => 'required|unique:agents|min:6',
            'email'     => 'required|email|unique:agents',
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

        $agent->email     = $request->email;
        $agent->username  = $request->username;
        $agent->firstname = $request->firstname;
        $agent->lastname  = $request->lastname;
        $agent->password  = bcrypt($request->pin);

        $agent->address = $request->address;
        $agent->city    = $request->city;
        $agent->state   = $request->state;
        $agent->zip     = $request->zip;

        $agent->kv = gs('kv') ? Status::NO : Status::YES;
        $agent->ev = gs('ev') ? Status::NO : Status::YES;
        $agent->ts = Status::DISABLE;
        $agent->tv = Status::ENABLE;

        $agent->profile_complete = Status::YES;
        $agent->save();

        $notify[] = 'Profile completed successfully';
        return apiResponse("profile_completed", "success", $notify, [
            'agent' => $agent
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
        
        

        $deviceToken = DeviceToken::where('token', $request->token)->where('agent_id', auth()->user()->id)->first();

        if ($deviceToken) {
            $notify[] = 'Token Already exists';
            return apiResponse("token_exists", "error", $notify);
        }

        $deviceToken            = new DeviceToken();
        $deviceToken->agent_id  = auth()->user()->id;
        $deviceToken->token     = $request->token;
        $deviceToken->is_app    = Status::YES;
        $deviceToken->save();

        $notify[] = 'Token save successfully';
        return apiResponse("token_saved", "success", $notify);
    }

    public function show2faData()
    {
        $ga        = new GoogleAuthenticator();
        $agent      = auth()->user();
        $secret    = $ga->createSecret();
        $qrCodeUrl = $ga->getQRCodeGoogleUrl($agent->username . '@' . gs('site_name'), $secret);

        $notify[] = '2FA Qr';

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

        $agent     = auth()->user();

        $response = verifyG2fa($agent, $request->code, $request->secret);
        if ($response) {
            $agent->tsc = $request->secret;
            $agent->ts  = Status::ENABLE;
            $agent->save();

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

        $agent     = auth()->user();
        $response = verifyG2fa($agent, $request->code);
        if ($response) {
            $agent->tsc = null;
            $agent->ts  = Status::DISABLE;
            $agent->save();

            $notify[] = 'Two factor authenticator deactivated successfully';

            return apiResponse("2fa_qr", "success", $notify);
        } else {
            $notify[] = 'Wrong verification code';

            return apiResponse("wrong_verification", "error", $notify);
        }
    }

    public function pushNotifications()
    {
        $notifications = NotificationLog::where('agent_id', auth()->id())->where('sender', 'firebase')->orderBy('id', 'desc')->paginate(getPaginate());
        $notify[]      = 'Push notifications';
        return apiResponse("notifications", "success", $notify, [
            'notifications' => $notifications,
        ]);
    }


    public function pushNotificationsRead($id)
    {
        $notification = NotificationLog::where('agent_id', auth()->user()->id())->where('sender', 'firebase')->find($id);
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
        $notify[] = 'Agent information';
        return apiResponse("agent_info", "success", $notify, [
            'agent'   => auth()->user()->makeVisible('balance')
        ]);
    }

    public function notificationSettings()
    {
        $notify[] = 'Notification settings';
        return apiResponse("notification_settings", "success", $notify, [
            'agent' => auth()->user(),
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

        $agent                              = auth()->user();
        $agent->en                          = $request->en ? Status::ENABLE : Status::DISABLE;
        $agent->sn                          = $request->sn ? Status::ENABLE : Status::DISABLE;
        $agent->pn                          = $request->pn ? Status::ENABLE : Status::DISABLE;
        $agent->is_allow_promotional_notify = $request->is_allow_promotional_notify ? Status::YES : Status::NO;
        $agent->save();

        $notify[] = 'Notification settings updated successfully';
        return apiResponse("notification_settings_update", "success", $notify, [
            'agent' => $agent
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

        $agent = auth()->user();

        if (!Hash::check($request->pin, $agent->password)) {
            $notify[] = 'The provided pin is invalid';
            return apiResponse("invalid_password", "error", $notify);
        }

        $agent              = auth()->user();
        $agent->status      = Status::USER_DELETE;
        $agent->username    = 'deleted_' . $agent->username;
        $agent->mobile      = 'deleted_' . $agent->mobile;
        $agent->email       = 'deleted_' . $agent->email;
        $agent->provider_id = 'deleted_' . $agent->provider_id;
        $agent->save();

        $agent->tokens()->delete();

        $adminNotification            = new AdminNotification();
        $adminNotification->agent_id  = $agent->id;
        $adminNotification->title     = $agent->username . ' agent deleted his account.';
        $adminNotification->click_url = urlPath('admin.agents.detail', $agent->id);
        $adminNotification->save();

        $notify[] = 'Account deleted successfully';
        return apiResponse("account_deleted", "success", $notify);
    }

    public function commissionLog()
    {
        $notify[] = "Commission Logs";
        $logs = Transaction::where('agent_id', auth()->user()->id)
            ->whereIn('remark', ['cash_in_commission', 'cash_out_commission'])
            ->apiQuery();
        return apiResponse("commission_log", "success", $notify, [
            'logs' => $logs
        ]);
    }

    public function qrCode()
    {
        $notify[] = 'QR Code';
        $agent     = auth()->user();
        $qrCode   = $agent->qrCode;

        if (!$qrCode) {
            $qrCode              = new QrCode();
            $qrCode->agent_id    = $agent->id;
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
        $agent    = auth()->user();
        $qrCode  = $agent->qrCode()->first();
        $general = gs();

        $file     = cryptoQR($qrCode->unique_code);
        $filename = $qrCode->unique_code . '.jpg';

        $manager  = new ImageManager(new Driver());
        $template = $manager->read('assets/images/qr_code_template/' . $general->qr_code_template);

        $client       = new Client();
        $response     = $client->get($file);
        $imageContent = $response->getBody()->getContents();

        $qrCode = $manager->read($imageContent)->cover(2000, 2000);
        $template->place($qrCode, 'center');
        $image = $template->encode();

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

    public function qrCodeScan(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'code' => 'required',
        ]);

        if ($validator->fails()) {
            return apiResponse("validation_error", "error", $validator->errors()->all());
        }

        $data = QrCode::where('unique_code', $request->code)->first();
        
        if (!$data) {
            $notify[] = "QR code doesn't match";
            return apiResponse("validation_error", "error", $notify);
        }

        $notify[] = "QR code scan";
        return apiResponse("qr_code_scan", "success", $notify, [
            'user_type'          => $data->getUserType(),
            'user_data'          => $data->getUser,
            'transaction_charge' => TransactionCharge::get(),
        ]);
    }

    public function validatePin(Request $request)
    {
        $validator = Validator::make($request->all(), [
            ...pinValidationRule()
        ]);

        if ($validator->fails()) {
            return apiResponse("validation_error", "error", $validator->errors()->all());
        }

        $agent = auth()->user();

        if (!Hash::check($request->pin, $agent->password)) {
            $notify[] = 'Provided PIN does not correct';
            return apiResponse("validation_error", "error", $notify);
        }

        $notify[] = 'Provided PIN is correct';
        return apiResponse("valid_pin", "success", $notify);
    }

    public function remarkWiseTransaction($remark, $transaction)
    {
        if ($remark === "add_money") {
            $deposit = $transaction->agentDeposit;
            return [
                'title'     => $deposit->gateway->name,
                'image_src' => getImage(getFilePath('gateway') . '/' . @$deposit->gateway->image),
            ];
        }

        if ($remark === "cash_out") {
            $agent = $transaction->cashOut->user;
            return [
                'title'     => @$agent->fullname,
                'subtitle'  => @$agent->mobileNumber,
                'image_src' => @$agent->image_src,
                'note'    => null
            ];
        }
        if ($remark === "cash_out") {
            $agent = $transaction->cashOut->user;
            return [
                'title'     => @$agent->fullname,
                'subtitle'  => @$agent->mobileNumber,
                'image_src' => @$agent->image_src,
                'note'    => null
            ];
        }
        if ($remark === "cash_out_commission") {
            $agent = $transaction->cashOutCommission->user;
            return [
                'title'     => @$agent->fullname,
                'subtitle'  => @$agent->mobileNumber,
                'image_src' => @$agent->image_src,
                'note'      => null
            ];
        }
        if ($remark === "cash_in") {
            $agent = $transaction->cashIn->user;
            return [
                'title'     => @$agent->fullname,
                'subtitle'  => @$agent->mobileNumber,
                'image_src' => @$agent->image_src,
                'note'      => null
            ];
        }

        if ($remark === "cash_in_commission") {
            $agent = $transaction->cashInCommission->user;
            return [
                'title'     => @$agent->fullname,
                'subtitle'  => @$agent->mobileNumber,
                'image_src' => @$agent->image_src,
                'note'      => null
            ];
        }

        if ($remark === "withdraw") {
            $method = $transaction->agentWithdrawal->method;
            return [
                'title'     => @$method->name,
                'image_src' => getImage(getFilePath('withdrawMethod') . '/' . $method->image),
            ];
        }

        if ($remark === "withdraw_reject") {
            $method = $transaction->agentWithdrawal->method;
            return [
                'title'     => @$method->name,
                'image_src' => getImage(getFilePath('withdrawMethod') . '/' . $method->image),
                'feedback'  => @$transaction->agentWithdrawal->admin_feedback
            ];
        }

        return (object)[];
    }
}
