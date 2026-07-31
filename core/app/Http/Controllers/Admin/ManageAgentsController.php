<?php

namespace App\Http\Controllers\Admin;

use App\Constants\Status;
use App\Http\Controllers\Controller;
use App\Lib\UserNotificationSender;
use App\Models\Deposit;
use App\Models\NotificationLog;
use App\Models\Transaction;
use App\Models\Agent;
use App\Models\UserLogin;
use App\Models\Withdrawal;
use Illuminate\Http\Request;
use App\Rules\FileTypeValidate;

class ManageAgentsController extends Controller
{
    public function allAgents()
    {
        $pageTitle = 'All Agents';
        extract($this->agentData());
        if (request()->export) {
            return $this->callExportData($baseQuery);
        }
        $agents = $baseQuery->paginate(getPaginate());
        return view('admin.agents.list', compact('pageTitle', 'agents', 'widget'));
    }


    public function activeAgents()
    {
        $pageTitle = 'Active Agents';
        extract($this->agentData("active"));

        if (request()->export) {
            return $this->callExportData($baseQuery);
        }

        $agents = $baseQuery->paginate(getPaginate());
        return view('admin.agents.list', compact('pageTitle', 'agents', 'widget'));
    }

    public function bannedAgents()
    {
        $pageTitle = 'Banned Agents';
        extract($this->agentData("banned"));

        if (request()->export) {
            return $this->callExportData($baseQuery);
        }
        $agents = $baseQuery->paginate(getPaginate());

        return view('admin.agents.list', compact('pageTitle', 'agents', 'widget'));
    }

    public function deletedAgents()
    {
        $pageTitle = 'Account Deleted Agents';
        extract($this->agentData("deletedAgent"));

        if (request()->export) {
            return $this->callExportData($baseQuery);
        }
        $agents = $baseQuery->paginate(getPaginate());

        return view('admin.agents.list', compact('pageTitle', 'agents', 'widget'));
    }

    public function emailUnverifiedAgents()
    {
        $pageTitle = 'Email Unverified Agents';
        extract($this->agentData('emailUnverified'));

        if (request()->export) {
            return $this->callExportData($baseQuery);
        }
        $agents = $baseQuery->paginate(getPaginate());

        return view('admin.agents.list', compact('pageTitle', 'agents', 'widget'));
    }

    public function kycUnverifiedAgents()
    {
        $pageTitle = 'KYC Unverified Agents';
        extract($this->agentData('kycUnverified'));

        if (request()->export) {
            return $this->callExportData($baseQuery);
        }
        $agents = $baseQuery->paginate(getPaginate());

        return view('admin.agents.list', compact('pageTitle', 'agents', 'widget'));
    }

    public function kycPendingAgents()
    {
        $pageTitle = 'KYC Pending Agents';
        extract($this->agentData('kycPending'));

        if (request()->export) {
            return $this->callExportData($baseQuery);
        }
        $agents = $baseQuery->paginate(getPaginate());

        return view('admin.agents.list', compact('pageTitle', 'agents', 'widget'));
    }



    public function emailVerifiedAgents()
    {
        $pageTitle = 'Email Verified Agents';
        extract($this->agentData('emailVerified'));

        if (request()->export) {
            return $this->callExportData($baseQuery);
        }
        $agents = $baseQuery->paginate(getPaginate());

        return view('admin.agents.list', compact('pageTitle', 'agents', 'widget'));
    }


    public function mobileUnverifiedAgents()
    {
        $pageTitle = 'Mobile Unverified Agents';
        extract($this->agentData('mobileUnverified'));

        if (request()->export) {
            return $this->callExportData($baseQuery);
        }
        $agents = $baseQuery->paginate(getPaginate());

        return view('admin.agents.list', compact('pageTitle', 'agents', 'widget'));
    }

    public function mobileVerifiedAgents()
    {
        $pageTitle = 'Mobile Verified Agents';
        extract($this->agentData('mobileVerified'));

        if (request()->export) {
            return $this->callExportData($baseQuery);
        }
        $agents = $baseQuery->paginate(getPaginate());

        return view('admin.agents.list', compact('pageTitle', 'agents', 'widget'));
    }

    public function agentsWithBalance()
    {
        $pageTitle = 'Agents with Balance';
        extract($this->agentData('withBalance'));

        if (request()->export) {
            return $this->callExportData($baseQuery);
        }
        $agents = $baseQuery->paginate(getPaginate());

        return view('admin.agents.list', compact('pageTitle', 'agents', 'widget'));
    }

    protected function agentData($scope = 'query')
    {
        $baseQuery  = Agent::$scope()->searchable(['email', 'username', 'firstname', 'lastname','mobile'])->dateFilter()->filter(['status'])->orderBy('id', getOrderBy());

        $countQuery = Agent::query();
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
        $agent      = Agent::findOrFail($id);
        $pageTitle = 'Agent Detail - ' . $agent->username;
        $loginLogs = UserLogin::where('agent_id', $agent->id)->take(6)->get();

        $widget['total_deposit']     = Deposit::where('agent_id', $agent->id)->successful()->sum('amount');
        $widget['total_withdraw']    = Withdrawal::where('agent_id', $agent->id)->approved()->sum('amount');
        $widget['total_transaction'] = Transaction::where('agent_id', $agent->id)->sum('amount');
        $countries                   = json_decode(file_get_contents(resource_path('views/partials/country.json')));

        return view('admin.agents.detail', compact('pageTitle', 'agent', 'widget', 'countries', 'loginLogs'));
    }

    public function kycDetails($id)
    {
        $pageTitle = 'KYC Details';
        $agent      = Agent::findOrFail($id);
        return view('admin.agents.kyc_detail', compact('pageTitle', 'agent'));
    }

    public function kycApprove($id)
    {
        $agent     = Agent::findOrFail($id);
        $agent->kv = Status::KYC_VERIFIED;
        $agent->save();

        notify($agent, 'KYC_APPROVE', []);

        $notify[] = ['success', 'KYC approved successfully'];
        return to_route('admin.agents.kyc.pending')->withNotify($notify);
    }

    public function kycReject(Request $request, $id)
    {
        $request->validate([
            'reason' => 'required'
        ]);

        $agent                       = Agent::findOrFail($id);
        $agent->kv                   = Status::KYC_UNVERIFIED;
        $agent->kyc_rejection_reason = $request->reason;
        $agent->save();

        notify($agent, 'KYC_REJECT', [
            'reason' => $request->reason
        ]);

        $notify[] = ['success', 'KYC rejected successfully'];
        return to_route('admin.agents.kyc.pending')->withNotify($notify);
    }


    public function update(Request $request, $id)
    {
        $agent         = Agent::findOrFail($id);
        $countryData  = json_decode(file_get_contents(resource_path('views/partials/country.json')));
        $countryArray = (array)$countryData;
        $countries    = implode(',', array_keys($countryArray));

        $countryCode = $request->country;
        $country     = $countryData->$countryCode->country;
        $dialCode    = $countryData->$countryCode->dial_code;

        $request->validate([
            'firstname' => 'required|string|max:40',
            'lastname'  => 'required|string|max:40',
            'email'     => 'required|email|string|max:40|unique:agents,email,' . $agent->id,
            'mobile'    => 'required|string|max:40',
            'country'   => 'required|in:' . $countries,
        ]);

        $exists = Agent::where('mobile', $request->mobile)->where('dial_code', $dialCode)->where('id', '!=', $agent->id)->exists();

        if ($exists) {
            $notify[] = ['error', 'The mobile number already exists.'];
            return back()->withNotify($notify);
        }

        $agent->mobile    = $request->mobile;
        $agent->firstname = $request->firstname;
        $agent->lastname  = $request->lastname;
        $agent->email     = $request->email;

        $agent->address      = $request->address;
        $agent->city         = $request->city;
        $agent->state        = $request->state;
        $agent->zip          = $request->zip;
        $agent->country_name = @$country;
        $agent->dial_code    = $dialCode;
        $agent->country_code = $countryCode;

        $agent->ev = $request->ev ? Status::VERIFIED : Status::UNVERIFIED;
        $agent->sv = $request->sv ? Status::VERIFIED : Status::UNVERIFIED;
        $agent->ts = $request->ts ? Status::ENABLE : Status::DISABLE;

        if (!$request->kv) {
            $agent->kv = Status::KYC_UNVERIFIED;
            if ($agent->kyc_data) {
                foreach ($agent->kyc_data as $kycData) {
                    if ($kycData->type == 'file') {
                        fileManager()->removeFile(getFilePath('verify') . '/' . $kycData->value);
                    }
                }
            }
            $agent->kyc_data = null;
        } else {
            $agent->kv = Status::KYC_VERIFIED;
        }
        $agent->save();

        $notify[] = ['success', 'Agent details updated successfully'];
        return back()->withNotify($notify);
    }

    public function addSubBalance(Request $request, $id)
    {

        $request->validate([
            'amount' => 'required|numeric|gt:0',
            'act'    => 'required|in:add,sub',
            'remark' => 'required|string|max:255',
        ]);

        $agent   = Agent::findOrFail($id);
        $amount = $request->amount;
        $trx    = getTrx();


        $transaction = new Transaction();

        if ($request->act == 'add') {
            $agent->balance += $amount;

            $transaction->trx_type = '+';
            $transaction->remark   = 'balance_add';

            $notifyTemplate = 'BAL_ADD';
            $message        = 'Balance added successfully';
        } else {
            if ($amount > $agent->balance) {
                $notify[] = ['error', $agent->username . ' doesn\'t have sufficient balance.'];
                return back()->withNotify($notify);
            }

            $agent->balance -= $amount;

            $transaction->trx_type = '-';
            $transaction->remark   = 'balance_subtract';

            $notifyTemplate = 'BAL_SUB';
            $message        = 'Balance subtracted successfully';
        }

        $agent->save();

        $transaction->agent_id      = $agent->id;
        $transaction->amount       = $amount;
        $transaction->post_balance = $agent->balance;
        $transaction->charge       = 0;
        $transaction->trx          = $trx;
        $transaction->details      = $request->remark;
        $transaction->save();
        notify($agent, $notifyTemplate, [
            'trx'          => $trx,
            'amount'       => showAmount($amount, currencyFormat: false),
            'remark'       => $request->remark,
            'post_balance' => showAmount($agent->balance, currencyFormat: false)
        ]);
        $notify[] = ['success', $message];
        return back()->withNotify($notify);
    }

    public function login($id)
    {
        Auth('agent')->loginUsingId($id);
        return to_route('agent.home');
    }

    public function status(Request $request, $id)
    {
        $agent = Agent::findOrFail($id);
        if ($agent->status == Status::AGENT_ACTIVE) {
            $request->validate([
                'reason' => 'required|string|max:255'
            ]);
            $agent->status     = Status::AGENT_BAN;
            $agent->ban_reason = $request->reason;
            $notify[]          = ['success', 'Agent banned successfully'];
        } else {
            $agent->status     = Status::AGENT_ACTIVE;
            $agent->ban_reason = null;
            $notify[]          = ['success', 'Agent unbanned successfully'];
        }
        $agent->save();
        return back()->withNotify($notify);
    }

    public function showNotificationSingleForm($id)
    {
        $agent = Agent::findOrFail($id);
        if (!gs('en') && !gs('sn') && !gs('pn')) {
            $notify[] = ['warning', 'Notification options are disabled currently'];
            return to_route('admin.agents.detail', $agent->id)->withNotify($notify);
        }
        $pageTitle = 'Send Notification to ' . $agent->username;
        return view('admin.agents.notification_single', compact('pageTitle', 'agent'));
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
        return (new UserNotificationSender())->notificationToSingle($request, $id, 'Agent');
    }

    public function showNotificationAllForm()
    {
        if (!gs('en') && !gs('sn') && !gs('pn')) {
            $notify[] = ['warning', 'Notification options are disabled currently'];
            return to_route('admin.dashboard')->withNotify($notify);
        }

        $notifyToAgent = Agent::notifyToAgent();
        $agents        = Agent::active()->count();
        $pageTitle    = 'Notification to Verified Agents';

        if (session()->has('SEND_NOTIFICATION') && !request()->email_sent) {
            session()->forget('SEND_NOTIFICATION');
        }

        return view('admin.agents.notification_all', compact('pageTitle', 'agents', 'notifyToAgent'));
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
            'number_of_top_deposited_agent' => 'required_if:being_sent_to,topDepositedAgents|integer|gte:0',
            'number_of_days'               => 'required_if:being_sent_to,notLoginAgents|integer|gte:0',
            'image'                        => ["nullable", 'image', new FileTypeValidate(['jpg', 'jpeg', 'png'])],
        ], [
            'number_of_days.required_if'               => "Number of days field is required",
            'number_of_top_deposited_agent.required_if' => "Number of top deposited agent field is required",
        ]);

        if (!gs('en') && !gs('sn') && !gs('pn')) {
            $notify[] = ['warning', 'Notification options are disabled currently'];
            return to_route('admin.dashboard')->withNotify($notify);
        }

        return (new UserNotificationSender())->notificationToAll($request, 'Agent');
    }

    public function countBySegment($methodName)
    {
        return Agent::$methodName()->count();
    }

    public function list()
    {
        $query  = Agent::get();
        $agents = $query->searchable(['email', 'username'])->orderBy('id', 'desc')->paginate(getPaginate());

        return response()->json([
            'success' => true,
            'agents'  => $agents,
            'more'    => $agents->hasMorePages()
        ]);
    }

    public function notificationLog($id)
    {
        $agent     = Agent::findOrFail($id);
        $userType  = 'AGENT';
        $pageTitle = 'Notifications Sent to ' . $agent->username;
        $logs      = NotificationLog::where('agent_id', $id)->with('agent')->orderBy('id', 'desc')->paginate(getPaginate());
        return view('admin.reports.notification_history', compact('pageTitle', 'logs', 'agent', 'userType'));
    }



    private function callExportData($baseQuery)
    {
        return exportData($baseQuery, request()->export, "agent", "A4 landscape");
    }
}
