<?php

namespace App\Http\Controllers\Api;

use App\Constants\Status;
use App\Http\Controllers\Controller;
use App\Lib\FormProcessor;
use App\Lib\GoogleAuthenticator;
use App\Models\Banner;
use App\Models\DeviceToken;
use App\Models\Form;
use App\Models\NotificationLog;
use App\Models\Offer;
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

class UserController extends Controller
{

    public function dashboard()
    {
        $notify[] = 'User Dashboard';
        $user     = auth()->user()->makeVisible('balance');
        $banners  = Banner::active()->limit(6)->get();
        $offers   = Offer::active()->with('merchant')->limit(6)->get();
        return apiResponse("dashboard", "success", $notify, [
            'user'     => $user,
            'kyc_data' => $user->kyc_data,
            'banners'  => $banners,
            'offers'   => $offers,
        ]);
    }

    public function offers()
    {
        $notify[] = 'All offers';
        $offers   = Offer::active()->with('merchant')->apiQuery();
        return apiResponse("offers", "success", $notify, [
            'offers' => $offers,
        ]);
    }

    public function userDataSubmit(Request $request)
    {
        $user = auth()->user();

        if ($user->profile_complete == Status::YES) {
            $notify[] = "You've already completed your profile";
            return apiResponse("already_completed", "error", $notify);
        }

        $validator = Validator::make($request->all(), [
            'username'  => 'required|unique:users|min:6',
            'email'     => 'required|email|unique:users',
            'firstname' => 'required',
            'lastname'  => 'required',
            ...pinValidationRule(),
        ]);

        if ($validator->fails()) {
            return apiResponse("validation_error", "error", $validator->errors()->all());
        }

        if (preg_match("/[^a-z0-9_]/", trim($request->username))) {
            $notify[] = 'No special character, space or capital letters in username';
            return apiResponse("validation_error", "error", $notify);
        }

        $user->email     = $request->email;
        $user->username  = $request->username;
        $user->firstname = $request->firstname;
        $user->lastname  = $request->lastname;
        $user->password  = bcrypt($request->pin);

        $user->address          = $request->address;
        $user->city             = $request->city;
        $user->state            = $request->state;
        $user->zip              = $request->zip;
        $user->profile_complete = Status::YES;
        $user->kv               = gs('kv') ? Status::NO : Status::YES;
        $user->ev               = gs('ev') ? Status::NO : Status::YES;
        $user->ts               = Status::DISABLE;
        $user->tv               = Status::ENABLE;
        $user->save();

        $notify[] = 'Profile completed successfully';
        return apiResponse("profile_completed", "success", $notify, [
            'user' => $user,
        ]);
    }

    public function kycForm()
    {
        if (auth()->user()->kv == Status::KYC_PENDING) {
            $notify[] = 'Your KYC is under review';
            return apiResponse("under_review", "error", $notify, [
                'kyc_data' => auth()->user()->kyc_data,
                'path'     => getFilePath('verify'),
            ]);
        }

        if (auth()->user()->kv == Status::KYC_VERIFIED) {
            $notify[] = 'You are already KYC verified';
            return apiResponse("already_verified", "error", $notify);
        }

        $form     = Form::where('act', 'user_kyc')->first();
        $notify[] = 'KYC field is below';

        return apiResponse("kyc_form", "success", $notify, [
            'form' => @$form->form_data ?? [],
        ]);
    }

    public function kycSubmit(Request $request)
    {
        $form = Form::where('act', 'user_kyc')->first();
        if (! $form) {
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
        $user = auth()->user();
        foreach (@$user->kyc_data ?? [] as $kycData) {
            if ($kycData->type == 'file') {
                fileManager()->removeFile(getFilePath('verify') . '/' . $kycData->value);
            }
        }
        $userData = $formProcessor->processFormData($request, $formData);

        $user->kyc_data             = $userData;
        $user->kyc_rejection_reason = null;
        $user->kv                   = Status::KYC_PENDING;
        $user->save();

        $notify[] = 'KYC data submitted successfully';
        return apiResponse("kyc_submitted", "success", $notify);
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
            'histories' => $addMoney,
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
            'lastname.required'  => 'The last name field is required',
        ]);

        if ($validator->fails()) {
            return apiResponse("validation_error", "error", $validator->errors()->all());
        }

        $user            = auth()->user();
        $user->firstname = $request->firstname;
        $user->lastname  = $request->lastname;
        $user->address   = $request->address;
        $user->city      = $request->city;
        $user->state     = $request->state;
        $user->zip       = $request->zip;

        if ($request->hasFile('image')) {
            try {
                $old         = $user->image;
                $user->image = fileUploader($request->image, getFilePath('userProfile'), getFileSize('userProfile'), $old);
            } catch (\Exception $exp) {
                $notify[] = "Couldn't upload your image";
                return apiResponse("validation_error", "error", $notify);
            }
        }

        $user->save();
        $notify[] = 'Profile updated successfully';

        return apiResponse("profile_updated", "success", $notify);
    }

    public function submitPassword(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'current_pin' => 'required',
            ...pinValidationRule(true),

        ]);

        if ($validator->fails()) {
            return apiResponse("validation_error", "error", $validator->errors()->all());
        }

        $user = auth()->user();
        if (Hash::check($request->current_pin, $user->password)) {
            $password       = Hash::make($request->pin);
            $user->password = $password;
            $user->save();
            $notify[] = 'PIN changed successfully';
            return apiResponse("password_changed", "success", $notify);
        } else {
            $notify[] = "The password doesn't match!";
            return apiResponse("not_match", "validation_error", $notify);
        }
    }

    public function addDeviceToken(Request $request)
    {

        $validator = Validator::make($request->all(), [
            'token' => 'required',
        ]);
        if ($validator->fails()) {
            return apiResponse("validation_error", "error", $validator->errors()->all());
        }

        $deviceToken = DeviceToken::where('token', $request->token)->first();

        if ($deviceToken) {
            $notify[] = 'Token already exists';
            return apiResponse("token_exists", "error", $notify);
        }

        $deviceToken          = new DeviceToken();
        $deviceToken->user_id = auth()->user()->id;
        $deviceToken->token   = $request->token;
        $deviceToken->is_app  = Status::YES;
        $deviceToken->save();

        $notify[] = 'Token saved successfully';
        return apiResponse("token_saved", "success", $notify);
    }

    public function show2faForm()
    {
        $ga        = new GoogleAuthenticator();
        $user      = auth()->user();
        $secret    = $ga->createSecret();
        $qrCodeUrl = $ga->getQRCodeGoogleUrl($user->username . '@' . gs('site_name'), $secret);
        $notify[]  = '2FA Qr';

        return apiResponse("2fa_qr", "success", $notify, [
            'secret'      => $secret,
            'qr_code_url' => $qrCodeUrl,
        ]);
    }

    public function create2fa(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'secret' => 'required',
            'code'   => 'required',
        ]);

        if ($validator->fails()) {
            return apiResponse("validation_error", "error", $validator->errors()->all());
        }

        $user     = auth()->user();
        $response = verifyG2fa($user, $request->code, $request->secret);
        if ($response) {
            $user->tsc = $request->secret;
            $user->ts  = Status::ENABLE;
            $user->save();

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

        $user     = auth()->user();
        $response = verifyG2fa($user, $request->code);
        if ($response) {
            $user->tsc = null;
            $user->ts  = Status::DISABLE;
            $user->save();
            $notify[] = 'Two factor authenticator deactivated successfully';

            return apiResponse("2fa_qr", "success", $notify);
        } else {
            $notify[] = 'Wrong verification code';
            return apiResponse("wrong_verification", "error", $notify);
        }
    }

    public function pushNotifications()
    {
        $notifications = NotificationLog::where('user_id', auth()->id())->where('sender', 'firebase')->orderBy('id', 'desc')->paginate(getPaginate());
        $notify[]      = 'Push notifications';
        return apiResponse("notifications", "success", $notify, [
            'notifications' => $notifications,
        ]);
    }

    public function pushNotificationsRead($id)
    {
        $notification = NotificationLog::where('user_id', auth()->id())->where('sender', 'firebase')->find($id);
        if (! $notification) {
            $notify[] = 'Notification not found';
            return apiResponse("notification_not_found", "error", $notify);
        }
        $notify[]                = 'Notification marked as read successfully';
        $notification->user_read = 1;
        $notification->save();

        return apiResponse("notification_read", "success", $notify);
    }

    public function qrCode()
    {
        $notify[] = 'QR Code';
        return apiResponse("qr_code", "success", $notify, [
            'qr_code' => getQrCodeUrl(),
        ]);
    }

    public function qrCodeDownload()
    {
        $user   = auth()->user();
        $qrCode = $user->qrCode()->first();

        $file     = cryptoQR($qrCode->unique_code);
        $filename = $qrCode->unique_code . '.jpg';

        $manager  = new ImageManager(new Driver());
        $template = $manager->read('assets/images/qr_code_template/' . gs('qr_code_template'));

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

        if (! $data) {
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

    public function trxLimit()
    {
        $transactionCharges = TransactionCharge::get();
        $user               = auth()->user();

        $usedLimit['send_money']    = $user->trxLimit('send_money');
        $usedLimit['cash_in']       = $user->trxLimit('cash_in');
        $usedLimit['cash_out']      = $user->trxLimit('cash_out');
        $usedLimit['bank_transfer'] = $user->trxLimit('bank_transfer');
        $usedLimit['request_money'] = $user->trxLimit('request_money');

        $transactionCharges->transform(function ($trxCharge) use ($usedLimit) {
            if (array_key_exists($trxCharge->slug, $usedLimit)) {
                $trxCharge->daily_used   = $usedLimit[$trxCharge->slug]['daily'];
                $trxCharge->monthly_used = $usedLimit[$trxCharge->slug]['monthly'];
            }
            return $trxCharge;
        });

        $notify[] = 'Transaction limit and charge';
        return apiResponse("trx_charge", "success", $notify, [
            'transaction_charges' => $transactionCharges,
        ]);
    }

    public function validatePin(Request $request)
    {
        $validator = Validator::make($request->all(), [
            ...pinValidationRule(),
        ]);
        if ($validator->fails()) {
            return apiResponse("validation_error", "error", $validator->errors()->all());
        }
        $user = auth()->user();

        if (! Hash::check($request->pin, $user->password)) {
            $notify[] = 'Provided PIN does not correct';
            return apiResponse("validation_error", "error", $notify);
        }

        $notify[] = 'Provided PIN is correct';
        return apiResponse("valid_pin", "success", $notify);
    }

    public function notificationSettings()
    {
        $notify[] = 'Notification settings';
        return apiResponse("notification_settings", "success", $notify, [
            'user' => auth()->user(),
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

        $user                              = auth()->user();
        $user->en                          = $request->en ? Status::ENABLE : Status::DISABLE;
        $user->sn                          = $request->sn ? Status::ENABLE : Status::DISABLE;
        $user->pn                          = $request->pn ? Status::ENABLE : Status::DISABLE;
        $user->is_allow_promotional_notify = $request->is_allow_promotional_notify ? Status::YES : Status::NO;
        $user->save();

        $notify[] = 'Notification settings updated successfully';
        return apiResponse("notification_settings_update", "success", $notify, [
            'user' => $user,
        ]);
    }

    public function removePromotionalNotificationImage(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'image' => 'required',
        ]);

        $image = $request->image;

        if ($validator->fails()) {
            return apiResponse("validation_error", "error", $validator->errors()->all());
        }

        if (file_exists($image)) {
            unlink($image);
        }

        $notify[] = 'Promotional notification image deleted successfully';
        return apiResponse("remove_promotional_notification_image", "success", $notify);
    }

    public function userInfo()
    {
        $notify[] = 'User information';
        return apiResponse("user_info", "success", $notify, [
            'user'    => auth()->user(),
            'balance' => auth()->user()->balance,
        ]);
    }

    public function deleteAccount(Request $request)
    {
        $validator = Validator::make($request->all(), [
            ...pinValidationRule(),
        ]);

        if ($validator->fails()) {
            return apiResponse("validation_error", "error", $validator->errors()->all());
        }

        $user = auth()->user();

        if (! Hash::check($request->pin, $user->password)) {
            $notify[] = 'The provided pin is invalid';
            return apiResponse("invalid_pin", "error", $notify);
        }

        $user->status      = Status::USER_DELETE;
        $user->mobile      = 'deleted_' . $user->mobile;
        $user->username    = 'deleted_' . $user->username;
        $user->email       = 'deleted_' . $user->email;
        $user->provider_id = 'deleted_' . $user->provider_id;
        $user->save();

        $user->tokens()->delete();

        $notify[] = 'Account deleted successfully';
        return apiResponse("account_deleted", "success", $notify);
    }

    public function checkUser(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'user' => 'required',
        ]);

        if ($validator->fails()) {
            return apiResponse("validation_error", "error", $validator->errors()->all());
        }

        $findUser = findUserWithUsernameOrMobile();
        $user     = auth()->user();

        if (@$findUser && $user->username == @$findUser->username || $user->email == @$findUser->email) {
            $notify[] = "You can't send money to yourself";
            return apiResponse("validation_error", "error", $notify);
        }

        $notify[] = 'Check User';

        return apiResponse("check_user", "success", $notify, [
            'user' => $findUser,
        ]);
    }

    public function checkMerchant(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'merchant' => 'required',
        ]);

        if ($validator->fails()) {
            return apiResponse("validation_error", "error", $validator->errors()->all());
        }

        $merchant = findMerchantWithUsernameOrMobile();

        $notify[] = 'Check Merchant';
        return apiResponse("check_merchant", "success", $notify, [
            'merchant' => $merchant,
        ]);
    }

    public function checkAgent(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'agent' => 'required',
        ]);

        if ($validator->fails()) {
            return apiResponse("validation_error", "error", $validator->errors()->all());
        }

        $agent    = findAgentWithUsernameOrMobile();
        $notify[] = 'Check Agent';

        return apiResponse("check_agent", "success", $notify, [
            'agent' => $agent,
        ]);
    }

    public function transactions(Request $request)
    {
        $remarks      = Transaction::distinct('remark')->where('user_id', '!=', 0)->get('remark');
        $transactions = Transaction::where('user_id', auth()->id());

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

        $notify[] = 'Transactions data';
        return apiResponse("transactions", "success", $notify, [
            'transactions' => $transactions,
            'remarks'      => $remarks,
        ]);
    }

    public function remarkWiseTransaction($remark, $transaction)
    {
        if ($remark == "add_money") {
            $deposit = @$transaction->userDeposit;
            return [
                'title'     => @$deposit->gateway->name,
                'image_src' => getImage(getFilePath('gateway') . '/' . @$deposit->gateway->image),
            ];
        }

        if ($remark == "send_money") {
            $receivedUser = @$transaction->sendMoney->receiverUser;
            return [
                'title'     => @$receivedUser->fullname,
                'subtitle'  => @$receivedUser->mobileNumber,
                'image_src' => @$receivedUser->image_src,
            ];
        }

        if ($remark == "receive_money") {
            $sender = @$transaction->sendMoney->user;
            return [
                'title'     => @$sender->fullname,
                'subtitle'  => @$sender->mobileNumber,
                'image_src' => @$sender->image_src,
            ];
        }

        if ($remark == "request_money_accept") {
            $sender = @$transaction->moneyRequest->requestSender;
            return [
                'title'     => @$sender->fullname,
                'subtitle'  => @$sender->mobileNumber,
                'image_src' => @$sender->image_src,
                'note'      => @$transaction->moneyRequest->note,
            ];
        }
        if ($remark == "requested_money_fund_added") {
            $receiver = @$transaction->moneyRequest->requestReceiver;
            return [
                'title'     => @$receiver->fullname,
                'subtitle'  => @$receiver->mobileNumber,
                'image_src' => @$receiver->image_src,
                'note'      => @$transaction->moneyRequest->note,
            ];
        }
        if ($remark == "top_up") {
            $topup = @$transaction->topup;
            return [
                'title'     => @$topup->operator->name,
                'subtitle'  => @$topup->mobile_number,
                'image_src' => @$topup->operator->logo_urls[0],
                'note'      => null,
            ];
        }

        if ($remark == "cash_out") {
            $agent = @$transaction->cashOut->receiverAgent;
            return [
                'title'     => @$agent->fullname,
                'subtitle'  => @$agent->mobileNumber,
                'image_src' => @$agent->image_src,
                'note'      => null,
            ];
        }

        if ($remark == "make_payment") {
            $payment = @$transaction->payment;
            return [
                'title'     => @$payment->merchant->fullname,
                'subtitle'  => @$payment->merchant->mobileNumber,
                'image_src' => @$payment->merchant->image_src,
                'note'      => null,
            ];
        }
        if ($remark == "cashback") {
            $payment = @$transaction->payment;
            return [
                'title'     => @$payment->merchant->fullname,
                'subtitle'  => @$payment->merchant->mobileNumber,
                'image_src' => @$payment->merchant->image_src,
                'note'      => null,
            ];
        }

        if ($remark == "utility_bill" || $remark == "reject_utility_bill") {
            $company = @$transaction->utilityBill->company;
            return [
                'title'     => @$company->name,
                'subtitle'  => @$company->category->name,
                'feedback'  => @$transaction->utilityBill->admin_feedback,
                'image_src' => getImage(getFilePath('utility') . '/' . @$company->image),
            ];
        }

        if ($remark == "donation") {
            $charity = @$transaction->donation->donationFor;
            return [
                'title'     => @$charity->name,
                'subtitle'  => @$charity->details,
                'image_src' => getImage(getFilePath('donation') . '/' . @$charity->image),
                'note'      => $transaction->donation->reference,
            ];
        }

        if ($remark == "bank_transfer" || $remark == "reject_bank_transfer") {
            $bankTransfer = @$transaction->bankTransfer;
            return [
                'title'     => @$bankTransfer->bank->name,
                'subtitle'  => @$bankTransfer->account_number,
                'feedback'  => @$bankTransfer->admin_feedback,
                'image_src' => getImage(getFilePath('bank_transfer') . '/' . @$bankTransfer->bank->image),
            ];
        }

        if ($remark == "education_fee" || $remark == "reject_education_fee") {
            $institute = @$transaction->educationFee->institution;
            return [
                'title'     => @$institute->name,
                'subtitle'  => @$institute->category->name,
                'feedback'  => @$transaction->educationFe->admin_feedback,
                'image_src' => getImage(getFilePath('education_fee') . '/' . @$institute->image),
            ];
        }

        if ($remark == "microfinance" || $remark == "reject_microfinance") {
            $ngo = @$transaction->microfinance->ngo;
            return [
                'title'     => @$ngo->name,
                'feedback'  => @$transaction->microfinance->admin_feedback,
                'image_src' => getImage(getFilePath('microfinance') . '/' . @$ngo->image),
            ];
        }

        if ($remark == "virtual_card_add_fund" || $remark == "virtual_card_payment") {
            if ($transaction->virtual_card_id != 0) {
                $virtualCard = @$transaction->virtualCard;
            } else {
                $virtualCard = @$transaction->forVirtualCard;
            }
            return [
                'title'    => @$virtualCard->cardHolder->name,
                'subtitle' => printVirtualCardNumber($virtualCard),
            ];
        }

        if ($remark == "cash_in") {
            $agent = $transaction->cashIn->agent;
            return [
                'title'     => @$agent->fullname,
                'subtitle'  => @$agent->mobileNumber,
                'image_src' => @$agent->image_src,
            ];
        }

        if ($remark == "gift_card_purchase") {
            $giftCard = $transaction?->giftCardPurchase?->giftCard;
            return [
                'title'     => @$giftCard->product_name,
                'image_src' => @$giftCard->logo_urls[0],
            ];
        }

        if ($remark == "investment") {
            $investment = @$transaction?->investment;
            $plan       = @$investment->plan;
            return [
                'title'    => @$plan->name,
                'subtitle' => @$plan->investTime->name,
            ];
        }
        if ($remark == "investment_capital_back") {
            $investment = @$transaction?->investment;
            $plan       = @$investment->plan;
            return [
                'title'    => @$plan->name,
                'subtitle' => @$plan->investTime->name,
            ];
        }

        if ($remark == "investment_interest") {
            $investment = @$transaction?->investmentInterest->investment;
            $plan       = @$investment->plan;
            return [
                'title'    => @$plan->name,
                'subtitle' => @$plan->investTime->name,
            ];
        }

        return (object) [];
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

        $startingBalance = Transaction::where('user_id', auth()->id())
            ->whereYear('created_at', $request->year)
            ->whereMonth('created_at', '<', $request->month)
            ->orderBy('created_at', 'desc')
            ->value('post_balance') ?? 0;

        $transactions = Transaction::where('user_id', auth()->id())
            ->whereYear('created_at', $request->year)
            ->whereMonth('created_at', $request->month);

        $totalTransactionAmount = $transactions->sum('amount');
        $totalTransactionCount  = $transactions->count();

        $notify[] = 'Statements retrieved successfully';
        return apiResponse("statements", "success", $notify, [
            'starting_balance'         => $startingBalance,
            'total_transaction_amount' => $totalTransactionAmount,
            'total_transaction_count'  => $totalTransactionCount,
            'current_balance'          => auth()->user()->balance,
        ]);
    }
}
