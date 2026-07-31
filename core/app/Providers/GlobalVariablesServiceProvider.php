<?php

namespace App\Providers;

use App\Constants\Status;
use App\Models\Admin;
use App\Models\AdminNotification;
use App\Models\Agent;
use App\Models\BankTransfer;
use App\Models\Deposit;
use App\Models\EducationFee;
use App\Models\Frontend;
use App\Models\Language;
use App\Models\Merchant;
use App\Models\Microfinance;
use App\Models\ModuleSetting;
use App\Models\SupportTicket;
use App\Models\User;
use App\Models\UtilityBill;
use App\Models\Withdrawal;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\ServiceProvider;

class GlobalVariablesServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        $viewShare['emptyMessage'] = 'No data found';

        view()->composer([
            'admin.partials.topnav',
            "Template::partials.footer",
            "Template::partials.auth_header",
            "Template::partials.auth_sidebar",
            "Template::partials.merchant_sidebar",
            "Template::partials.merchant_header",
            "Template::partials.agent_header",
            "Template::partials.agent_sidebar",
        ], function ($view) {
            $view->with([
                'languages' => Language::get()
            ]);
        });

        view()->composer(["Template::partials.auth_sidebar", "Template::user.dashboard"], function ($view) {
            $view->with([
                'enableModules' => ModuleSetting::where('status', Status::ENABLE)->where('user_type', "USER")->pluck('slug')->toArray()
            ]);
        });

        view()->composer(["Template::agent.dashboard", "Template::partials.agent_sidebar"], function ($view) {
            $view->with([
                'enableModules' => ModuleSetting::where('status', Status::ENABLE)->where('user_type', "AGENT")->pluck('slug')->toArray()
            ]);
        });


        view()->composer(['admin.partials.sidenav', 'admin.partials.topnav'], function ($view) {
            $view->with([
                'menus'                        => json_decode(file_get_contents(resource_path('views/admin/partials/menu.json'))),
                'pendingTicketCount'           => SupportTicket::whereIn('status', [Status::TICKET_OPEN, Status::TICKET_REPLY])->userTicket()->count(),
                'pendingAgentTicketCount'      => SupportTicket::whereIn('status', [Status::TICKET_OPEN, Status::TICKET_REPLY])->agentTicket()->count(),
                'pendingMerchantTicketCount'   => SupportTicket::whereIn('status', [Status::TICKET_OPEN, Status::TICKET_REPLY])->merchantTicket()->count(),
                'permissions'                  => Auth::guard('admin')->user()->getAllPermissions()->pluck('name')->toArray() ?? [],
                'pendingDepositsCount'         => Deposit::pending()->userDeposit()->count(),
                'pendingAgentDepositsCount'    => Deposit::pending()->agentDeposit()->count(),
                'pendingWithdrawCount'         => Withdrawal::pending()->agentWithdraw()->count(),
                'pendingMerchantWithdrawCount' => Withdrawal::pending()->merchantWithdraw()->count(),

                'processingUtilityBillCount' => UtilityBill::processing()->count(),
                'pendingMicrofinanceCount'   => Microfinance::pending()->count(),
                'pendingBankTransferCount'   => BankTransfer::pending()->count(),
                'pendingEducationFeeCount'   => EducationFee::pending()->count()
            ]);
        });

        view()->composer('admin.partials.topnav', function ($view) {
            $view->with([
                'adminNotifications'     => AdminNotification::where('is_read', Status::NO)->with('user')->orderBy('id', 'desc')->take(10)->get(),
                'adminNotificationCount' => AdminNotification::where('is_read', Status::NO)->count(),
                'hasNotification'        => AdminNotification::exists(),
            ]);
        });

        view()->composer(['components.permission_check', 'admin.partials.topnav',], function ($view) {
            $view->with([
                'admin' => Auth::guard('admin')->user()
            ]);
        });

        view()->composer('admin.partials.sidenav', function ($view) {
            $view->with([
                'bannedUsersCount'              => User::banned()->count(),
                'deletedUsersCount'             => User::deletedUser()->count(),
                'emailUnverifiedUsersCount'     => User::emailUnverified()->count(),
                'mobileUnverifiedUsersCount'    => User::mobileUnverified()->count(),
                'kycUnverifiedUsersCount'       => User::kycUnverified()->count(),
                'kycPendingUsersCount'          => User::kycPending()->count(),

                'bannedAgentsCount'             => Agent::banned()->count(),
                'deletedAgentsCount'            => Agent::deletedAgent()->count(),
                'emailUnverifiedAgentsCount'    => Agent::emailUnverified()->count(),
                'mobileUnverifiedAgentsCount'   => Agent::mobileUnverified()->count(),
                'kycUnverifiedAgentsCount'      => Agent::kycUnverified()->count(),
                'kycPendingAgentsCount'         => Agent::kycPending()->count(),

                'bannedMerchantsCount'          => Merchant::banned()->count(),
                'deletedMerchantsCount'         => Merchant::deletedMerchant()->count(),
                'emailUnverifiedMerchantsCount' => Merchant::emailUnverified()->count(),
                'mobileUnverifiedMerchantsCount' => Merchant::mobileUnverified()->count(),
                'kycUnverifiedMerchantsCount'   => Merchant::kycUnverified()->count(),
                'kycPendingMerchantsCount'      => Merchant::kycPending()->count(),

                'bannedAdminsCount'             => Admin::banned()->count(),
                'emailUnverifiedAdminsCount'    => Admin::emailUnverified()->count(),
            ]);
        });

        view()->composer('partials.seo', function ($view) {
            $seo = Frontend::where('data_keys', 'seo.data')->first();
            $view->with([
                'seo' => $seo ? $seo->data_values : $seo,
            ]);
        });

        view()->share($viewShare);
    }
}
