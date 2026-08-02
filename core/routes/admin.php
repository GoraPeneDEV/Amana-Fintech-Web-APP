<?php

use Illuminate\Support\Facades\Route;

Route::namespace('Auth')->group(function () {
    Route::middleware('admin.guest')->group(function () {
        Route::controller('LoginController')->group(function () {
            Route::get('/', 'showLoginForm')->name('login');
            Route::post('/', 'login')->name('login');
            Route::get('logout', 'logout')->middleware('admin')->withoutMiddleware('admin.guest')->name('logout');
        });
        // Admin Password Reset
        Route::controller('ForgotPasswordController')->prefix('password')->name('password.')->group(function () {
            Route::get('reset', 'showLinkRequestForm')->name('reset');
            Route::post('reset', 'sendResetCodeEmail');
            Route::get('code-verify', 'codeVerify')->name('code.verify');
            Route::post('verify-code', 'verifyCode')->name('verify.code');
        });

        Route::controller('ResetPasswordController')->group(function () {
            Route::get('password/reset/{token}', 'showResetForm')->name('password.reset.form');
            Route::post('password/reset/change', 'reset')->name('password.change');
        });
    });
});

Route::middleware('admin')->group(function () {
    // Users Management
    Route::controller('ManageUsersController')->name('users.')->prefix('users')->group(function () {
        Route::middleware('permission:view users,admin')->group(function () {
            Route::get('/', 'allUsers')->name('all');
            Route::get('active', 'activeUsers')->name('active');
            Route::get('banned', 'bannedUsers')->name('banned');
            Route::get('deleted', 'deletedUsers')->name('deleted');
            Route::get('email-verified', 'emailVerifiedUsers')->name('email.verified');
            Route::get('email-unverified', 'emailUnverifiedUsers')->name('email.unverified');
            Route::get('mobile-unverified', 'mobileUnverifiedUsers')->name('mobile.unverified');
            Route::get('kyc-unverified', 'kycUnverifiedUsers')->name('kyc.unverified');
            Route::get('kyc-pending', 'kycPendingUsers')->name('kyc.pending');
            Route::get('mobile-verified', 'mobileVerifiedUsers')->name('mobile.verified');
            Route::get('with-balance', 'usersWithBalance')->name('with.balance');
            Route::get('detail/{id}', 'detail')->name('detail');
            Route::get('list', 'list')->name('list');
        });

        Route::middleware(['permission:update user,admin'])->group(function () {
            Route::get('kyc-data/{id}', 'kycDetails')->name('kyc.details');
            Route::post('kyc-approve/{id}', 'kycApprove')->name('kyc.approve');
            Route::post('kyc-reject/{id}', 'kycReject')->name('kyc.reject');
            Route::post('update/{id}', 'update')->name('update');
        });

        Route::post('add-sub-balance/{id}', 'addSubBalance')->name('add.sub.balance')->middleware(['permission:update user balance,admin']);

        Route::middleware(['permission:send user notification,admin'])->group(function () {
            Route::get('send-notification/{id}', 'showNotificationSingleForm')->name('notification.single');
            Route::post('send-notification/{id}', 'sendNotificationSingle')->name('notification.single');
            Route::get('send-notification', 'showNotificationAllForm')->name('notification.all');
            Route::post('send-notification', 'sendNotificationAll')->name('notification.all.send');
        });

        Route::get('login/{id}', 'login')->name('login')->middleware('permission:login as user,admin');
        Route::post('status/{id}', 'status')->name('status')->middleware('permission:ban user,admin');

        Route::get('count-by-segment/{methodName}', 'countBySegment')->name('segment.count');
        Route::get('notification-log/{id}', 'notificationLog')->name('notification.log')->middleware('permission:view user notifications,admin');
    });

    // Agents Management
    Route::controller('ManageAgentsController')->name('agents.')->prefix('agents')->group(function () {
        Route::middleware('permission:view agents,admin')->group(function () {
            Route::get('/', 'allAgents')->name('all');
            Route::get('active', 'activeAgents')->name('active');
            Route::get('banned', 'bannedAgents')->name('banned');
            Route::get('deleted', 'deletedAgents')->name('deleted');
            Route::get('email-verified', 'emailVerifiedAgents')->name('email.verified');
            Route::get('email-unverified', 'emailUnverifiedAgents')->name('email.unverified');
            Route::get('mobile-unverified', 'mobileUnverifiedAgents')->name('mobile.unverified');
            Route::get('kyc-pending', 'kycPendingAgents')->name('kyc.pending');
            Route::get('kyc-unverified', 'kycUnverifiedAgents')->name('kyc.unverified');
            Route::get('mobile-verified', 'mobileVerifiedAgents')->name('mobile.verified');
            Route::get('with-balance', 'agentsWithBalance')->name('with.balance');
            Route::get('list', 'list')->name('list');
            Route::get('detail/{id}', 'detail')->name('detail');
        });

        Route::middleware('permission:verify agent kyc,admin')->group(function () {
            Route::post('agent-data-reject/{id}', 'dataReject')->name('data.reject');
            Route::get('kyc-data/{id}', 'kycDetails')->name('kyc.details');
            Route::post('kyc-approve/{id}', 'kycApprove')->name('kyc.approve');
            Route::post('kyc-reject/{id}', 'kycReject')->name('kyc.reject');
        });

        Route::post('update/{id}', 'update')->name('update')->middleware('permission:update agent,admin');
        Route::post('add-sub-balance/{id}', 'addSubBalance')->name('add.sub.balance')->middleware('permission:update agent balance,admin');
        Route::get('login/{id}', 'login')->name('login')->middleware('permission:login as agent,admin');
        Route::post('status/{id}', 'status')->name('status')->middleware('permission:ban agent,admin');

        Route::middleware('permission:send agent notification,admin')->group(function () {
            Route::get('send-notification/{id}', 'showNotificationSingleForm')->name('notification.single');
            Route::post('send-notification/{id}', 'sendNotificationSingle')->name('notification.single');
            Route::get('send-notification', 'showNotificationAllForm')->name('notification.all');
            Route::post('send-notification', 'sendNotificationAll')->name('notification.all.send');
        });

        Route::get('count-by-segment/{methodName}', 'countBySegment')->name('segment.count');
        Route::get('notification-log/{id}', 'notificationLog')->name('notification.log')->middleware('permission:view agent notifications,admin');
    });

    // Merchants Management
    Route::controller('ManageMerchantController')->name('merchants.')->prefix('merchants')->group(function () {
        Route::middleware('permission:view merchants,admin')->group(function () {
            Route::get('/', 'allMerchants')->name('all');
            Route::get('active', 'activeMerchants')->name('active');
            Route::get('banned', 'bannedMerchants')->name('banned');
            Route::get('deleted', 'deletedMerchants')->name('deleted');
            Route::get('email-verified', 'emailVerifiedMerchants')->name('email.verified');
            Route::get('email-unverified', 'emailUnverifiedMerchants')->name('email.unverified');
            Route::get('mobile-unverified', 'mobileUnverifiedMerchants')->name('mobile.unverified');
            Route::get('kyc-unverified', 'kycUnverifiedMerchants')->name('kyc.unverified');
            Route::get('kyc-pending', 'kycPendingMerchants')->name('kyc.pending');
            Route::get('mobile-verified', 'mobileVerifiedMerchants')->name('mobile.verified');
            Route::get('with-balance', 'merchantsWithBalance')->name('with.balance');
            Route::get('detail/{id}', 'detail')->name('detail');
            Route::get('list', 'list')->name('list');
        });

        Route::middleware('permission:verify merchant kyc,admin')->group(function () {
            Route::get('kyc-data/{id}', 'kycDetails')->name('kyc.details');
            Route::post('kyc-approve/{id}', 'kycApprove')->name('kyc.approve');
            Route::post('kyc-reject/{id}', 'kycReject')->name('kyc.reject');
        });

        Route::post('update/{id}', 'update')->name('update')->middleware('permission:update merchant,admin');
        Route::post('status/{id}', 'status')->name('status')->middleware('permission:ban merchant,admin');

        Route::post('add-sub-balance/{id}', 'addSubBalance')->name('add.sub.balance')->middleware('permission:update merchant balance,admin');
        Route::get('login/{id}', 'login')->name('login')->middleware('permission:login as merchant,admin');

        Route::middleware('permission:send merchant notification,admin')->group(function () {
            Route::post('send-notification', 'sendNotificationAll')->name('notification.all.send')->middleware('permission:send merchant notification,admin');
            Route::get('send-notification/{id}', 'showNotificationSingleForm')->name('notification.single')->middleware('permission:send merchant notification,admin');
            Route::post('send-notification/{id}', 'sendNotificationSingle')->name('notification.single')->middleware('permission:send merchant notification,admin');
        });

        Route::get('send-notification', 'showNotificationAllForm')->name('notification.all')->middleware('permission:show merchant notifications,admin');
        Route::get('notification-log/{id}', 'notificationLog')->name('notification.log')->middleware('permission:view merchant notifications,admin');
        Route::get('count-by-segment/{methodName}', 'countBySegment')->name('segment.count');
    });

    // role & permission
    Route::controller('RoleController')->name('role.')->prefix('role')->group(function () {
        Route::get('list', 'list')->name('list')->middleware('permission:view roles,admin');
        Route::post('create', 'save')->name('create')->middleware('permission:add role,admin');
        Route::post('update/{id}', 'save')->name('update')->middleware('permission:edit role,admin');
        Route::get('permission/{id}', 'permission')->name('permission')->middleware('permission:assign permissions,admin');
        Route::post('permission/update/{id}', 'permissionUpdate')->name('permission.update')->middleware('permission:assign permissions,admin');
    });

    //donation
    Route::controller('DonationController')->prefix('donation')->group(function () {
        Route::prefix('charity')->name('donation.charity.')->middleware('permission:manage charity,admin')->group(function () {
            Route::get('all', 'all')->name('all');
            Route::post('save/{id?}', 'save')->name('save');
            Route::post('status/{id}', 'status')->name('status');
        });
        Route::get('/', 'donation')->name("donation.all")->middleware('permission:view donation,admin');
    });

    Route::prefix('setting')->name('setting.')->group(function () {
        //Manage Mobile Operator
        Route::controller('MobileOperatorController')->middleware('permission:manage mobile operator,admin')->name('mobile.operator.')->prefix('mobile/operator')->group(function () {
            Route::get('all', 'all')->name('all');
            Route::post('save/{id?}', 'save')->name('save');
            Route::post('status/{id}', 'status')->name('status');
        });

        //Manage Microfinance
        Route::controller('NgoController')->name('microfinance.')->prefix('microfinance')->group(function () {
            Route::middleware('permission:manage microfinance ngo,admin')->group(function () {
                Route::get('all', 'all')->name('all');
                Route::post('save/{id?}', 'save')->name('save');
                Route::get('configure/{id}', 'configure')->name('configure');
                Route::post('configure/{id}', 'saveConfigure');
                Route::post('status/{id}', 'status')->name('status');
            });
        });

        //Manage Bank Transfer
        Route::controller('BankController')->middleware('permission:manage bank transfer bank,admin')->name('bank.transfer.')->prefix('bank/transfer')->group(function () {
            Route::get('all', 'all')->name('all');
            Route::post('save/{id?}', 'save')->name('save');
            Route::get('configure/{id}', 'configure')->name('configure');
            Route::post('configure/{id}', 'saveConfigure');
            Route::post('status/{id}', 'status')->name('status');
        });
    });

    //Manage promotional banner
    Route::controller('BannerController')->middleware('permission:manage banners,admin')->name('banner.')->prefix('banner')->group(function () {
        Route::get('all', 'all')->name('all');
        Route::post('save/{id?}', 'save')->name('save');
        Route::post('status/{id}', 'status')->name('status');
    });

    // Offers
    Route::controller('ManageOffersController')->middleware('permission:manage offers,admin')->prefix('promotion')->name('promotion.')->group(function () {
        Route::get('offers', 'index')->name('offer.index');
        Route::get('offer/create', 'create')->name('offer.create');
        Route::get('offer/edit/{id}', 'edit')->name('offer.edit');
        Route::post('offer/save/{id}', 'save')->name('offer.store');
        Route::post('offer-status/{id}', 'status')->name('offer.status');
    });

    //Utility bill
    Route::controller('UtilityBillController')->name('utility.bill.')->prefix('utility/bill')->group(function () {
        Route::middleware('permission:view utility bill,admin')->group(function () {
            Route::get('all', 'all')->name('all');
            Route::get('pending', 'pending')->name('pending');
            Route::get('processing', 'processing')->name('processing');
            Route::get('approved', 'approved')->name('approved');
            Route::get('refunded', 'refunded')->name('refunded');
        });
        Route::post('approve/{id}', 'approve')->name('approve')->middleware('permission:approve utility bill,admin');
        Route::post('reject/{id}', 'reject')->name('reject')->middleware('permission:reject utility bill,admin');
        Route::get('charge-setting', 'chargeSetting')->name('charge.setting')->middleware('permission:manage utility bill charge,admin');
        Route::post('charge-setting', 'updateCharges')->name('charge.setting.update')->middleware('permission:manage utility bill charge,admin');
    });

    //Manage Utility Bill Category
    Route::controller('BillCategoryController')->middleware('permission:manage bill category,admin')->name('bill.category.')->prefix('bill/category')->group(function () {
        Route::get('/', 'all')->name('all');
        Route::get('import', 'import')->name('import');
        Route::post('save/{id?}', 'save')->name('save');
        Route::post('status/{id}', 'status')->name('status');
    });
    //Manage Utility Bill
    Route::controller('CompanyController')->middleware('permission:manage bill company,admin')->name('utility.bill.company.')->prefix('utility/bill/company')->group(function () {

        Route::get('all/{countryId}', 'all')->name('all');
        Route::post('save/{id}', 'save')->name('save');
        Route::post('status/{id}', 'status')->name('status');

        Route::get('fetch-companies/{countryId}', 'fetchCompanies')->name('fetch.companies');
        Route::post('save-companies/{countryId}', 'saveCompanies')->name('companies.save');

        Route::get('countries', 'countries')->name('countries');
        Route::get('fetch-countries', 'fetchCountries')->name('fetch.countries');
        Route::post('save-countries', 'saveCountries')->name('countries.save');
        Route::post('update-country-status/{id}', 'updateCountryStatus')->name('country.status');
    });

    //Microfinance
    Route::controller('MicrofinanceController')->name('microfinance.')->prefix('microfinance')->group(function () {
        Route::middleware('permission:view microfinance,admin')->group(function () {
            Route::get('all', 'all')->name('all');
            Route::get('pending', 'pending')->name('pending');
            Route::get('approved', 'approved')->name('approved');
            Route::get('rejected', 'rejected')->name('rejected');
        });
        Route::post('approve/{id}', 'approve')->name('approve')->middleware('permission:approve microfinance,admin');
        Route::post('reject/{id}', 'reject')->name('reject')->middleware('permission:reject microfinance,admin');
        Route::get('charge-setting', 'chargeSetting')->name('charge.setting')->middleware('permission:manage microfinance charge,admin');
        Route::post('charge-setting', 'updateCharges')->name('charge.setting.update')->middleware('permission:manage microfinance charge,admin');
    });

    Route::controller('EducationFeeController')->name('education.fee.')->prefix('education/fee')->group(function () {
        Route::middleware('permission:view education fee,admin')->group(function () {
            Route::get('all', 'all')->name('all');
            Route::get('pending', 'pending')->name('pending');
            Route::get('approved', 'approved')->name('approved');
            Route::get('rejected', 'rejected')->name('rejected');
        });

        Route::post('approve/{id}', 'approve')->name('approve')->middleware('permission:approve education fee,admin');
        Route::post('reject/{id}', 'reject')->name('reject')->middleware('permission:reject education fee,admin');
        Route::get('charge-setting', 'chargeSetting')->name('charge.setting')->middleware('permission:manage education fee charge,admin');
        Route::post('charge-setting', 'updateCharges')->name('charge.setting.update')->middleware('permission:manage education fee charge,admin');
    });

    //Manage Education Fee
    Route::controller('InstitutionController')->middleware('permission:manage institution,admin')->name('education.institute.')->prefix('education/institute')->group(function () {
        Route::get('all', 'all')->name('all');
        Route::post('save/{id?}', 'save')->name('save');
        Route::get('configure/{id}', 'configure')->name('configure');
        Route::post('configure/{id}', 'saveConfigure');
        Route::post('status/{id}', 'status')->name('status');
    });

    //Manage Education Category
    Route::controller('CategoryController')->middleware('permission:manage institution category,admin')->name('education.category.')->prefix('education/category')->group(function () {
        Route::get('all', 'all')->name('all');
        Route::post('save/{id?}', 'save')->name('save');
        Route::post('status/{id}', 'status')->name('status');
    });

    //Bank transfer
    Route::controller('BankTransferController')->name('bank.transfer.')->prefix('bank/transfer')->group(function () {
        Route::middleware('permission:view bank transfer,admin')->group(function () {
            Route::get('all', 'all')->name('all');
            Route::get('pending', 'pending')->name('pending');
            Route::get('approved', 'approved')->name('approved');
            Route::get('rejected', 'rejected')->name('rejected');
        });
        Route::post('approve/{id}', 'approve')->name('approve')->middleware('permission:approve bank transfer,admin');
        Route::post('reject/{id}', 'reject')->name('reject')->middleware('permission:reject bank transfer,admin');
        Route::get('charge-setting', 'chargeSetting')->name('charge.setting')->middleware('permission:manage bank transfer charge,admin');
        Route::post('charge-setting', 'updateCharges')->name('charge.setting.update')->middleware('permission:manage bank transfer charge,admin');
    });

    //Investment
    Route::prefix('investment')->name('investment.')->group(function () {
        Route::controller('InvestmentHistoryController')->group(function () {
            Route::get('history', 'all')->name('history')->middleware('permission:view investment,admin');
            Route::get('details/{id}', 'details')->name('details');
        });

        Route::controller('InvestmentInterestController')->prefix('interest')->name('interest.')->group(function () {
            Route::get('history', 'all')->name('history')->middleware('permission:view investment,admin');
        });

        Route::controller('InvestmentPlanController')->prefix('plan')->name('plan.')->group(function () {
            Route::get('/', 'all')->name('index')->middleware('permission:manage investment plan,admin');
            Route::post('store', 'store')->name('store');
            Route::post('update/{id}', 'update')->name('update');
            Route::post('status/{id}', 'status')->name('status');
        });

        Route::controller('InvestmentTimeController')->prefix('time')->name('time.')->group(function () {
            Route::get('/', 'all')->name('index')->middleware('permission:manage investment time,admin');
            Route::post('store', 'store')->name('store');
            Route::post('update/{id}', 'update')->name('update');
            Route::post('status/{id}', 'status')->name('status');
        });
    });

    //Send Money
    Route::controller('SendMoneyController')->name('send.money.')->prefix('send-money')->group(function () {
        Route::get('/', 'history')->name('history')->middleware('permission:view send money,admin')->middleware('permission:view send money,admin');
        Route::get('charge-setting', 'chargeSetting')->name('charge.setting')->middleware('permission:manage send money charge,admin');
        Route::post('charge-setting', 'updateCharges')->name('charge.setting.update')->middleware('permission:manage send money charge,admin');
    });

    //cashout
    Route::controller('CashOutController')->name('cashout.')->prefix('cashout')->group(function () {
        Route::get('/', 'history')->name('history')->middleware('permission:view cash out,admin')->middleware('permission:view cash out,admin');
        Route::get('charge-setting', 'chargeSetting')->name('charge.setting')->middleware('permission:manage cash out charge,admin');
        Route::post('charge-setting', 'updateCharges')->name('charge.setting.update')->middleware('permission:manage cash out charge,admin');
    });

    //cashin
    Route::controller('CashInController')->name('cashin.')->prefix('cashin')->group(function () {
        Route::get('/', 'history')->name('history')->middleware('permission:view cash in,admin');
        Route::get('charge-setting', 'chargeSetting')->name('charge.setting')->middleware('permission:manage cash in charge,admin');
        Route::post('charge-setting', 'updateCharges')->name('charge.setting.update')->middleware('permission:manage cash in charge,admin');;
    });

    //payment
    Route::controller('PaymentController')->name('payment.')->prefix('payment')->group(function () {
        Route::get('/', 'history')->name('history')->middleware('permission:view payment,admin');
        Route::get('charge-setting', 'chargeSetting')->name('charge.setting')->middleware('permission:manage payment charge,admin');
        Route::post('charge-setting', 'updateCharges')->name('charge.setting.update')->middleware('permission:manage payment charge,admin');
    });

    // Subscriber
    Route::controller('SubscriberController')->middleware('permission:manage subscribers,admin')->prefix('subscriber')->name('subscriber.')->group(function () {
        Route::get('/', 'index')->name('index');
        Route::get('send-email', 'sendEmailForm')->name('send.email');
        Route::post('remove/{id}', 'remove')->name('remove');
        Route::post('send-email', 'sendEmail')->name('send.email');
    });

    //Module setting
    Route::controller('ModuleSettingController')->middleware('permission:module settings,admin')->group(function () {
        Route::get('module-setting', 'index')->name('module.setting');
        Route::get('module-setting/update/{id}', 'update')->name('module.update');
    });

    //API setting
    Route::controller('ApiProviderSettingController')->middleware('permission:module settings,admin')->group(function () {
        Route::get('api-setting/', 'providerConfiguration')->name('api.setting')->middleware('permission:module settings,admin');
        Route::post('api-setting/{id}', 'providerConfigurationUpdate')->name('api.setting.update')->middleware('permission:module settings,admin');
    });

    // Report
    Route::controller('ReportController')->prefix('report')->name('report.')->group(function () {
        Route::get('transaction/', 'transaction')->name('transaction')->middleware('permission:view all transactions,admin');
        Route::get('user/transaction/', 'userTransaction')->name('user.transaction')->middleware('permission:view user transactions,admin');
        Route::get('agent/transaction/', 'agentTransaction')->name('agent.transaction')->middleware('permission:view agent transactions,admin');
        Route::get('merchant/transaction/', 'merchantTransaction')->name('merchant.transaction')->middleware('permission:view merchant transactions,admin');
        Route::get('login/history', 'loginHistory')->name('login.history')->middleware('permission:view login history,admin');
        Route::get('login/ipHistory/{ip}', 'loginIpHistory')->name('login.ipHistory')->middleware('permission:view login history,admin');
        Route::get('email/detail/{id}', 'emailDetails')->name('email.details');

        Route::get('notification/history', 'notificationHistory')->name('notification.history')->middleware('permission:view all notifications,admin');
        Route::get('user/notification/history', 'userNotificationHistory')->name('user.notification.history')->middleware('permission:view user notifications,admin');
        Route::get('agent/notification/history', 'agentNotificationHistory')->name('agent.notification.history')->middleware('permission:view agent notifications,admin');
        Route::get('merchant/notification/history', 'merchantNotificationHistory')->name('merchant.notification.history')->middleware('permission:view merchant notifications,admin');
    });

    // Support
    Route::controller('SupportTicketController')->prefix('ticket')->name('ticket.')->group(function () {
        Route::middleware('permission:view user tickets,admin')->group(function () {
            Route::get('/', 'tickets')->name('user.index');
            Route::get('pending', 'pendingTicket')->name('user.pending');
            Route::get('closed', 'closedTicket')->name('user.closed');
            Route::get('answered', 'answeredTicket')->name('user.answered');
        });
        Route::middleware('permission:answer tickets,admin')->group(function () {
            Route::get('view/{id}', 'ticketReply')->name('view');
            Route::post('reply/{id}', 'replyTicket')->name('reply');
            Route::get('download/{attachment_id}', 'ticketDownload')->name('download');
        });
        Route::post('close/{id}', 'closeTicket')->name('close')->middleware('permission:close tickets,admin');
        Route::post('delete/{id}', 'ticketDelete')->name('delete')->middleware('permission:close tickets,admin');
    });

    // Agent Support
    Route::controller('AgentSupportTicketController')->middleware('permission:view agent tickets,admin')->prefix('ticket/agent')->name('ticket.agent.')->group(function () {
        Route::get('/', 'tickets')->name('index');
        Route::get('pending', 'pendingTicket')->name('pending');
        Route::get('closed', 'closedTicket')->name('closed');
        Route::get('answered', 'answeredTicket')->name('answered');
    });

    // Merchant Support
    Route::controller('MerchantSupportTicketController')->middleware('permission:view merchant tickets,admin')->prefix('ticket/merchant')->name('ticket.merchant.')->group(function () {
        Route::get('/', 'tickets')->name('index');
        Route::get('pending', 'pendingTicket')->name('pending');
        Route::get('closed', 'closedTicket')->name('closed');
        Route::get('answered', 'answeredTicket')->name('answered');
    });

    Route::controller('AdminController')->group(function () {
        Route::get('dashboard', 'dashboard')->name('dashboard')->middleware('permission:view dashboard,admin');
        Route::get('chart/deposit-withdraw', 'depositAndWithdrawReport')->name('chart.deposit.withdraw');
        Route::get('chart/transaction', 'transactionReport')->name('chart.transaction');
        Route::get('profile', 'profile')->name('profile');
        Route::post('profile', 'profileUpdate')->name('profile.update');
        Route::get('password', 'password')->name('password');
        Route::post('password', 'passwordUpdate')->name('password.update');

        //Notification
        Route::middleware('permission:view all notifications,admin')->group(function () {
            Route::get('notifications', 'notifications')->name('notifications')->middleware('permission:view all notifications,admin');
            Route::get('notification/read/{id}', 'notificationRead')->name('notification.read');
            Route::get('notifications/read-all', 'readAllNotification')->name('notifications.read.all');
            Route::post('notifications/delete-all', 'deleteAllNotification')->name('notifications.delete.all');
            Route::post('notifications/delete-single/{id}', 'deleteSingleNotification')->name('notifications.delete.single');
        });
        Route::get('download-attachments/{file_hash}', 'downloadAttachment')->name('download.attachment');

        // Assign role
        Route::get('list', 'list')->name('list')->middleware('permission:view admin,admin');
        Route::post('store', 'save')->name('store')->middleware('permission:add admin,admin');
        Route::post('update/{id}', 'save')->name('update')->middleware('permission:edit admin,admin');
        Route::post('status-change/{id}', 'status')->name('status.change')->middleware('permission:edit admin,admin');
    });

    // extensions
    Route::controller('ExtensionController')->middleware('permission:manage extensions,admin')->prefix('extensions')->name('extensions.')->group(function () {
        Route::get('/', 'index')->name('index');
        Route::post('update/{id}', 'update')->name('update');
        Route::post('status/{id}', 'status')->name('status');
    });

    // operating country
    Route::controller('OperatingCountryController')->middleware("permission:country settings,admin")->prefix('operating-country')->name('operating.country.')->group(function () {
        Route::get('/', 'index')->name('index');
        Route::post('save/{id?}', 'save')->name('save');
        Route::post('status/{id}', 'status')->name('status');
    });

    // Language Manager
    Route::controller('LanguageController')->middleware('permission:manage languages,admin')->prefix('language')->name('language.')->group(function () {
        Route::get('/', 'langManage')->name('manage');
        Route::post('/', 'langStore')->name('manage.store');
        Route::post('delete/{id}', 'langDelete')->name('manage.delete');
        Route::post('update/{id}', 'langUpdate')->name('manage.update');
        Route::get('edit/{id}', 'langEdit')->name('key');
        Route::post('import', 'langImport')->name('import.lang');
        Route::post('store/key/{id}', 'storeLanguageJson')->name('store.key');
        Route::post('delete/key/{id}/{key}', 'deleteLanguageJson')->name('delete.key');
        Route::post('update/key/{id}', 'updateLanguageJson')->name('update.key');
        Route::get('get-keys', 'getKeys')->name('get.key');
    });

    // Airtime
    Route::controller('AirtimeController')->name('airtime.')->prefix('mobile-recharge')->group(function () {
        Route::middleware('permission:manage airtime operator,admin')->group(function () {

            Route::get('countries', 'countries')->name('countries');
            Route::get('fetch-countries', 'fetchCountries')->name('fetch.countries');
            Route::post('save-countries', 'saveCountries')->name('countries.save');
            Route::post('update-country-status/{id}', 'updateCountryStatus')->name('country.status');

            Route::get('operators/{iso?}', 'operators')->name('operators');
            Route::get('fetch-operators/{iso}', 'fetchOperatorsByISO')->name('fetch.operators');
            Route::post('save-operators/{iso}', 'saveOperators')->name('operators.save');
            Route::post('update-operator-status/{id}', 'updateOperatorStatus')->name('operator.status');
        });
        Route::get('history', 'history')->name('history')->middleware('permission:view airtime,admin');
    });

    // Gift Card
    Route::controller('GiftCardController')->name('gift.card.')->prefix('gift-card')->group(function () {
        Route::get('category', 'allCategory')->name('category.all')->middleware('permission:manage gift card category,admin');
        Route::get('fetch-categories', 'fetchCategories')->name('fetch.categories')->middleware('permission:manage gift card category,admin');
        Route::post('save-categories', 'saveCategories')->name('categories.save')->middleware('permission:manage gift card category,admin');
        Route::post('update-category-status/{id}', 'updateCategoryStatus')->name('category.status')->middleware('permission:manage gift card category,admin');
        Route::post('edit-category/{id}', 'editCategory')->name('edit.category')->middleware('permission:manage gift card category,admin');

        Route::get('countries', 'countries')->name('countries')->middleware('permission:manage gift card category,admin');
        Route::get('fetch-countries', 'fetchCountries')->name('fetch.countries')->middleware('permission:manage gift card category,admin');
        Route::post('save-countries', 'saveCountries')->name('countries.save')->middleware('permission:manage gift card category,admin');
        Route::post('update-country-status/{id}', 'updateCountryStatus')->name('country.status')->middleware('permission:manage gift card category,admin');

        Route::get('all/products/{countryId}', 'allProducts')->name('products.all')->middleware('permission:manage gift card,admin');
        Route::get('fetch/products/{countryId}', 'fetchProducts')->name('products.fetch')->middleware('permission:manage gift card,admin');
        Route::post('store/products/{countryId}', 'saveProducts')->name('products.store')->middleware('permission:manage gift card,admin');
        Route::post('update-product-status/{id}', 'updateProductStatus')->name('product.status')->middleware('permission:manage gift card,admin');

        Route::get('/', 'history')->name('history')->middleware('permission:view gift card purchases,admin');
    });

    //Notification Setting
    Route::name('setting.notification.')->controller('NotificationController')->middleware('permission:notification settings,admin')->prefix('notification')->group(function () {
        //Template Setting
        Route::get('global/email', 'globalEmail')->name('global.email');
        Route::post('global/email/update', 'globalEmailUpdate')->name('global.email.update');

        Route::get('global/sms', 'globalSms')->name('global.sms');
        Route::post('global/sms/update', 'globalSmsUpdate')->name('global.sms.update');

        Route::get('global/push', 'globalPush')->name('global.push');
        Route::post('global/push/update', 'globalPushUpdate')->name('global.push.update');

        Route::get('templates', 'templates')->name('templates');
        Route::get('template/edit/{type}/{id}', 'templateEdit')->name('template.edit');
        Route::post('template/update/{type}/{id}', 'templateUpdate')->name('template.update');

        //Email Setting
        Route::get('email/setting', 'emailSetting')->name('email');
        Route::post('email/setting', 'emailSettingUpdate');
        Route::post('email/test', 'emailTest')->name('email.test');

        //SMS Setting
        Route::get('sms/setting', 'smsSetting')->name('sms');
        Route::post('sms/setting', 'smsSettingUpdate');
        Route::post('sms/test', 'smsTest')->name('sms.test');

        Route::get('notification/push/setting', 'pushSetting')->name('push');
        Route::post('notification/push/setting', 'pushSettingUpdate');
        Route::post('notification/push/setting/upload', 'pushSettingUpload')->name('push.upload');
        Route::get('notification/push/setting/download', 'pushSettingDownload')->name('push.download');
    });

    //KYC setting
    Route::controller('KycController')->middleware('permission:security settings,admin')->prefix('kyc')->group(function () {
        Route::get('kyc-type', 'kycType')->name('kyc.type');
        Route::get('kyc-setting/{type?}', 'setting')->name('kyc.setting');
        Route::post('kyc-setting/{type}', 'settingUpdate')->name('kyc.setting.update');
    });

    // User Deposit
    Route::controller('DepositController')->prefix('deposit')->name('deposit.')->group(function () {
        Route::middleware('permission:view user add money,admin')->group(function () {
            Route::get('all', 'deposit')->name('list');
            Route::get('pending', 'pending')->name('pending');
            Route::get('rejected', 'rejected')->name('rejected');
            Route::get('approved', 'approved')->name('approved');
            Route::get('successful', 'successful')->name('successful');
            Route::get('initiated', 'initiated')->name('initiated');
            Route::get('details/{id}', 'details')->name('details');
        });
        Route::post('reject', 'reject')->name('reject')->middleware('permission:reject user add money,admin');
        Route::post('approve/{id}', 'approve')->name('approve')->middleware('permission:approve user add money,admin');
    });

    // Agent Deposit
    Route::controller('AgentDepositController')->prefix('agent/deposit')->name('agent.deposit.')->group(function () {
        Route::middleware('permission:view agent add money,admin')->group(function () {
            Route::get('all', 'deposit')->name('list');
            Route::get('pending', 'pending')->name('pending');
            Route::get('rejected', 'rejected')->name('rejected');
            Route::get('approved', 'approved')->name('approved');
            Route::get('successful', 'successful')->name('successful');
            Route::get('initiated', 'initiated')->name('initiated');
            Route::get('details/{id}', 'details')->name('details');
        });
        Route::post('reject', 'reject')->name('reject')->middleware('permission:reject agent add money,admin');
        Route::post('approve/{id}', 'approve')->name('approve')->middleware('permission:approve agent add money,admin');
    });

    // WITHDRAW SYSTEM
    Route::name('withdraw.')->prefix('withdraw')->group(function () {
        Route::controller('WithdrawalController')->name('data.')->group(function () {
            Route::middleware('permission:view agent withdraw,admin')->group(function () {
                Route::get('pending/{user_id?}', 'pending')->name('pending');
                Route::get('approved/{user_id?}', 'approved')->name('approved');
                Route::get('rejected/{user_id?}', 'rejected')->name('rejected');
                Route::get('all/{user_id?}', 'all')->name('all');
                Route::get('details/{id}', 'details')->name('details');
            });
            Route::post('approve', 'approve')->name('approve')->middleware('permission:approve agent withdraw,admin');
            Route::post('reject', 'reject')->name('reject')->middleware('permission:reject agent withdraw,admin');
        });

        Route::controller('MerchantWithdrawalController')->name('merchant.data.')->group(function () {
            Route::middleware('permission:view merchant withdraw,admin')->group(function () {
                Route::get('merchant/pending/{user_id?}', 'pending')->name('pending');
                Route::get('merchant/approved/{user_id?}', 'approved')->name('approved');
                Route::get('merchant/rejected/{user_id?}', 'rejected')->name('rejected');
                Route::get('merchant/all/{user_id?}', 'all')->name('all');
                Route::get('merchant/details/{id}', 'details')->name('details');
            });
            Route::post('merchant/approve', 'approve')->name('approve')->middleware('permission:approve merchant withdraw,admin');
            Route::post('merchant/reject', 'reject')->name('reject')->middleware('permission:reject merchant withdraw,admin');
        });

        // Withdraw Method
        Route::controller('WithdrawMethodController')->middleware('permission:manage withdraw methods,admin')->prefix('method')->name('method.')->group(function () {
            Route::get('/', 'methods')->name('index');
            Route::get('create', 'create')->name('create');
            Route::post('create', 'store')->name('store');
            Route::get('edit/{id}', 'edit')->name('edit');
            Route::post('edit/{id}', 'update')->name('update');
            Route::post('status/{id}', 'status')->name('status');
        });
    });

    // SEO
    Route::get('seo', 'FrontendController@seoEdit')->name('seo')->middleware('permission:seo settings,admin');

    // Frontend
    Route::name('frontend.')->prefix('frontend')->group(function () {

        Route::controller('FrontendController')->middleware('permission:manage sections,admin')->group(function () {
            Route::get('index', 'index')->name('index');
            Route::get('templates', 'templates')->name('templates');
            Route::post('templates', 'templatesActive')->name('templates.active');
            Route::get('frontend-sections/{key?}', 'frontendSections')->name('sections');
            Route::post('frontend-content/{key}', 'frontendContent')->name('sections.content');
            Route::get('frontend-element/{key}/{id?}', 'frontendElement')->name('sections.element');
            Route::get('frontend-slug-check/{key}/{id?}', 'frontendElementSlugCheck')->name('sections.element.slug.check');
            Route::get('frontend-element-seo/{key}/{id}', 'frontendSeo')->name('sections.element.seo');
            Route::post('frontend-element-seo/{key}/{id}', 'frontendSeoUpdate');
            Route::post('remove/{id}', 'remove')->name('remove');
        });

        // Page Builder
        Route::controller('PageBuilderController')->middleware('permission:manage pages,admin')->group(function () {
            Route::get('manage-pages', 'managePages')->name('manage.pages');
            Route::get('manage-pages/check-slug/{id?}', 'checkSlug')->name('manage.pages.check.slug');
            Route::post('manage-pages', 'managePagesSave')->name('manage.pages.save');
            Route::post('manage-pages/update', 'managePagesUpdate')->name('manage.pages.update');
            Route::post('manage-pages/delete/{id}', 'managePagesDelete')->name('manage.pages.delete');
            Route::get('manage-section/{id}', 'manageSection')->name('manage.section');
            Route::post('manage-section/{id}', 'manageSectionUpdate')->name('manage.section.update');

            Route::get('manage-seo/{id}', 'manageSeo')->name('manage.pages.seo');
            Route::post('manage-seo/{id}', 'manageSeoStore');
        });
    });

    //System Information
    Route::controller('SystemController')->middleware('permission:view application info,admin')->name('system.')->prefix('system')->group(function () {
        Route::get('info', 'systemInfo')->name('info');
        Route::get('optimize-clear', 'optimizeClear')->name('optimize.clear');
    });

    //Request Money
    Route::controller('RequestMoneyController')->middleware('permission:view money requests,admin')->name('request.money.')->prefix('request-money')->group(function () {
        Route::get('history', 'history')->name('history');
    });

    //virtual card
    Route::controller('VirtualCardController')->name('virtual.card.')->prefix('virtual-card')->group(function () {
        Route::get('list', 'list')->name('list')->middleware('permission:view virtual card,admin');
        Route::get('detail/{id}', 'detail')->name('detail')->middleware('permission:view virtual card,admin');
        Route::get('provider-configuration', 'providerConfiguration')->name('provider.configuration')->middleware('permission:configure virtual card provider,admin');
        Route::post('provider-configuration/update/{code}', 'providerConfigurationUpdate')->name('provider.configuration.update')->middleware('permission:configure virtual card provider,admin');
        Route::get('charge-and-other-setting', 'chargeAndOtherSetting')->name('charge.and.other.setting')->middleware('permission:manage virtual card charge,admin');
        Route::post('charge-and-other-setting', 'chargeAndOtherSettingUpdate')->name('charge.and.other.setting.update')->middleware('permission:manage virtual card charge,admin');
    });

    Route::controller('GeneralSettingController')->group(function () {

        // General Setting
        Route::middleware('permission:update general settings,admin')->group(function () {
            Route::get('general-setting', 'general')->name('setting.general');
            Route::post('general-setting', 'generalUpdate');
        });

        Route::get('setting/qr-code/template', 'qrCodeTemplate')->name('setting.qr.code')->middleware('permission:manage qr code,admin');
        Route::post('setting/qr-code/template', 'qrCodeTemplateUpdate')->middleware('permission:manage qr code,admin');

        //configuration
        Route::middleware('permission:system configuration,admin')->group(function () {
            Route::get('setting/system-configuration', 'systemConfiguration')->name('setting.system.configuration');
            Route::get('setting/system-configuration/{key}', 'systemConfigurationUpdate')->name("setting.system.configuration.update");
        });
        // Logo-Icon
        Route::middleware('permission:update brand settings,admin')->group(function () {
            Route::get('setting/brand', 'logoIcon')->name('setting.brand');
            Route::post('setting/brand', 'logoIconUpdate')->name('setting.brand');
        });

        Route::middleware('permission:security settings,admin')->group(function () {
            //Custom CSS
            Route::get('custom-css', 'customCss')->name('setting.custom.css');
            Route::post('custom-css', 'customCssSubmit');

            Route::get('sitemap', 'sitemap')->name('setting.sitemap');
            Route::post('sitemap', 'sitemapSubmit');

            Route::get('robot', 'robot')->name('setting.robot');
            Route::post('robot', 'robotSubmit');

            //Cookie
            Route::get('cookie', 'cookie')->name('setting.cookie');
            Route::post('cookie', 'cookieSubmit');
        });

        //maintenance_mode
        Route::middleware('permission:update maintenance mode,admin')->group(function () {
            Route::get('maintenance-mode', 'maintenanceMode')->name('maintenance.mode');
            Route::post('maintenance-mode', 'maintenanceModeSubmit');
        });
    });
    // Deposit Gateway
    Route::name('gateway.')->prefix('gateway')->middleware('permission:manage gateways,admin')->group(function () {
        // Manual Methods
        Route::controller('ManualGatewayController')->prefix('manual')->name('manual.')->group(function () {
            Route::get('new', 'create')->name('create');
            Route::post('new', 'store')->name('store');
            Route::get('edit/{alias}', 'edit')->name('edit');
            Route::post('update/{id}', 'update')->name('update');
            Route::post('status/{id}', 'status')->name('status');
        });

        // Automatic Gateway
        Route::controller('AutomaticGatewayController')->name('automatic.')->group(function () {
            Route::get('/', 'index')->name('index');
            Route::get('edit/{alias}', 'edit')->name('edit');
            Route::post('update/{code}', 'update')->name('update');
            Route::post('remove/{id}', 'remove')->name('remove');
            Route::post('status/{id}', 'status')->name('status');
            Route::post('sync-operators/{id}', 'syncOperators')->name('sync.operators');
        });
    });

    //cron
    Route::controller('CronConfigurationController')->name('cron.')->prefix('cron')->middleware('permission:manage cron job,admin')->group(function () {
        Route::get('index', 'cronJobs')->name('index');
        Route::post('store', 'cronJobStore')->name('store');
        Route::post('update/{id}', 'cronJobUpdate')->name('update');
        Route::post('delete/{id}', 'cronJobDelete')->name('delete');
        Route::get('schedule', 'schedule')->name('schedule');
        Route::post('schedule/store/{id?}', 'scheduleStore')->name('schedule.store');
        Route::post('schedule/status/{id}', 'scheduleStatus')->name('schedule.status');
        Route::get('schedule/pause/{id}', 'schedulePause')->name('schedule.pause');
        Route::get('schedule/logs/{id}', 'scheduleLogs')->name('schedule.logs');
        Route::post('schedule/log/resolved/{id}', 'scheduleLogResolved')->name('schedule.log.resolved');
        Route::post('schedule/log/flush/{id}', 'logFlush')->name('log.flush');
    });
});
