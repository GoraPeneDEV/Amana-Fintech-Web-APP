<?php

use Illuminate\Support\Facades\Route;

Route::namespace('Api')->group(function () {
    Route::namespace('Merchant\Auth')->group(function () {
        Route::controller('LoginController')->group(function () {
            Route::post('authentication', 'authentication');
            Route::post('check-token', 'checkToken');
            Route::post('login-with/qr-code/{code}', 'loginWithQrCode')->middleware('auth:sanctum','token.permission:merchant_token');
        });
        Route::controller('ForgotPasswordController')->group(function () {
            Route::post('password/mobile', 'sendResetCode');
            Route::post('password/verify-code', 'verifyCode');
            Route::post('password/reset', 'reset');
        });
    });

    Route::middleware(['auth:sanctum', 'token.permission:merchant_token'])->group(function () {

        Route::post('data-submit', 'Merchant\MerchantController@userDataSubmit')->middleware('mobile.verify');

        //mobile verify
        Route::controller('Merchant\AuthorizationController')->group(function () {
            Route::get('authorization', 'authorization');
            Route::get('resend-verify/{type}', 'sendVerifyCode');
            Route::post('verify-mobile', 'mobileVerification');
        });

        //authorization
        Route::middleware(['mobile.verify', 'registration.complete'])->controller('Merchant\AuthorizationController')->group(function () {
            Route::post('verify-email', 'emailVerification');
            Route::post('verify-g2fa', 'g2faVerification');
        });

        //KYC
        Route::middleware(['mobile.verify', 'registration.complete', 'check.status'])->namespace('Merchant')->controller('MerchantController')->group(function () {
            Route::get('kyc-form', 'kycForm');
            Route::post('kyc-submit', 'kycSubmit');
        });

        Route::middleware(['mobile.verify', 'registration.complete:merchant', 'check.status'])->group(function () {

            Route::namespace('Merchant')->group(function () {
                Route::controller('MerchantController')->group(function () {
                    Route::get('user-info', 'userInfo');

                    Route::get('dashboard', 'dashboard');

                    Route::post('statements', 'statements');

                    Route::post('pin/validate', 'validatePin');

                    Route::get('push-notifications', 'pushNotifications');
                    Route::post('push-notifications/read/{id}', 'pushNotificationsRead');

                    Route::get('transactions', 'transactions');

                    Route::get('business/api/key', 'apiKey');
                    Route::post('generate/api/key', 'generateApiKey');

                    Route::get('/qr-code', 'qrCode');
                    Route::post('/qr-code/download', 'qrCodeDownload');
                    Route::post('/qr-code/remove', 'qrCodeRemove');

                    Route::post('profile-setting', 'submitProfile');
                    Route::post('change-password', 'submitPassword');

                    Route::get('twofactor', 'show2faData');
                    Route::post('twofactor/enable', 'create2fa');
                    Route::post('twofactor/disable', 'disable2fa');

                    Route::post('delete-account', 'accountDelete');

                    // Payment list

                    Route::get('payment-list', 'paymentList');
                    Route::get('payment-details/{id}', 'paymentDetails');
                    Route::get('payment-pdf/{id}', 'paymentPdf');


                    Route::get('notification/settings', 'notificationSettings');
                    Route::post('notification/settings', 'notificationSettingsUpdate');
                    Route::post('remove/promotional/notification/image', 'removePromotionalNotificationImage');
                });

                // Withdraw
                Route::controller('MerchantWithdrawController')->group(function () {
                    Route::middleware(['kyc.merchant'])->group(function () {
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

            Route::controller('TicketController')->prefix('ticket')->group(function () {
                Route::get('/', 'supportTicket');
                Route::post('create', 'storeSupportTicket');
                Route::get('view/{ticket}', 'viewTicket');
                Route::post('reply/{id}', 'replyTicket');
                Route::post('close/{id}', 'closeTicket');
                Route::get('download/{attachment_id}', 'ticketDownload');
            });
        });

        Route::get('logout', 'Merchant\Auth\LoginController@logout');
        Route::post('add-device-token', 'Merchant\MerchantController@addDeviceToken');
    });
});
