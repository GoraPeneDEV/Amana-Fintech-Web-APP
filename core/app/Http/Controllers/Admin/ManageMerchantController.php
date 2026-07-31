<?php

namespace App\Http\Controllers\Admin;

use App\Constants\Status;
use App\Http\Controllers\Controller;
use App\Lib\UserNotificationSender;
use App\Models\MakePayment;
use App\Models\NotificationLog;
use App\Models\Transaction;
use App\Models\Merchant;
use App\Models\UserLogin;
use App\Models\Withdrawal;
use Illuminate\Http\Request;
use App\Rules\FileTypeValidate;
use Illuminate\Support\Facades\Auth;

class ManageMerchantController extends Controller
{
    public function allMerchants()
    {
        $pageTitle = 'All Merchants';
        extract($this->merchantData());
        if (request()->export) {
            return $this->callExportData($baseQuery);
        }
        $merchants = $baseQuery->paginate(getPaginate());
        return view('admin.merchants.list', compact('pageTitle', 'merchants', 'widget'));
    }


    public function activeMerchants()
    {
        $pageTitle = 'Active Merchants';
        extract($this->merchantData("active"));

        if (request()->export) {
            return $this->callExportData($baseQuery);
        }

        $merchants = $baseQuery->paginate(getPaginate());
        return view('admin.merchants.list', compact('pageTitle', 'merchants', 'widget'));
    }

    public function bannedMerchants()
    {
        $pageTitle = 'Banned Merchants';
        extract($this->merchantData("banned"));

        if (request()->export) {
            return $this->callExportData($baseQuery);
        }
        $merchants = $baseQuery->paginate(getPaginate());

        return view('admin.merchants.list', compact('pageTitle', 'merchants', 'widget'));
    }
    public function deletedMerchants()
    {
        $pageTitle = 'Account Deleted  Merchants';
        extract($this->merchantData("deletedMerchant"));

        if (request()->export) {
            return $this->callExportData($baseQuery);
        }
        $merchants = $baseQuery->paginate(getPaginate());

        return view('admin.merchants.list', compact('pageTitle', 'merchants', 'widget'));
    }


    public function emailUnverifiedMerchants()
    {
        $pageTitle = 'Email Unverified Merchants';
        extract($this->merchantData('emailUnverified'));

        if (request()->export) {
            return $this->callExportData($baseQuery);
        }
        $merchants = $baseQuery->paginate(getPaginate());

        return view('admin.merchants.list', compact('pageTitle', 'merchants', 'widget'));
    }

    public function kycUnverifiedMerchants()
    {
        $pageTitle = 'KYC Unverified Merchants';
        extract($this->merchantData('kycUnverified'));

        if (request()->export) {
            return $this->callExportData($baseQuery);
        }
        $merchants = $baseQuery->paginate(getPaginate());

        return view('admin.merchants.list', compact('pageTitle', 'merchants', 'widget'));
    }

    public function kycPendingMerchants()
    {
        $pageTitle = 'KYC Pending Merchants';
        extract($this->merchantData('kycPending'));

        if (request()->export) {
            return $this->callExportData($baseQuery);
        }
        $merchants = $baseQuery->paginate(getPaginate());

        return view('admin.merchants.list', compact('pageTitle', 'merchants', 'widget'));
    }

    public function emailVerifiedMerchants()
    {
        $pageTitle = 'Email Verified Merchants';
        extract($this->merchantData('emailVerified'));

        if (request()->export) {
            return $this->callExportData($baseQuery);
        }
        $merchants = $baseQuery->paginate(getPaginate());

        return view('admin.merchants.list', compact('pageTitle', 'merchants', 'widget'));
    }


    public function mobileUnverifiedMerchants()
    {
        $pageTitle = 'Mobile Unverified Merchants';
        extract($this->merchantData('mobileUnverified'));

        if (request()->export) {
            return $this->callExportData($baseQuery);
        }
        $merchants = $baseQuery->paginate(getPaginate());

        return view('admin.merchants.list', compact('pageTitle', 'merchants', 'widget'));
    }

    public function mobileVerifiedMerchants()
    {
        $pageTitle = 'Mobile Verified Merchants';
        extract($this->merchantData('mobileVerified'));

        if (request()->export) {
            return $this->callExportData($baseQuery);
        }
        $merchants = $baseQuery->paginate(getPaginate());

        return view('admin.merchants.list', compact('pageTitle', 'merchants', 'widget'));
    }

    public function merchantsWithBalance()
    {
        $pageTitle = 'Merchants with Balance';
        extract($this->merchantData('withBalance'));

        if (request()->export) {
            return $this->callExportData($baseQuery);
        }
        $merchants = $baseQuery->paginate(getPaginate());

        return view('admin.merchants.list', compact('pageTitle', 'merchants', 'widget'));
    }

    protected function merchantData($scope = 'query')
    {
        $baseQuery  = Merchant::$scope()->searchable(['email', 'username', 'firstname', 'lastname','mobile'])->dateFilter()->filter(['status'])->orderBy('id', getOrderBy());

        $countQuery = Merchant::query();
        $widget['all']   = (clone $countQuery)->count();
        $widget['today'] = (clone $countQuery)->whereDate('created_at', now())->count();
        $widget['week']  = (clone $countQuery)->whereDate('created_at', ">=", now()->subDays(7))->count();
        $widget['month'] = (clone $countQuery)->whereDate('created_at', ">=", now()->subDays(30))->count();

        return [
            'baseQuery' => $baseQuery,
            'widget'    => $widget
        ];
    }

    public function detail($id)
    {
        $merchant      = Merchant::findOrFail($id);
        $pageTitle = 'Merchant Detail - ' . $merchant->username;
        $loginLogs = UserLogin::where('merchant_id', $merchant->id)->take(6)->get();

        $widget['total_withdraw']    = Withdrawal::where('merchant_id', $merchant->id)->approved()->sum('amount');
        $widget['total_transaction'] = Transaction::where('merchant_id', $merchant->id)->sum('amount');
        $widget['merchant_amount']   = MakePayment::where('merchant_id', $merchant->id)->sum('merchant_amount');
        $countries                   = json_decode(file_get_contents(resource_path('views/partials/country.json')));

        return view('admin.merchants.detail', compact('pageTitle', 'merchant', 'widget', 'countries', 'loginLogs'));
    }

    public function kycDetails($id)
    {
        $pageTitle = 'KYC Details';
        $merchant      = Merchant::findOrFail($id);
        return view('admin.merchants.kyc_detail', compact('pageTitle', 'merchant'));
    }

    public function kycApprove($id)
    {
        $merchant     = Merchant::findOrFail($id);
        $merchant->kv = Status::KYC_VERIFIED;
        $merchant->save();

        notify($merchant, 'KYC_APPROVE', []);

        $notify[] = ['success', 'KYC approved successfully'];
        return to_route('admin.merchants.kyc.pending')->withNotify($notify);
    }

    public function kycReject(Request $request, $id)
    {
        $request->validate([
            'reason' => 'required'
        ]);

        $merchant                       = Merchant::findOrFail($id);
        $merchant->kv                   = Status::KYC_UNVERIFIED;
        $merchant->kyc_rejection_reason = $request->reason;
        $merchant->save();

        notify($merchant, 'KYC_REJECT', [
            'reason' => $request->reason
        ]);

        $notify[] = ['success', 'KYC rejected successfully'];
        return to_route('admin.merchants.kyc.pending')->withNotify($notify);
    }


    public function update(Request $request, $id)
    {
        $merchant         = Merchant::findOrFail($id);
        $countryData  = json_decode(file_get_contents(resource_path('views/partials/country.json')));
        $countryArray = (array)$countryData;
        $countries    = implode(',', array_keys($countryArray));

        $countryCode = $request->country;
        $country     = $countryData->$countryCode->country;
        $dialCode    = $countryData->$countryCode->dial_code;

        $request->validate([
            'firstname' => 'required|string|max:40',
            'lastname'  => 'required|string|max:40',
            'email'     => 'required|email|string|max:40|unique:merchants,email,' . $merchant->id,
            'mobile'    => 'required|string|max:40',
            'country'   => 'required|in:' . $countries,
        ]);

        $exists = Merchant::where('mobile', $request->mobile)->where('dial_code', $dialCode)->where('id', '!=', $merchant->id)->exists();

        if ($exists) {
            $notify[] = ['error', 'The mobile number already exists.'];
            return back()->withNotify($notify);
        }

        $merchant->mobile    = $request->mobile;
        $merchant->firstname = $request->firstname;
        $merchant->lastname  = $request->lastname;
        $merchant->email     = $request->email;

        $merchant->address      = $request->address;
        $merchant->city         = $request->city;
        $merchant->state        = $request->state;
        $merchant->zip          = $request->zip;
        $merchant->country_name = @$country;
        $merchant->dial_code    = $dialCode;
        $merchant->country_code = $countryCode;

        $merchant->ev = $request->ev ? Status::VERIFIED : Status::UNVERIFIED;
        $merchant->sv = $request->sv ? Status::VERIFIED : Status::UNVERIFIED;
        $merchant->ts = $request->ts ? Status::ENABLE : Status::DISABLE;

        if (!$request->kv) {
            $merchant->kv = Status::KYC_UNVERIFIED;
            if ($merchant->kyc_data) {
                foreach ($merchant->kyc_data as $kycData) {
                    if ($kycData->type == 'file') {
                        fileManager()->removeFile(getFilePath('verify') . '/' . $kycData->value);
                    }
                }
            }
            $merchant->kyc_data = null;
        } else {
            $merchant->kv = Status::KYC_VERIFIED;
        }
        $merchant->save();

        $notify[] = ['success', 'Merchant details updated successfully'];
        return back()->withNotify($notify);
    }

    public function addSubBalance(Request $request, $id)
    {

        $request->validate([
            'amount' => 'required|numeric|gt:0',
            'act'    => 'required|in:add,sub',
            'remark' => 'required|string|max:255',
        ]);

        $merchant   = Merchant::findOrFail($id);
        $amount = $request->amount;
        $trx    = getTrx();


        $transaction = new Transaction();

        if ($request->act == 'add') {
            $merchant->balance += $amount;

            $transaction->trx_type = '+';
            $transaction->remark   = 'balance_add';

            $notifyTemplate = 'BAL_ADD';
            $message        = 'Balance added successfully';
        } else {
            if ($amount > $merchant->balance) {
                $notify[] = ['error', $merchant->username . ' doesn\'t have sufficient balance.'];
                return back()->withNotify($notify);
            }

            $merchant->balance -= $amount;

            $transaction->trx_type = '-';
            $transaction->remark   = 'balance_subtract';

            $notifyTemplate = 'BAL_SUB';
            $message        = 'Balance subtracted successfully';
        }

        $merchant->save();

        $transaction->merchant_id  = $merchant->id;
        $transaction->amount       = $amount;
        $transaction->post_balance = $merchant->balance;
        $transaction->charge       = 0;
        $transaction->trx          = $trx;
        $transaction->details      = $request->remark;
        $transaction->save();
        notify($merchant, $notifyTemplate, [
            'trx'          => $trx,
            'amount'       => showAmount($amount, currencyFormat: false),
            'remark'       => $request->remark,
            'post_balance' => showAmount($merchant->balance, currencyFormat: false)
        ]);
        $notify[] = ['success', $message];
        return back()->withNotify($notify);
    }

    public function login($id)
    {
        Auth('merchant')->loginUsingId($id);
        return to_route('merchant.home');
    }

    public function status(Request $request, $id)
    {
        $merchant = Merchant::findOrFail($id);
        if ($merchant->status == Status::MERCHANT_ACTIVE) {
            $request->validate([
                'reason' => 'required|string|max:255'
            ]);
            $merchant->status     = Status::MERCHANT_BAN;
            $merchant->ban_reason = $request->reason;
            $notify[]         = ['success', 'Merchant banned successfully'];
        } else {
            $merchant->status     = Status::MERCHANT_ACTIVE;
            $merchant->ban_reason = null;
            $notify[]         = ['success', 'Merchant unbanned successfully'];
        }
        $merchant->save();
        return back()->withNotify($notify);
    }

    public function showNotificationSingleForm($id)
    {
        $merchant = Merchant::findOrFail($id);
        if (!gs('en') && !gs('sn') && !gs('pn')) {
            $notify[] = ['warning', 'Notification options are disabled currently'];
            return to_route('admin.merchants.detail', $merchant->id)->withNotify($notify);
        }
        $pageTitle = 'Send Notification to ' . $merchant->username;
        return view('admin.merchants.notification_single', compact('pageTitle', 'merchant'));
    }

    public function sendNotificationSingle(Request $request, $id)
    {
        $request->validate([
            'message' => 'required',
            'via'     => 'required|in:email,sms,push',
            'subject' => 'required_if:via,email,push',
            'image'   => ['nullable', 'image', new FileTypeValidate(['jpg', 'jpeg', 'png'])],
        ]);

        if (!gs('en') && !gs('sn') && !gs('pn')) {
            $notify[] = ['warning', 'Notification options are disabled currently'];
            return to_route('admin.dashboard')->withNotify($notify);
        }
        return (new UserNotificationSender())->notificationToSingle($request, $id, 'Merchant');
    }

    public function showNotificationAllForm()
    {
        if (!gs('en') && !gs('sn') && !gs('pn')) {
            $notify[] = ['warning', 'Notification options are disabled currently'];
            return to_route('admin.dashboard')->withNotify($notify);
        }

        $notifyToMerchant = Merchant::notifyToMerchant();
        $merchants        = Merchant::active()->count();
        $pageTitle    = 'Notification to Verified Merchants';

        if (session()->has('SEND_NOTIFICATION') && !request()->email_sent) {
            session()->forget('SEND_NOTIFICATION');
        }

        return view('admin.merchants.notification_all', compact('pageTitle', 'merchants', 'notifyToMerchant'));
    }

    public function sendNotificationAll(Request $request)
    {
        $request->validate([
            'via'                          => 'required|in:email,sms,push',
            'message'                      => 'required',
            'subject'                      => 'required_if:via,email,push',
            'start'                        => 'required|integer|gte:1',
            'batch'                        => 'required|integer|gte:1',
            'being_sent_to'                => 'required',
            'cooling_time'                 => 'required|integer|gte:1',
            'number_of_top_deposited_merchant' => 'required_if:being_sent_to,topDepositedMerchants|integer|gte:0',
            'number_of_days'               => 'required_if:being_sent_to,notLoginMerchants|integer|gte:0',
            'image'                        => ["nullable", 'image', new FileTypeValidate(['jpg', 'jpeg', 'png'])],
        ], [
            'number_of_days.required_if'               => "Number of days field is required",
            'number_of_top_deposited_merchant.required_if' => "Number of top deposited merchant field is required",
        ]);

        if (!gs('en') && !gs('sn') && !gs('pn')) {
            $notify[] = ['warning', 'Notification options are disabled currently'];
            return to_route('admin.dashboard')->withNotify($notify);
        }

        return (new UserNotificationSender())->notificationToAll($request, 'Merchant');
    }


    public function countBySegment($methodName)
    {
        return Merchant::$methodName()->count();
    }

    public function list()
    {
        $query = Merchant::get();
        $merchants = $query->searchable(['email', 'username'])->orderBy('id', 'desc')->paginate(getPaginate());

        return response()->json([
            'success' => true,
            'merchants'   => $merchants,
            'more'    => $merchants->hasMorePages()
        ]);
    }

    public function notificationLog($id)
    {
        $merchant  = Merchant::findOrFail($id);
        $userType  = 'MERCHANT';
        $pageTitle = 'Notifications Sent to ' . $merchant->username;
        $logs      = NotificationLog::where('merchant_id', $id)->with('merchant')->orderBy('id', 'desc')->paginate(getPaginate());
        return view('admin.reports.notification_history', compact('pageTitle', 'logs', 'merchant', 'userType'));
    }

    private function callExportData($baseQuery)
    {
        return exportData($baseQuery, request()->export, "merchant", "A4 landscape");
    }
}
