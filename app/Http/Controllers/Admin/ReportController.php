<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\NotificationLog;
use App\Models\Transaction;
use App\Models\UserLogin;
use Illuminate\Http\Request;

class ReportController extends Controller
{
    public function transaction(Request $request)
    {
        $pageTitle = 'All Transaction Logs';
        $baseQuery = Transaction::searchable(['trx', 'user:username', 'agent:username', 'merchant:username'])->filter(['trx_type', 'remark'])->dateFilter()->orderBy('id', getOrderBy());
        if (request()->export) {
            return exportData($baseQuery, request()->export, "Transaction");
        }
        $transactions = $baseQuery->with('user', 'agent', 'merchant')->paginate(getPaginate());
        $userType = "all";
        return view('admin.reports.transactions', compact('pageTitle', 'transactions', 'userType'));
    }

    public function userTransaction(Request $request)
    {
        $pageTitle = 'User Transaction Logs';
        $baseQuery = Transaction::searchable(['trx', 'user:username'])->filter(['trx_type', 'remark'])->dateFilter()->where('user_id', '!=', '0')->orderBy('id', getOrderBy());
        if (request()->export) {
            return exportData($baseQuery, request()->export, "Transaction");
        }
        $transactions = $baseQuery->with('agent')->paginate(getPaginate());
        $userType = "user";
        return view('admin.reports.transactions', compact('pageTitle', 'transactions', 'userType'));
    }

    public function agentTransaction(Request $request)
    {
        $pageTitle = 'Agent Transaction Logs';
        $baseQuery = Transaction::searchable(['trx', 'agent:username'])->filter(['trx_type', 'remark'])->dateFilter()->where('agent_id', '!=', '0')->orderBy('id', getOrderBy());
        if (request()->export) {
            return exportData($baseQuery, request()->export, "Transaction");
        }

        $transactions = $baseQuery->with('agent')->paginate(getPaginate());
        $userType = "agent";
        return view('admin.reports.transactions', compact('pageTitle', 'transactions', 'userType'));
    }

    public function merchantTransaction(Request $request)
    {
        $pageTitle = 'Merchant Transaction Logs';
        $baseQuery = Transaction::searchable(['trx', 'merchant:username'])->filter(['trx_type', 'remark'])->dateFilter()->where('merchant_id', '!=', '0')->orderBy('id', getOrderBy());
        if (request()->export) {
            return exportData($baseQuery, request()->export, "Transaction");
        }
        $transactions = $baseQuery->with('merchant')->paginate(getPaginate());
        $userType = "merchant";
        return view('admin.reports.transactions', compact('pageTitle', 'transactions', 'userType'));
    }

    public function loginHistory(Request $request)
    {
        $pageTitle = 'User Login History';
        $baseQuery = UserLogin::orderBy('id', getOrderBy())->searchable(['user:username', 'agent:username', 'merchant:username'])->filter(['user_type'])->dateFilter();
        if (request()->export) {
            return exportData($baseQuery, request()->export, "UserLogin");
        }

        $loginLogs = $baseQuery->with('user')->paginate(getPaginate());
        return view('admin.reports.logins', compact('pageTitle', 'loginLogs'));
    }

    public function loginIpHistory($ip)
    {
        $pageTitle = 'Login by - ' . $ip;
        $baseQuery = UserLogin::where('user_ip', $ip)->orderBy('id', 'desc');

        if (request()->export) {
            return exportData($baseQuery, request()->export, "UserLogin");
        }

        $loginLogs = $baseQuery->with('user')->paginate(getPaginate());
        return view('admin.reports.logins', compact('pageTitle', 'loginLogs', 'ip'));
    }

    public function notificationHistory(Request $request)
    {
        $pageTitle = 'Notification History';
        $baseQuery = NotificationLog::orderBy('id', 'desc')->searchable(['user:username', 'agent:username', 'merchant:username'])->filter(['user_type'])->dateFilter();
        if (request()->export) {
            return exportData($baseQuery, request()->export, "NotificationLog");
        }
        $logs = $baseQuery->with('user', 'agent', 'merchant')->paginate(getPaginate());
        $userType = "all";
        return view('admin.reports.notification_history', compact('pageTitle', 'logs', 'userType'));
    }

    public function userNotificationHistory(Request $request)
    {
        $pageTitle = 'User Notification History';
        $baseQuery = NotificationLog::orderBy('id', 'desc')->searchable(['user:username'])->where('user_id', '!=', '0')->dateFilter();
        if (request()->export) {
            return exportData($baseQuery, request()->export, "NotificationLog");
        }
        $logs = $baseQuery->with('user')->paginate(getPaginate());
        $userType = "User";
        return view('admin.reports.notification_history', compact('pageTitle', 'logs', 'userType'));
    }

    public function agentNotificationHistory(Request $request)
    {
        $pageTitle = 'Agent Notification History';
        $baseQuery = NotificationLog::orderBy('id', 'desc')->searchable(['agent:username'])->where('agent_id', '!=', '0')->dateFilter();
        if (request()->export) {
            return exportData($baseQuery, request()->export, "NotificationLog");
        }
        $logs = $baseQuery->with('agent')->paginate(getPaginate());
        $userType = "Agent";
        return view('admin.reports.notification_history', compact('pageTitle', 'logs', 'userType'));
    }

    public function merchantNotificationHistory(Request $request)
    {
        $pageTitle = 'Merchant Notification History';
        $baseQuery = NotificationLog::orderBy('id', 'desc')->searchable(['merchant:username'])->where('merchant_id', '!=', '0')->dateFilter();
        if (request()->export) {
            return exportData($baseQuery, request()->export, "NotificationLog");
        }
        $logs = $baseQuery->with('merchant')->paginate(getPaginate());
        $userType = "Merchant";
        return view('admin.reports.notification_history', compact('pageTitle', 'logs', 'userType'));
    }

    public function emailDetails($id)
    {
        $pageTitle = 'Email Details';
        $email     = NotificationLog::findOrFail($id);
        return view('admin.reports.email_details', compact('pageTitle', 'email'));
    }
}
