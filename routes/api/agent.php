<?php

use Illuminate\Support\Facades\Route;

Route::namespace('Api')->name('api.agent.')->group(function () {
    Route::namespace('Agent\Auth')->group(function () {
        Route::controller('LoginController')->group(function () {
            Route::post('authentication', 'authentication');
            Route::post('check-token', 'checkToken');
            Route::post('login-with/qr-code/{code}', 'loginWithQrCode')->middleware('auth:sanctum','token.permission:agent_token');
        });
        Route::controller('ForgotPasswordController')->group(function () {
            Route::post('password/mobile', 'sendResetCode');
            Route::post('password/verify-code', 'verifyCode');
            Route::post('password/reset', 'reset');
        });
    });

    Route::middleware(['auth:sanctum', 'token.permission:agent_token'])->group(function () {

        Route::post('data-submit', 'Agent\AgentController@userDataSubmit')->middleware('mobile.verify');

        //mobile verify
        Route::controller('Agent\AuthorizationController')->group(function () {
            Route::get('authorization', 'authorization');
            Route::get('resend-verify/{type}', 'sendVerifyCode');
            Route::post('verify-mobile', 'mobileVerification');
        });

        //authorization
        Route::middleware(['mobile.verify', 'registration.complete'])->controller('Agent\AuthorizationController')->group(function () {
            Route::post('verify-email', 'emailVerification');
            Route::post('verify-g2fa', 'g2faVerification');
        });
        //KYC
        Route::middleware(['mobile.verify', 'registration.complete', 'check.status'])->namespace('Agent')->controller('AgentController')->group(function () {
            Route::get('kyc-form', 'kycForm');
            Route::post('kyc-submit', 'kycSubmit');
        });

        Route::middleware(['mobile.verify', 'registration.complete:agent', 'check.status'])->group(function () {

            Route::namespace('Agent')->group(function () {
                Route::controller('AgentController')->group(function () {
                    Route::get('user-info', 'userInfo');
                    Route::get('dashboard', 'home');

                    Route::get('push-notifications', 'pushNotifications');
                    Route::post('push-notifications/read/{id}', 'pushNotificationsRead');

                    Route::post('pin/validate', 'validatePin');

                    //Report
                    Route::any('add-money/history', 'addMoneyHistory');
                    Route::get('transactions', 'transactions');

                    Route::post('statements', 'statements');

                    Route::get('qr-code', 'qrCode');
                    Route::post('qr-code/download', 'qrCodeDownload');
                    Route::post('qr-code/remove', 'qrCodeRemove');
                    Route::post('qr-code/scan', 'qrCodeScan');

                    Route::post('profile-setting', 'submitProfile');
                    Route::post('change-password', 'submitPassword');

                    Route::get('commission-log', 'commissionLog');

                    Route::get('twofactor', 'show2faData');
                    Route::post('twofactor/enable', 'create2fa');
                    Route::post('twofactor/disable', 'disable2fa');

                    Route::post('delete-account', 'accountDelete');

                    Route::get('notification/settings', 'notificationSettings');
                    Route::post('notification/settings', 'notificationSettingsUpdate');
                    Route::post('remove/promotional/notification/image', 'removePromotionalNotificationImage');
                });
                //Cash In
                Route::controller('CashInController')->middleware('module:cash_in', 'kyc.agent')->group(function () {
                    Route::post('check-user', 'checkUser');
                    Route::prefix('cash-in')->group(function () {
                        Route::get('history', 'history');
                        Route::get('details/{id}', 'details');
                        Route::get('create', 'cashInForm');
                        Route::post('submit', 'confirmCashIn');
                        Route::get('pdf/{id}', 'pdf');
                    });
                });

                // Withdraw
                Route::controller('AgentWithdrawController')->group(function () {
                    Route::middleware(['kyc.agent'])->group(function () {
                        Route::get('withdraw-method', 'withdrawMethod');
                        Route::post('withdraw-request', 'withdrawStore');
                        Route::post('withdraw-request/confirm', 'withdrawSubmit');

                        Route::get('withdraw/account/save/{methodId}', 'saveAccount')->name('account.save');
                        Route::post('withdraw/account/save-data/{methodId?}', 'saveAccountData')->name('account.save.data');
                        Route::get('withdraw/account/edit/{id}', 'editAccount')->name('account.edit');
                        Route::get('withdraw/account/show/{id}', 'getAccount')->name('account.show');
                        Route::post('withdraw/account/delete/{id}', 'deleteAccount')->name('account.delete');
                    });
                    Route::get('withdraw/history', 'withdrawLog');
                });
            });

            // Payment
            Route::controller('PaymentController')->middleware(['module:add_money', 'kyc.agent'])->group(function () {
                Route::get('add-money/methods', 'methods');
                Route::post('add-money/insert', 'depositInsert');
            });

            Route::controller('TicketController')->prefix('ticket')->group(function () {
                Route::get('/', 'supportTicket');
                Route::post('create', 'storeSupportTicket');
                Route::get('view/{ticket}', 'viewTicket');
                Route::post('reply/{id}', 'replyTicket');
                Route::post('close/{id}', 'closeTicket');
                Route::get('download/{attachment_id}', 'ticketDownload');
            });
        });

        Route::get('logout', 'Agent\Auth\LoginController@logout');
        Route::post('add-device-token', 'Agent\AgentController@addDeviceToken')->name('get.device.token');
    });
});
