<?php

namespace App\Http\Controllers\Admin;

use App\Constants\Status;
use App\Http\Controllers\Controller;
use App\Models\Admin;
use App\Models\AdminNotification;
use App\Models\Agent;
use App\Models\BankTransfer;
use App\Models\CashOut;
use App\Models\Deposit;
use App\Models\EducationFee;
use App\Models\MakePayment;
use App\Models\Microfinance;
use App\Models\SendMoney;
use App\Models\Transaction;
use App\Models\User;
use App\Models\UserLogin;
use App\Models\UtilityBill;
use App\Models\Withdrawal;
use App\Rules\FileTypeValidate;
use App\Traits\AdminOperation;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class AdminController extends Controller
{
    use AdminOperation;

    public function dashboard()
    {
        // 
        $userQuery     = User::query();
        $agentQuery    = Agent::query();
        $merchantQuery = Agent::query();
        $depositQuery  = Deposit::query();
        $withdrawQuery = Withdrawal::query();
        $trxQuery      = Transaction::query();

        $userStatistic['active_users']            = (clone $userQuery)->active()->count();
        $userStatistic['banned_user']             = (clone $userQuery)->banned()->count();
        $userStatistic['email_unverified_users']  = (clone $userQuery)->emailUnverified()->count();
        $userStatistic['email_verified_users']    = (clone $userQuery)->where('status', Status::VERIFIED)->count();
        $userStatistic['mobile_unverified_users'] = (clone $userQuery)->mobileUnverified()->count();

        $widget['total_agent']             = (clone $agentQuery)->active()->count();
        $widget['active_agent']            = (clone $agentQuery)->active()->count();
        $widget['email_unverified_agent']  = (clone $agentQuery)->emailUnverified()->count();
        $widget['mobile_unverified_agent'] = (clone $agentQuery)->mobileUnverified()->count();

        $widget['total_merchant']             = (clone $merchantQuery)->active()->count();
        $widget['active_merchant']            = (clone $merchantQuery)->active()->count();
        $widget['email_unverified_merchant']  = (clone $merchantQuery)->emailUnverified()->count();
        $widget['mobile_unverified_merchant'] = (clone $merchantQuery)->mobileUnverified()->count();

        $revenueStatistic['send_money_charge']           = SendMoney::sum('charge');
        $revenueStatistic['cash_out_charge']             = CashOut::sum('charge');
        $revenueStatistic['payment_charge']              = MakePayment::sum('charge');
        $revenueStatistic['bank_transfer_charge']        = BankTransfer::approved()->sum('charge');
        $revenueStatistic['microfinance_payment_charge'] = Microfinance::approved()->sum('charge');
        $revenueStatistic['utility_bill_charge']         = UtilityBill::approved()->sum('charge');
        $revenueStatistic['education_fee_charge']        = EducationFee::approved()->sum('charge');


        $widget['total_deposit_amount_user']         = (clone $depositQuery)->userDeposit()->successful()->sum('amount');
        $widget['total_deposit_pending_user']        = (clone $depositQuery)->userDeposit()->pending()->sum('amount');
        $widget['total_deposit_pending_count_user']  = (clone $depositQuery)->userDeposit()->pending()->count();
        $widget['total_deposit_rejected_user']       = (clone $depositQuery)->userDeposit()->rejected()->sum('amount');
        $widget['total_deposit_rejected_count_user'] = (clone $depositQuery)->userDeposit()->rejected()->count();
        $widget['total_deposit_charge_user']         = (clone $depositQuery)->userDeposit()->successful()->sum('charge');

        $widget['total_withdraw_amount_merchant']         = (clone $withdrawQuery)->agentWithdraw()->approved()->sum('amount');
        $widget['total_withdraw_pending_merchant']        = (clone $withdrawQuery)->agentWithdraw()->pending()->sum('amount');
        $widget['total_withdraw_pending_count_merchant']  = (clone $withdrawQuery)->agentWithdraw()->pending()->count();
        $widget['total_withdraw_rejected_merchant']       = (clone $withdrawQuery)->agentWithdraw()->rejected()->sum('amount');
        $widget['total_withdraw_rejected_count_merchant'] = (clone $withdrawQuery)->agentWithdraw()->rejected()->count();
        $widget['total_withdraw_charge_merchant']         = (clone $withdrawQuery)->agentWithdraw()->approved()->sum('charge');

        $widget['total_deposit_amount_agent']         = (clone $depositQuery)->agentDeposit()->successful()->sum('amount');
        $widget['total_deposit_pending_agent']        = (clone $depositQuery)->agentDeposit()->pending()->sum('amount');
        $widget['total_deposit_pending_count_agent']  = (clone $depositQuery)->agentDeposit()->pending()->count();
        $widget['total_deposit_rejected_agent']       = (clone $depositQuery)->agentDeposit()->rejected()->sum('amount');
        $widget['total_deposit_rejected_count_agent'] = (clone $depositQuery)->agentDeposit()->rejected()->count();
        $widget['total_deposit_charge_agent']         = (clone $depositQuery)->agentDeposit()->successful()->sum('charge');

        $widget['total_withdraw_amount_agent']         = (clone $withdrawQuery)->approved()->sum('amount');
        $widget['total_withdraw_pending_agent']        = (clone $withdrawQuery)->pending()->sum('amount');
        $widget['total_withdraw_pending_count_agent']  = (clone $withdrawQuery)->pending()->count();
        $widget['total_withdraw_rejected_agent']       = (clone $withdrawQuery)->rejected()->sum('amount');
        $widget['total_withdraw_rejected_count_agent'] = (clone $withdrawQuery)->rejected()->count();
        $widget['total_withdraw_charge_agent']         = (clone $withdrawQuery)->approved()->sum('charge');

        $widget['total_trx']          = (clone $trxQuery)->count();
        $widget['total_trx_user']     = (clone $trxQuery)->where('user_id', '!=', 0)->count();
        $widget['total_trx_agent']    = (clone $trxQuery)->where('agent_id', '!=', 0)->count();
        $widget['total_trx_merchant'] = (clone $trxQuery)->where('merchant_id', '!=', 0)->count();

        $pageTitle = 'Dashboard';
        $admin     = auth('admin')->user();
        $userLogin = UserLogin::selectRaw('browser, COUNT(*) as total')
            ->groupBy('browser')
            ->orderBy('total', 'desc')
            ->get();

        return view('admin.dashboard', compact('pageTitle', 'admin', 'widget', 'userLogin', 'userStatistic', 'revenueStatistic'));
    }

    public function profile()
    {
        $pageTitle = 'My Profile';
        $admin     = auth('admin')->user();
        return view('admin.profile', compact('pageTitle', 'admin'));
    }

    public function profileUpdate(Request $request)
    {
        $request->validate([
            'name'  => 'required|max:40',
            'email' => 'required|email',
            'image' => ['nullable', 'image', new FileTypeValidate(['jpg', 'jpeg', 'png'])]
        ]);

        $user = auth('admin')->user();

        if ($request->hasFile('image')) {
            try {
                $old         = $user->image;
                $user->image = fileUploader($request->image, getFilePath('adminProfile'), getFileSize('adminProfile'), $old);
            } catch (\Exception $exp) {
                $notify[] = ['error', 'Couldn\'t upload your image'];
                return back()->withNotify($notify);
            }
        }

        $user->name  = $request->name;
        $user->email = $request->email;
        $user->save();

        $notify[] = ['success', 'Profile updated successfully'];
        return to_route('admin.profile')->withNotify($notify);
    }

    public function password()
    {
        $pageTitle = 'Change Password';
        $admin     = auth('admin')->user();
        return view('admin.password', compact('pageTitle', 'admin'));
    }

    public function passwordUpdate(Request $request)
    {
        $request->validate([
            'old_password' => 'required',
            'password'     => 'required|min:6|confirmed',
        ]);

        $user = auth('admin')->user();
        if (!Hash::check($request->old_password, $user->password)) {
            $notify[] = ['error', 'Password doesn\'t match!!'];
            return back()->withNotify($notify);
        }
        $user->password = Hash::make($request->password);
        $user->save();
        $notify[] = ['success', 'Password changed successfully.'];
        return to_route('admin.password')->withNotify($notify);
    }

    public function depositAndWithdrawReport(Request $request)
    {
        $today             = Carbon::today();
        $timePeriodDetails = $this->timePeriodDetails($today);
        $timePeriod        = (object) $timePeriodDetails[$request->time_period ?? 'daily'];
        $carbonMethod      = $timePeriod->carbon_method;
        $starDate          = $today->copy()->$carbonMethod($timePeriod->take);
        $endDate           = $today->copy();

        $deposits = Deposit::successful()
            ->whereDate('created_at', '>=', $starDate)
            ->whereDate('created_at', '<=', $endDate)
            ->selectRaw('DATE_FORMAT(created_at, "' . $timePeriod->sql_date_format . '") as date,SUM(amount) as amount')
            ->orderBy('date', 'asc')
            ->groupBy('date')
            ->get();

        $withdrawals = Withdrawal::approved()
            ->whereDate('created_at', '>=', $starDate)
            ->whereDate('created_at', '<=', $endDate)
            ->selectRaw('DATE_FORMAT(created_at, "' . $timePeriod->sql_date_format . '") as date,SUM(amount) as amount')
            ->orderBy('date', 'asc')
            ->groupBy('date')
            ->get();

        $data       = [];

        for ($i = 0; $i < $timePeriod->take; $i++) {
            $date       = $today->copy()->$carbonMethod($i)->format($timePeriod->php_date_format);
            $deposit    = $deposits->where('date', $date)->first();
            $withdrawal = $withdrawals->where('date', $date)->first();

            $depositAmount    = $deposit ? $deposit->amount : 0;
            $withdrawalAmount = $withdrawal ? $withdrawal->amount : 0;

            $data[$date] = [
                'deposited_amount' => $depositAmount,
                'withdrawn_amount' => $withdrawalAmount
            ];
        }

        return response()->json($data);
    }

    public function transactionReport(Request $request)
    {

        $today             = Carbon::today();
        $timePeriodDetails = $this->timePeriodDetails($today);

        $timePeriod        = (object) $timePeriodDetails[$request->time_period ?? 'daily'];
        $carbonMethod      = $timePeriod->carbon_method;
        $starDate          = $today->copy()->$carbonMethod($timePeriod->take);
        $endDate           = $today->copy();

        $plusTransactions   = Transaction::where('trx_type', '+')
            ->whereDate('created_at', '>=', $starDate)
            ->whereDate('created_at', '<=', $endDate)
            ->selectRaw('DATE_FORMAT(created_at, "' . $timePeriod->sql_date_format . '") as date,SUM(amount) as amount')
            ->orderBy('date', 'asc')
            ->groupBy('date')
            ->get();

        $minusTransactions  = Transaction::where('trx_type', '-')
            ->whereDate('created_at', '>=', $starDate)
            ->whereDate('created_at', '<=', $endDate)
            ->selectRaw('DATE_FORMAT(created_at, "' . $timePeriod->sql_date_format . '") as date,SUM(amount) as amount')
            ->orderBy('date', 'asc')
            ->groupBy('date')
            ->get();

        $data = [];

        for ($i = 0; $i < $timePeriod->take; $i++) {
            $date       = $today->copy()->$carbonMethod($i)->format($timePeriod->php_date_format);
            $plusTransaction  = $plusTransactions->where('date', $date)->first();
            $minusTransaction = $minusTransactions->where('date', $date)->first();

            $plusAmount  = $plusTransaction ? $plusTransaction->amount : 0;
            $minusAmount = $minusTransaction ? $minusTransaction->amount : 0;

            $data[$date] = [
                'plus_amount'  => $plusAmount,
                'minus_amount' => $minusAmount
            ];
        }

        return response()->json($data);
    }

    public function notifications()
    {
        $notifications   = AdminNotification::orderBy('id', 'desc')->selectRaw('*,DATE(created_at) as date')->with('user')->paginate(getPaginate());
        $hasUnread       = AdminNotification::where('is_read', Status::NO)->exists();
        $hasNotification = AdminNotification::exists();
        $pageTitle       = 'All Notifications';
        return view('admin.notifications', compact('pageTitle', 'notifications', 'hasUnread', 'hasNotification'));
    }


    public function notificationRead($id)
    {

        $notification          = AdminNotification::findOrFail($id);
        $notification->is_read = Status::YES;
        $notification->save();
        $url = $notification->click_url;
        if ($url == '#') {
            $url = url()->previous();
        }
        return redirect($url);
    }

    public function readAllNotification()
    {
        AdminNotification::where('is_read', Status::NO)->update([
            'is_read' => Status::YES
        ]);
        $notify[] = ['success', 'Notifications read successfully'];
        return back()->withNotify($notify);
    }

    public function deleteAllNotification()
    {
        AdminNotification::truncate();
        $notify[] = ['success', 'Notifications deleted successfully'];
        return back()->withNotify($notify);
    }

    public function deleteSingleNotification($id)
    {
        AdminNotification::where('id', $id)->delete();
        $notify[] = ['success', 'Notification deleted successfully'];
        return back()->withNotify($notify);
    }

    private function timePeriodDetails($today): array
    {
        if (request()->date) {
            $date                 = explode('to', request()->date);
            $startDateForCustom   = Carbon::parse(trim($date[0]))->format('Y-m-d');
            $endDateDateForCustom = @$date[1] ? Carbon::parse(trim(@$date[1]))->format('Y-m-d') : $startDateForCustom;
        } else {
            $startDateForCustom   = $today->copy()->subDays(15);
            $endDateDateForCustom = $today->copy();
        }

        return  [
            'daily'   => [
                'sql_date_format' => "%d %b,%Y",
                'php_date_format' => "d M,Y",
                'take'            => 15,
                'carbon_method'   => 'subDays',
                'start_date'      => $today->copy()->subDays(15),
                'end_date'        => $today->copy(),
            ],
            'monthly' => [
                'sql_date_format' => "%b,%Y",
                'php_date_format' => "M,Y",
                'take'            => 12,
                'carbon_method'   => 'subMonths',
                'start_date'      => $today->copy()->subMonths(12),
                'end_date'        => $today->copy(),
            ],
            'yearly'  => [
                'sql_date_format' => '%Y',
                'php_date_format' => 'Y',
                'take'            => 12,
                'carbon_method'   => 'subYears',
                'start_date'      => $today->copy()->subYears(12),
                'end_date'        => $today->copy(),
            ],
            'date_range'   => [
                'sql_date_format' => "%d %b,%Y",
                'php_date_format' => "d M,Y",
                'take'            => (int) Carbon::parse($startDateForCustom)->diffInDays(Carbon::parse($endDateDateForCustom)),
                'carbon_method'   => 'subDays',
                'start_date'      => $startDateForCustom,
                'end_date'        => $endDateDateForCustom,
            ],
        ];
    }

    public function downloadAttachment($fileHash)
    {
        $filePath  = decrypt($fileHash);
        $extension = pathinfo($filePath, PATHINFO_EXTENSION);
        $title     = slug(gs('site_name')) . '- attachments.' . $extension;
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

    public function checkUser(Request $request)
    {
        $exist['data'] = false;
        $exist['type'] = null;
        $exist['field'] = '';

        if ($request->email) {
            $exist['data'] = Admin::where('email', $request->email)->exists();
            $exist['type'] = 'email';
            $exist['field'] = 'Email';
        }
        if ($request->username) {
            $exist['data'] = Admin::where('username', $request->username)->exists();
            $exist['type'] = 'username';
            $exist['field'] = 'Username';
        }

        return response($exist);
    }
}
