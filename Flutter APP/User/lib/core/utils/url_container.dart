import 'package:ovopay/environment.dart';

class UrlContainer {
  static const String domainUrl = Environment.MAIN_API_URL;
  static const String baseUrl = '$domainUrl/api/';

  static const String socialLoginEndPoint = 'social-login';
  static const String dashBoardEndPoint = 'dashboard';
  static const String promotionalOffersEndPoint = 'offers/list';

  static const String accountDisable = "delete-account";

  static const String notificationEndPoint = 'push-notifications';
  static const String notificationSettingsEndPoint = 'notification/settings';

  static const String registrationEndPoint = 'register';
  static const String authenticationEndPoint = 'authentication';
  static const String loginEndPoint = 'login';
  static const String logoutUrl = 'logout';
  static const String deleteAccountEndPoint = 'delete-account';

  static const String forgetPasswordEndPoint = 'password/mobile';
  static const String passwordVerifyEndPoint = 'password/verify-code';
  static const String resetPasswordEndPoint = 'password/reset';
  static const String verify2FAUrl = 'verify-g2fa';

  static const String verifyOtp = 'verification-process/verify/otp';
  static const String verifyPin = 'verification-process/verify/pin';
  static const String otpResend = 'verification-process/verify/resend/otp';

  static const String verifyEmailEndPoint = 'verify-email';
  static const String verifySmsEndPoint = 'verify-mobile';
  static const String resendVerifyCodeEndPoint = 'resend-verify/';
  static const String authorizationCodeEndPoint = 'authorization';

  static const String dashBoardUrl = 'dashboard';
  static const String transactionEndpoint = 'transactions';
  static const String statementsEndpoint = 'statements';

  static const String kycFormUrl = 'kyc-form';
  static const String kycSubmitUrl = 'kyc-submit';

  static const String generalSettingEndPoint = 'general-setting';
  static const String moduleSettingEndPoint = 'module-setting';

  static const String privacyPolicyEndPoint = 'policies';
  static const String getProfileEndPoint = 'user-info';
  static const String updateProfileEndPoint = 'profile-setting';
  static const String profileCompleteEndPoint = 'user-data-submit';

  static const String changePasswordEndPoint = 'change-password';
  static const String countryEndPoint = 'get-countries';

  static const String deviceTokenEndPoint = 'add-device-token';
  static const String languageUrl = 'language/';

  static const String twoFactor = "twofactor";
  static const String twoFactorEnable = "twofactor/enable";
  static const String twoFactorDisable = "twofactor/disable";

  static const String communityGroupsEndPoint = 'community-groups';
  static const String pinValidate = "pin/validate";

  static const String supportMethodsEndPoint = 'support/method';
  static const String supportListEndPoint = 'ticket';
  static const String storeSupportEndPoint = 'ticket/create';
  static const String supportViewEndPoint = 'ticket/view';
  static const String supportReplyEndPoint = 'ticket/reply';
  static const String supportCloseEndPoint = 'ticket/close';
  static const String supportDownloadEndPoint = 'ticket/download';
  static const String countryFlagImageLink = 'https://flagpedia.net/data/flags/h24/{countryCode}.webp';
  static const String faqEndPoint = 'faq';

  //Qr Code
  static const String myQrCodeEndPoint = 'qr-code';
  static const String myQrCodeDownloadEndPoint = 'qr-code/download';
  static const String myQrCodeRemoveEndPoint = 'qr-code/remove';
  static const String qrCodeScanEndPoint = 'qr-code/scan';
  static const String qrCodeLoginEndPoint = 'login-with/qr-code';

  static const String userExistEndPoint = 'user/exist';
  static const String agentExistEndPoint = 'agent/exist';
  static const String merchantExistEndPoint = 'merchant/exist';

  //Module
  ///Add Money
  static const String depositHistoryUrl = 'add-money/history';
  static const String depositMethodUrl = 'add-money/methods';
  static const String depositInsertUrl = 'add-money/insert';

  ///Add Money End
  ///Send Money
  static const String sendMoneyEndPoint = 'send-money';
  static const String sendMoneyStoreEndPoint = 'send-money/store';
  static const String sendMoneyHistoryEndPoint = 'send-money/history';

  ///Send Money End
  ///Send Money
  static const String requestMoneyEndPoint = 'request-money/create';
  static const String requestMoneyStoreEndPoint = 'request-money/store';
  static const String requestMoneyReceivedStoreEndPoint = 'request-money/received-store';
  static const String sentRequestMoneyEndPoint = 'request-money/history';
  static const String receivedRequestMoneyEndPoint = 'request-money/received-history';
  static const String requestMoneyApprove = 'request/money-approve';
  static const String requestMoneyReject = 'request-money/reject';

  ///Send Money End
  ///Make Payment
  static const String makePaymentEndPoint = 'make-payment/create';
  static const String makePaymentStoreEndPoint = 'make-payment/store';
  static const String makePaymentHistoryEndPoint = 'make-payment/history';

  ///Make Payment End
  ///Cash Out
  static const String cashOutEndPoint = 'cash-out/create';
  static const String cashOutStoreEndPoint = 'cash-out/store';
  static const String cashOutHistoryEndPoint = 'cash-out/history';
  //Cash Out End
  ///Bill Pay
  static const String billPayCategoryEndPoint = 'utility-bill/create';
  static const String billPayEndPoint = 'utility-bill/store';
  static const String billPayHistoryEndPoint = 'utility-bill/history';
  static const String billPayDownloadEndPoint = 'utility-bill/pdf';
  static const String billPayCompanyAccountEndPoint = 'utility-bill/company/store';
  static const String billPayCompanyDeleteEndPoint = 'utility-bill/company/delete';
  //Bill Pay End
  ///Education
  static const String educationFeeEndPoint = 'education-fee/create';
  static const String educationStoreEndPoint = 'education-fee/store';
  static const String educationHistoryEndPoint = 'education-fee/history';
  static const String educationDownloadEndPoint = 'education-fee/pdf';
  //Education End
  ///MicroFinance
  static const String microfinanceEndPoint = 'microfinance/create';
  static const String microfinanceStoreEndPoint = 'microfinance/store';
  static const String microfinanceHistoryEndPoint = 'microfinance/history';
  static const String microfinanceDownloadEndPoint = 'microfinance/pdf';
  //MicroFinance End
  ///Donation
  static const String donationEndPoint = 'donation/create';
  static const String donationStoreEndPoint = 'donation/store';
  static const String donationHistoryEndPoint = 'donation/history';
  //Donation End
  ///Recharge
  static const String rechargeEndPoint = 'mobile-recharge';
  static const String rechargeStoreEndPoint = 'mobile-recharge/store';
  static const String rechargeHistoryEndPoint = 'mobile-recharge/history';

  //gift card
  static const String allGiftCardEndPoint = 'gift-card/create';
  static const String giftPurchaseEndPoint = 'gift-card/purchase';
  static const String giftCardHistoryEndPoint = 'gift-card/history';
  static const String giftCardPdfDownloadEndPoint = 'gift-card/pdf';

  ///Recharge End
  ///Airtime Recharge
  static const String airtimeEndPoint = 'airtime/create';
  static const String airtimeStoreEndPoint = 'airtime/store';
  static const String airtimeOperatorDetailsEndPoint = 'airtime/operators-by-country';
  static const String airtimeTopUpEndPoint = 'airtime/top-up';
  static const String airtimeHistoryEndPoint = 'airtime/history';

  ///Airtime Recharge End
  ///Bank transfer
  static const String bankTransferEndPoint = 'bank-transfer/create';
  static const String bankTransferStoreEndPoint = 'bank-transfer/store';
  static const String bankTransferAddBankEndPoint = 'bank-transfer/account';
  static const String bankTransferDeleteBankEndPoint = 'bank-transfer/delete/account';
  static const String bankTransferBankDetailsEndPoint = 'bank-transfer/account-details';
  static const String bankTransferHistoryEndPoint = 'bank-transfer/history';

  ///Bank transfer End
  ///Virtual card
  static const String virtualCardListEndPoint = 'virtual-card/list';
  static const String virtualCardNewEndPoint = 'virtual-card/new';
  static const String virtualCardSingleEndPoint = 'virtual-card/view';
  static const String virtualCardStoreEndPoint = 'virtual-card/store';
  static const String virtualCardAddFundEndPoint = 'virtual-card/add/fund';
  static const String virtualCardCancelEndPoint = 'virtual-card/cancel';
  static const String virtualCardConfidentialEndPoint = 'virtual-card/confidential';
  static const String virtualCardHistoryEndPoint = 'virtual-card/transaction';

  ///Virtual card end
  static const String supportImagePath = '$domainUrl/assets/support/';
  static const String languageImagePath = '$domainUrl/assets/images/language/';
  static const String withDrawImagePath = '$domainUrl/assets/images/withdraw_method/';
  static const String userImagePath = '$domainUrl/assets/images/user/profile/';
  static const String agentImagePath = '$domainUrl/assets/images/agent/profile/';
  static const String merchantImagePath = '$domainUrl/assets/images/merchant/profile/';
  static const String maintenanceImagePath = '$domainUrl/assets/images/maintenance/';

  //investment
  static const String investmentPlan = 'investment/plan';
  static const String makeInvestmentStoreEndPoint = 'investment/store';
  static const String investmentHistoryEndPoint = 'investment/history';
}
