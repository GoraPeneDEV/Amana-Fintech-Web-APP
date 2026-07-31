<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::namespace('Api')->name('api.')->group(function () {

    Route::controller('AppController')->group(function () {
        Route::withoutMiddleware('maintenance')->group(function () {
            Route::get('general-setting', 'generalSetting');
            Route::get('get-countries', 'getCountries');
            Route::get('policies', 'policies');
            Route::get('faq', 'faq');
            Route::get('module-setting', 'moduleSetting');
        });
        Route::get('language/{key}', 'getLanguage');
    });

    Route::namespace('Auth')->group(function () {
        Route::controller('LoginController')->group(function () {
            Route::post('authentication', 'authentication');
            Route::post('login-with/qr-code/{code}', 'loginWithQrCode')->middleware('auth:sanctum', 'token.permission:user_token');
            Route::post('check-token', 'checkToken');
        });

        Route::controller('ForgotPasswordController')->group(function () {
            Route::post('password/mobile', 'sendResetCode');
            Route::post('password/verify-code', 'verifyCode');
            Route::post('password/reset', 'reset');
        });
    });

    Route::middleware('auth:sanctum', 'token.permission:user_token')->group(function () {

        Route::post('user-data-submit', 'UserController@userDataSubmit')->middleware('mobile.verify');

        //mobile verify
        Route::controller('AuthorizationController')->group(function () {
            Route::get('authorization', 'authorization');
            Route::get('resend-verify/{type}', 'sendVerifyCode');
            Route::post('verify-mobile', 'mobileVerification');
        });

        //authorization
        Route::middleware(['mobile.verify', 'registration.complete'])->controller('AuthorizationController')->group(function () {
            Route::post('verify-email', 'emailVerification');
            Route::post('verify-g2fa', 'g2faVerification');
        });

        //KYC
        Route::middleware(['mobile.verify', 'registration.complete', 'check.status'])->controller('UserController')->group(function () {
            Route::get('kyc-form', 'kycForm');
            Route::post('kyc-submit', 'kycSubmit');
        });

        Route::middleware(['mobile.verify', 'registration.complete', 'check.status'])->group(function () {

            Route::controller('UserController')->group(function () {

                Route::get('dashboard', 'dashboard')->withoutMiddleware('kyc');
                Route::post('profile-setting', 'submitProfile');
                Route::post('change-password', 'submitPassword');

                Route::get('user-info', 'userInfo')->withoutMiddleware('kyc');

                Route::post('/qr-code/scan', 'qrCodeScan');
                Route::get('/qr-code', 'qrCode');
                Route::post('/qr-code/download', 'qrCodeDownload');
                Route::post('/qr-code/remove', 'qrCodeRemove');

                Route::get('limit-charge', 'trxLimit');

                Route::post('pin/validate', 'validatePin');

                Route::get('offers/list', 'offers');

                Route::get('notification/settings', 'notificationSettings');
                Route::post('notification/settings', 'notificationSettingsUpdate');
                Route::post('remove/promotional/notification/image', 'removePromotionalNotificationImage');

                //Report
                Route::any('add-money/history', 'addMoneyHistory')->middleware('kyc');
                Route::get('transactions', 'transactions');

                Route::get('push-notifications', 'pushNotifications');
                Route::post('push-notifications/read/{id}', 'pushNotificationsRead');

                //2FA
                Route::get('twofactor', 'show2faForm');
                Route::post('twofactor/enable', 'create2fa');
                Route::post('twofactor/disable', 'disable2fa');

                Route::post('delete-account', 'deleteAccount');

                Route::post('user/exist', 'checkUser');
                Route::post('agent/exist', 'checkAgent');
                Route::post('merchant/exist', 'checkMerchant');

                Route::get('statements', 'statements');
            });

            // Payment
            Route::controller('PaymentController')->middleware(['module:add_money', 'kyc'])->group(function () {
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

            //Cash out
            Route::controller('CashOutController')->prefix('cash-out')->middleware(['module:cash_out', 'kyc'])->group(function () {
                Route::get('create', 'create');
                Route::post('store', 'store');
                Route::get('history', 'history');
                Route::get('details/{id}', 'details');
                Route::get('pdf/{id}', 'pdf');
            });

            //Send money

            Route::controller('SendMoneyController')->prefix('send-money')->middleware(['module:send_money', 'kyc'])->group(function () {
                Route::get('/', 'create')->name('create');
                Route::post('store', 'store')->name('store');
                Route::get('details/{id}', 'details')->name('details');
                Route::get('history', 'history')->name('history');
                Route::get('pdf/{id}', 'pdf');
            });

            // Request Money
            Route::controller('RequestMoneyController')->prefix('request-money')->middleware(['module:request_money', 'kyc'])->group(function () {

                Route::get('create', 'create');
                Route::post('store', 'store');
                Route::get('details/{id}', 'details');
                Route::get('/history', 'history');
                Route::get('pdf/{id}', 'pdf');
                Route::get('received/pdf/{id}', 'requestPdf');

                Route::get('received-history', 'requestHistory');
                Route::get('received-details/{id}', 'requestDetails');
                Route::post('received-store/{id}', 'requestStore');
                Route::post('reject/{id}', 'rejectRequest');
            });

            //Make payment

            Route::controller('MakePaymentController')->prefix('make-payment')->middleware(['module:make_payment', 'kyc'])->group(function () {
                Route::get('create', 'create')->name('make.payment');
                Route::post('store', 'store');
                Route::get('history', 'history');
                Route::get('details/{id}', 'details');
                Route::get('pdf/{id}', 'pdf');
            });

            //Pay bill
            Route::controller('PayBillController')->prefix('utility-bill')->middleware(['module:utility_bill', 'kyc'])->group(function () {
                Route::get('create', 'create');
                Route::post('store', 'store');
                Route::get('history', 'history');
                Route::get('details/{id}', 'details');
                Route::get('pdf/{id}', 'pdf');

                Route::post('company/delete/{id}', 'deleteUserCompany');
                Route::get('get-companies', 'getCompanies');
            });

            //Microfinance
            Route::controller('MicrofinanceController')->prefix('microfinance')->middleware(['module:microfinance', 'kyc'])->group(function () {
                Route::get('create', 'create');
                Route::post('store', 'store');
                Route::get('history', 'history');
                Route::get('details/{id}', 'details');
                Route::get('form/{id}', 'form');
                Route::get('pdf/{id}', 'pdf');
            });

            //Education Fee
            Route::controller('EducationFeeController')->prefix('education-fee')->middleware(['module:education_fee', 'kyc'])->group(function () {
                Route::get('create', 'create');
                Route::post('store', 'store');
                Route::get('history', 'history');
                Route::get('details/{id}', 'details');
                Route::get('pdf/{id}', 'pdf');
            });

            //Airtime
            Route::controller('AirtimeController')->prefix('airtime')->middleware(['module:mobile_recharge', 'kyc'])->group(function () {
                Route::get('countries', 'countries');
                Route::get('operators-by-country/{id}', 'getOperatorByCountry');
                Route::get('create', 'create');
                Route::post('store', 'store');
                Route::get('history', 'history');
            });

            //virtual card
            Route::prefix('virtual-card')->middleware(['module:virtual_card'])->controller('VirtualCardController')->group(function () {
                Route::get('list', 'list');
                Route::get('transaction', 'transaction');
                Route::get('new', 'newCard');
                Route::get('view/{id}', 'view');
                Route::post('store', 'store');
                Route::post('add/fund/{id}', 'addFund');
                Route::post('cancel/{id}', 'cancel');
                Route::post('confidential/{id}', 'confidential');
            });

            //gift card
            Route::prefix('gift-card')->middleware(['module:gift_card'])->controller('GiftCardController')->group(function () {
                Route::get('create', 'create');
                Route::get('show/{id}', 'show')->name('show');
                Route::post('purchase/{id}', 'purchase')->name('purchase');
                Route::get('history', 'history')->name('history');
                Route::get('details/{id}', 'details')->name('details');
                Route::get('pdf/{id}', 'pdf')->name('pdf');
            });

            //Donation
            Route::controller('DonationController')->prefix('donation')->middleware(['module:donation', 'kyc'])->group(function () {
                Route::get('create', 'create');
                Route::post('store', 'store');
                Route::get('history', 'history');
                Route::get('details/{id}', 'details');
                Route::get('pdf/{id}', 'pdf');
            });

            Route::prefix('verification-process')->controller('VerificationProcessController')->group(function () {
                Route::post('verify/otp', 'verifyOtp');
                Route::post('verify/pin', 'verifyPin');
                Route::post('verify/resend/otp', 'resendCode');
            });

            //Bank Transfer
            Route::controller('BankTransferController')->prefix('bank-transfer')->middleware(['module:bank_transfer', 'kyc'])->group(function () {
                Route::get('create', 'create');
                Route::post('store', 'store');
                Route::get('history', 'history');
                Route::get('details/{id}', 'details');
                Route::get('pdf/{id}', 'pdf');
                Route::post('account', 'account');
                Route::get('account-details/{id}', 'accountDetails');
                Route::post('delete/account/{id}', 'deleteAccount');
            });

            //Investment
            Route::prefix('investment')->name('investment.')->middleware(['module:investment'])->controller('InvestmentController')->group(function () {
                Route::get('plan', 'all')->name('all');
                Route::post('store', 'store')->name('store');
                Route::get('show/{id}', 'show')->name('show');
                Route::get('details/{id}', 'details')->name('details');
                Route::get('history', 'history')->name('history');
            });
        });

        Route::get('logout', 'Auth\LoginController@logout');
        Route::post('add-device-token', 'UserController@addDeviceToken');
    });
});
