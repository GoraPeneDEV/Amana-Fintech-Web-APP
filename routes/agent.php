<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Start Agent Area
|--------------------------------------------------------------------------
*/

Route::namespace('Agent\Auth')->name('agent.')->middleware('agent.guest')->group(function () {
    Route::controller('LoginController')->group(function () {
        Route::get('/login', 'showLoginForm')->name('login');
        Route::post('/login', 'login');
        Route::post('qr-code/login/{id}', 'qrCodeLogin')->name('qrcode.login');
        Route::get('logout', 'logout')->middleware('agent')->withoutMiddleware('agent.guest')->name('logout');
    });

    Route::controller('RegisterController')->group(function () {
        Route::get('register', 'showRegistrationForm')->name('register');
        Route::post('register', 'register');
        Route::post('check-user', 'checkUser')->name('checkUser')->withoutMiddleware('guest');
    });

    Route::controller('ForgotPasswordController')->prefix('pin')->name('password.')->group(function () {
        Route::get('reset', 'showLinkRequestForm')->name('request');
        Route::post('email', 'sendResetCodeEmail')->name('email');
        Route::get('code-verify', 'codeVerify')->name('code.verify');
        Route::post('verify-code', 'verifyCode')->name('verify.code');
    });

    Route::controller('ResetPasswordController')->group(function () {
        Route::post('password/reset', 'reset')->name('password.update');
        Route::get('password/reset/{token}', 'showResetForm')->name('password.reset');
    });
});


Route::namespace('Agent')->middleware('agent')->name('agent.')->group(function () {

    //authorization
    Route::get('agent-data', 'AgentController@userData')->name('data')->middleware('mobile_verified:agent');
    Route::post('agent-data-submit', 'AgentController@userDataSubmit')->name('data.submit')->middleware('mobile_verified:agent');

    Route::controller('AuthorizationController')->group(function () {
        Route::get('authorization', 'authorizeForm')->name('authorization');
        Route::get('resend-verify/{type}', 'sendVerifyCode')->name('send.verify.code');
        Route::post('verify-email', 'emailVerification')->name('verify.email');
        Route::post('verify-mobile', 'mobileVerification')->name('verify.mobile');
        Route::post('verify-g2fa', 'g2faVerification')->name('2fa.verify');
    });



    Route::middleware(['mobile.verify:agent', 'registration.complete:agent', 'check.status:agent'])->group(function () {

        Route::middleware('agent')->group(function () {
            Route::controller('AgentController')->group(function () {
                Route::get('dashboard', 'home')->name('home');
                Route::get('download-attachments/{file_hash}', 'downloadAttachment')->name('download.attachment');

                Route::middleware('kyc.agent')->group(function () {

                    Route::any('deposit/history', 'depositHistory')->name('add.money.history');


                    //Report
                    Route::get('transactions', 'transactions')->name('transactions');
                    Route::get('statements', 'statements')->name('statement.history');
                });

                //2FA
                Route::get('twofactor', 'show2faForm')->name('twofactor');
                Route::post('twofactor/enable', 'create2fa')->name('twofactor.enable');
                Route::post('twofactor/disable', 'disable2fa')->name('twofactor.disable');

                //KYC
                Route::get('kyc-form', 'kycForm')->name('kyc.form');
                Route::get('kyc-data', 'kycData')->name('kyc.data');
                Route::post('kyc-submit', 'kycSubmit')->name('kyc.submit');


                Route::post('add-device-token', 'addDeviceToken')->name('add.device.token');

                Route::get('notification/settings', 'notificationSetting')->name('notification.setting');
                Route::post('notification/settings', 'notificationSettingsUpdate')->name('notification.setting');
            });

            // Cash in
            Route::prefix('cash-in')->name('cash.in.')->middleware(['kyc.agent', 'module:cash_in'])->controller('CashInController')->group(function () {
                Route::get('/', 'create')->name('create');
                Route::post('store', 'store')->name('store');
                Route::get('details/{id}', 'details')->name('details');
                Route::get('history', 'history')->name('history');
                Route::get('pdf/{id}', 'pdf')->name('pdf');
            });


            // add money
            Route::prefix('add-money')->name('add.money.')->middleware(['kyc.agent', 'module:add_money,agent'])->controller('AddMoneyController')->group(function () {
                Route::get('/', 'create')->name('create');
                Route::post('store', 'store')->name('store');
                Route::get('history', 'History')->name('history');
            });

            // Withdraw
            Route::controller('WithdrawController')->prefix('withdraw')->name('withdraw.')->middleware(['kyc.agent'])->group(function () {
                Route::get('/', 'withdrawMoney')->name('index');
                Route::post('/', 'withdrawStore')->name('money');
                Route::get('preview', 'withdrawPreview')->name('preview');
                Route::post('preview', 'withdrawSubmit')->name('submit');
                Route::get('history', 'withdrawLog')->name('history');

                // save account data
                Route::get('account/setting', 'accountSetting')->name('account.setting');
                Route::get('account/save/{methodId}', 'saveAccount')->name('account.save');
                Route::post('account/save-data/{methodId?}', 'saveAccountData')->name('account.save.data');
                Route::get('account/edit/{id}', 'editAccount')->name('account.edit');
                Route::post('account/delete/{id}', 'deleteAccount')->name('account.delete');
            });

            //Profile setting
            Route::controller('ProfileController')->group(function () {
                Route::get('profile-setting', 'profile')->name('profile.setting');
                Route::post('profile-setting', 'submitProfile');
                Route::get('change-pin', 'changePassword')->name('change.password');
                Route::post('change-pin', 'submitPassword');
            });
        });
    });
});

Route::prefix('deposit')->name('agent.deposit.')->middleware('kyc.agent')->controller('Gateway\PaymentController')->group(function () {
    Route::get('confirm', 'depositConfirm')->name('confirm');
    Route::get('manual', 'manualDepositConfirm')->name('manual.confirm');
    Route::post('manual', 'manualDepositUpdate')->name('manual.update');
});
