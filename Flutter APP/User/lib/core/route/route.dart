import 'package:get/get.dart';
import 'package:ovopay/app/components/preview_image.dart';
import 'package:ovopay/app/screens/add_money/views/add_money_history_screen.dart';
import 'package:ovopay/app/screens/add_money/views/add_money_webview/my_webview_screen.dart';
import 'package:ovopay/app/screens/add_money/views/add_money_screen.dart';
import 'package:ovopay/app/screens/airtime_recharge_screen/views/airtime_recharge_history_screen.dart';
import 'package:ovopay/app/screens/airtime_recharge_screen/views/airtime_recharge_screen.dart';
import 'package:ovopay/app/screens/auth/biometric/setup_biometric_screen.dart';
import 'package:ovopay/app/screens/auth/email_verification_page/views/email_verification_screen.dart';
import 'package:ovopay/app/screens/auth/forget_pin/views/forget_pin_screen.dart';
import 'package:ovopay/app/screens/auth/kyc/views/kyc_screen.dart';
import 'package:ovopay/app/screens/auth/login/views/login_screen.dart';
import 'package:ovopay/app/screens/auth/register/views/register_screen.dart';
import 'package:ovopay/app/screens/bank_transfer_screen/views/bank_transfer_add_new_bank_screen.dart';
import 'package:ovopay/app/screens/bank_transfer_screen/views/bank_transfer_history_screen.dart';
import 'package:ovopay/app/screens/bank_transfer_screen/views/bank_transfer_screen.dart';
import 'package:ovopay/app/screens/bill_pay_screen/views/bill_pay_history_screen.dart';
import 'package:ovopay/app/screens/bill_pay_screen/views/bill_pay_screen.dart';
import 'package:ovopay/app/screens/gift_screen/views/gift_history_screen.dart';
import 'package:ovopay/app/screens/investment/investment_screen.dart';
import 'package:ovopay/app/screens/investment/widgets/investment_history_screen.dart';
import 'package:ovopay/app/screens/my_qr_code_screen/views/qr_code_login_screen.dart';
import 'package:ovopay/app/screens/no_internet/no_internet_screen.dart';
import 'package:ovopay/app/screens/page_content_screen/views/maintenance_content_screen.dart';
import 'package:ovopay/app/screens/virtual_cards/views/virtual_cards_screen.dart';
import 'package:ovopay/app/screens/virtual_cards/views/create_vc_card_screen.dart';
import 'package:ovopay/app/screens/virtual_cards/views/single_cards_screen.dart';
import 'package:ovopay/app/screens/cash_out_screen_screen/views/cash_out_history_screen.dart';
import 'package:ovopay/app/screens/cash_out_screen_screen/views/cash_out_screen.dart';
import 'package:ovopay/app/screens/dashboard_screen/views/dashboard_screen.dart';
import 'package:ovopay/app/screens/dashboard_screen/views/promotional_offers_screen.dart';
import 'package:ovopay/app/screens/donation_screen/views/donation_history_screen.dart';
import 'package:ovopay/app/screens/donation_screen/views/donation_screen.dart';
import 'package:ovopay/app/screens/education_fee_screen/views/education_fee_history_screen.dart';
import 'package:ovopay/app/screens/education_fee_screen/views/education_fee_screen.dart';
import 'package:ovopay/app/screens/faq/views/faq_screen.dart';
import 'package:ovopay/app/screens/language/language_screen.dart';
import 'package:ovopay/app/screens/micro_finance_screen/views/micro_finance_history_screen.dart';
import 'package:ovopay/app/screens/micro_finance_screen/views/micro_finance_screen.dart';
import 'package:ovopay/app/screens/my_qr_code_screen/views/my_qr_code_screen.dart';
import 'package:ovopay/app/screens/my_qr_code_screen/views/scan_qr_code_screen.dart';
import 'package:ovopay/app/screens/notification_screen/notification_screen.dart';
import 'package:ovopay/app/screens/onboard/views/onboard_screen.dart';
import 'package:ovopay/app/screens/page_content_screen/views/page_content_screen.dart';
import 'package:ovopay/app/screens/payment_screen/views/payment_history_screen.dart';
import 'package:ovopay/app/screens/payment_screen/views/payment_screen.dart';
import 'package:ovopay/app/screens/profile_and_settings_screen/views/app_preferences_screen.dart';
import 'package:ovopay/app/screens/profile_and_settings_screen/views/change_pin_screen.dart';
import 'package:ovopay/app/screens/profile_and_settings_screen/views/delete_account_screen.dart';
import 'package:ovopay/app/screens/profile_and_settings_screen/views/notification_settings_screen.dart';
import 'package:ovopay/app/screens/profile_and_settings_screen/views/privacy_screen.dart';
import 'package:ovopay/app/screens/profile_and_settings_screen/views/profile_edit_screen.dart';
import 'package:ovopay/app/screens/profile_and_settings_screen/views/profile_information_screen.dart';
import 'package:ovopay/app/screens/profile_and_settings_screen/views/security_screen.dart';
import 'package:ovopay/app/screens/request_money_screen/views/request_money_approve_screen.dart';
import 'package:ovopay/app/screens/request_money_screen/views/request_money_history_screen.dart';
import 'package:ovopay/app/screens/request_money_screen/views/request_money_screen.dart';
import 'package:ovopay/app/screens/send_money_screen/views/send_money_history_screen.dart';
import 'package:ovopay/app/screens/send_money_screen/views/send_money_screen.dart';
import 'package:ovopay/app/screens/splash/views/splash_screen.dart';
import 'package:ovopay/app/screens/support_ticket/views/support_ticket_list_screen/support_ticket_list_screen.dart';
import 'package:ovopay/app/screens/support_ticket/views/new_ticket_screen/new_ticket_screen.dart';
import 'package:ovopay/app/screens/support_ticket/views/ticket_details_screen/ticket_details_screen.dart';
import 'package:ovopay/app/screens/two_factor/two_factor_setup_screen/two_factor_setup_screen.dart';
import 'package:ovopay/app/screens/two_factor/two_factor_verification_screen/two_factor_verification_screen.dart';
import 'package:ovopay/core/data/models/user/user_model.dart';
import 'package:ovopay/core/data/services/service_exporter.dart';
import 'package:ovopay/core/utils/url_container.dart';

import '../../app/screens/gift_screen/views/gift_screen.dart';

class RouteHelper {
  // Route names
  static const String splashScreen = "/splash_screen";
  static const String onboardScreen = "/onboard_screen";
  static const String loginScreen = "/login_screen";
  static const String registrationScreen = "/registration_screen";
  static const String dashboardScreen = "/dashboard_screen";
  static const String promotionalOffersScreen = "/promotional_offers_screen";

  //Auth
  static const String forgotPinScreen = "/forgot_pin_screen";
  static const String pinChangeScreen = "/pin_change_screen";

  // Additional screens
  static const String emailVerificationScreen = "/verify_email_screen";
  static const String twoFactorScreen = "/two-factor-screen";
  static const String twoFactorSetupScreen = "/two-factor-setup-screen";
  // Other routes...
  static const String profileInformationScreen = "/profile_information_screen";
  static const String profileEditScreen = "/profile_edit_screen";
  static const String deleteAccountScreen = "/delete_account_screen";
  static const String myQrCodeScreen = "/my_qr_code_screen";
  static const String scanQrCodeScreen = "/scan_qr_code_screen";
  static const String qrCodeLoginScreen = "/qr_code_login_screen";
  static const String securityScreen = "/security_screen";
  static const String notificationSettingsScreen = "/notification_settings_screen";
  static const String privacyScreen = "/privacy_screen";
  static const String pageContentScreen = "/page_content_screen";
  static const String appPreferencesScreen = "/app_preferences_screen";
  static const String kycScreen = "/kyc_screen";
  static const String notificationScreen = "/notification_screen";
  static const String faqScreen = "/faq-screen";
  static const String maintenanceScreen = "/maintenance-screen";
  static const String languageScreen = "/language-screen";
  static const String setupBioMetricScreen = "/setup_biometric_screen";
  //Support ticket
  static const String supportTicketScreen = '/all_ticket_screen';
  static const String ticketDetailsScreen = '/ticket_details_screen';
  static const String newTicketScreen = '/new_ticket_screen';
  static const String previewImageScreen = "/preview-image-screen";
  static const String noInternetScreen = "/no_internet_screen";

  //Module
  static const String sendMoneyScreen = "/send_money_screen";
  static const String sendMoneyHistoryScreen = "/send_money_history_screen";
  static const String requestMoneyScreen = "/request_money_screen";
  static const String requestMoneyApproveScreen = "/request_money_approve_screen";
  static const String requestMoneyHistoryScreen = "/request_money_history_screen";
  static const String cashOutScreen = "/cash_out_screen";
  static const String cashOutHistoryScreen = "/cash_out_history_screen";
  static const String paymentScreen = "/payment_screen";
  static const String paymentHistoryScreen = "/payment_history_screen";
  static const String giftCardScreen = "/gift_card_screen";
  static const String giftHistoryScreen = "/mobile_recharge_history_screen";
  static const String airTimeScreen = "/airtime_recharge_screen";
  static const String airTimeHistoryScreen = "/airtime_history_screen";
  static const String billPayScreen = "/bill_pay_screen";
  static const String billPayHistoryScreen = "/bill_pay_history_screen";
  static const String bankTransferScreen = "/bank_transfer_screen";
  static const String bankTransferAddNewBankScreen = "/bank_transfer_add_new_bank_screen";
  static const String bankTransferHistoryScreen = "/bank_transfer_history_screen";
  static const String educationFeeScreen = "/education_fee_screen";
  static const String educationFeeHistoryScreen = "/education_fee_history_screen";
  static const String donationScreen = "/donation_screen";
  static const String donationHistoryScreen = "/donation_history_screen";
  static const String microFinanceScreen = "/micro_finance_screen";
  static const String microFinanceHistoryScreen = "/micro_finance_history_screen";
  static const String addMoneyScreen = "/add_money_screen";
  static const String addMoneyWebViewScreen = '/add_money_webView';
  static const String addMoneyHistoryScreen = "/add_money_history_screen";
  static const String virtualCardsScreen = "/cards_screen";
  static const String singleCardsScreen = "/single_cards_screen";
  static const String createVccCardScreen = "/create_vcc_card_screen";
  static const String investmentScreen = "/investment_screen";
  static const String investmentHistoryScreen = "/investment__history_screen";

  // Define your routes
  static List<GetPage> routes = [
    GetPage(
      name: splashScreen,
      page: () => const SplashScreen(),
      transition: Transition.fadeIn,
    ),
    GetPage(
      name: onboardScreen,
      page: () => const OnboardScreen(),
      transitionDuration: const Duration(milliseconds: 400),
      transition: Transition.fadeIn,
    ),
    GetPage(
      name: loginScreen,
      page: () => const LoginScreen(),
      transitionDuration: const Duration(milliseconds: 400),
      transition: Transition.fadeIn,
    ),
    GetPage(
      name: registrationScreen,
      page: () => RegisterScreen(
        userModel: Get.arguments != null ? Get.arguments as UserModel : null,
      ),
      transitionDuration: const Duration(milliseconds: 400),
      transition: Transition.rightToLeftWithFade,
    ),
    GetPage(
      name: forgotPinScreen,
      page: () => const ForgotPinScreen(),
      transitionDuration: const Duration(milliseconds: 400),
      transition: Transition.rightToLeftWithFade,
    ),
    //Home Auth
    GetPage(
      name: dashboardScreen,
      page: () => const DashboardScreen(),
      transitionDuration: const Duration(milliseconds: 400),
      transition: Transition.fadeIn,
    ),
    GetPage(
      name: profileInformationScreen,
      page: () => const ProfileInformationScreen(),
      transitionDuration: const Duration(milliseconds: 400),
      transition: Transition.fadeIn,
    ),
    GetPage(
      name: profileEditScreen,
      page: () => ProfileEditScreen(),
      transitionDuration: const Duration(milliseconds: 400),
      transition: Transition.fadeIn,
    ),
    GetPage(
      name: myQrCodeScreen,
      page: () => MyQrCodeScreen(),
      transitionDuration: const Duration(milliseconds: 400),
      transition: Transition.fadeIn,
    ),
    GetPage(
      name: scanQrCodeScreen,
      page: () => ScanQrCodeScreen(scanSubTitle: Get.arguments),
      transitionDuration: const Duration(milliseconds: 400),
      transition: Transition.fadeIn,
    ),
    GetPage(
      name: qrCodeLoginScreen,
      page: () => QrCodeLoginScreen(scanSubTitle: Get.arguments),
      transitionDuration: const Duration(milliseconds: 400),
      transition: Transition.fadeIn,
    ),
    GetPage(
      name: pinChangeScreen,
      page: () => const ChangPineScreen(),
      transitionDuration: const Duration(milliseconds: 400),
      transition: Transition.rightToLeftWithFade,
    ),
    GetPage(
      name: deleteAccountScreen,
      page: () => const DeleteAccountScreen(),
      transitionDuration: const Duration(milliseconds: 400),
      transition: Transition.rightToLeftWithFade,
    ),
    GetPage(
      name: securityScreen,
      page: () => const SecurityScreen(),
      transitionDuration: const Duration(milliseconds: 400),
      transition: Transition.fadeIn,
    ),
    GetPage(
      name: notificationSettingsScreen,
      page: () => const NotificationSettingsScreen(),
      transitionDuration: const Duration(milliseconds: 400),
      transition: Transition.fadeIn,
    ),
    GetPage(
      name: privacyScreen,
      page: () => const PrivacyScreen(),
      transitionDuration: const Duration(milliseconds: 400),
      transition: Transition.fadeIn,
    ),
    GetPage(
      name: maintenanceScreen,
      page: () => const MaintenanceContentScreen(),
      transitionDuration: const Duration(milliseconds: 400),
      transition: Transition.fadeIn,
    ),
    GetPage(
      name: noInternetScreen,
      page: () => const NoInterNetScreen(),
      transitionDuration: const Duration(milliseconds: 400),
      transition: Transition.fadeIn,
    ),
    GetPage(
      name: appPreferencesScreen,
      page: () => const AppPreferencesScreen(),
      transitionDuration: const Duration(milliseconds: 400),
      transition: Transition.fadeIn,
    ),
    GetPage(
      name: languageScreen,
      page: () => const LanguageScreen(),
      transitionDuration: const Duration(milliseconds: 400),
      transition: Transition.rightToLeft,
    ),
    GetPage(
      name: notificationScreen,
      page: () => const NotificationScreen(),
      transitionDuration: const Duration(milliseconds: 400),
      transition: Transition.rightToLeft,
    ),
    GetPage(
      name: pageContentScreen,
      page: () => const PageContentScreen(),
      transitionDuration: const Duration(milliseconds: 400),
      transition: Transition.rightToLeft,
    ),

    GetPage(
      name: emailVerificationScreen,
      page: () => const EmailVerificationScreen(),
      transitionDuration: const Duration(milliseconds: 400),
      transition: Transition.rightToLeft,
    ),
    GetPage(
      name: twoFactorSetupScreen,
      page: () => const TwoFactorSetupScreen(),
      transitionDuration: const Duration(milliseconds: 400),
      transition: Transition.rightToLeft,
    ),
    GetPage(
      name: setupBioMetricScreen,
      page: () => SetupFingerPrintScreen(),
      transitionDuration: const Duration(milliseconds: 400),
      transition: Transition.rightToLeft,
    ),
    GetPage(
      name: twoFactorScreen,
      page: () => const TwoFactorVerificationScreen(),
      transitionDuration: const Duration(milliseconds: 400),
      transition: Transition.rightToLeft,
    ),
    GetPage(
      name: kycScreen,
      page: () => const KycScreen(),
      transitionDuration: const Duration(milliseconds: 400),
      transition: Transition.rightToLeft,
    ),
    GetPage(
      name: promotionalOffersScreen,
      page: () => const PromotionalOffersScreen(),
      transitionDuration: const Duration(milliseconds: 400),
      transition: Transition.fadeIn,
    ),
    //TICKET
    GetPage(
      name: supportTicketScreen,
      page: () => const SupportTicketListScreen(),
      transitionDuration: const Duration(milliseconds: 400),
      transition: Transition.fadeIn,
    ),
    GetPage(
      name: ticketDetailsScreen,
      page: () => const TicketDetailsScreen(),
      transitionDuration: const Duration(milliseconds: 400),
      transition: Transition.fadeIn,
    ),
    GetPage(
      name: newTicketScreen,
      page: () => const NewTicketScreen(),
      transitionDuration: const Duration(milliseconds: 400),
      transition: Transition.fadeIn,
    ),
    GetPage(
      name: previewImageScreen,
      page: () => PreviewImage(url: Get.arguments),
    ),
    //TICKET END
    GetPage(
      name: faqScreen,
      page: () => const FaqScreen(),
      transitionDuration: const Duration(milliseconds: 400),
      transition: Transition.rightToLeftWithFade,
    ),
    //Modules
    //Send Money
    GetPage(
      name: sendMoneyScreen,
      transitionDuration: const Duration(milliseconds: 400),
      page: () => SendMoneyScreen(toUserInformation: Get.arguments),
      transition: Transition.fadeIn,
    ),
    GetPage(
      name: sendMoneyHistoryScreen,
      transitionDuration: const Duration(milliseconds: 400),
      page: () => const SendMoneyHistoryScreen(),
      transition: Transition.fadeIn,
    ),
    //Request Money
    GetPage(
      name: requestMoneyScreen,
      transitionDuration: const Duration(milliseconds: 400),
      page: () => const RequestMoneyScreen(),
      transition: Transition.fadeIn,
    ),
    GetPage(
      name: requestMoneyApproveScreen,
      transitionDuration: const Duration(milliseconds: 400),
      page: () => const RequestMoneyApproveScreen(),
      transition: Transition.fadeIn,
    ),
    GetPage(
      name: requestMoneyHistoryScreen,
      transitionDuration: const Duration(milliseconds: 400),
      page: () => RequestMoneyHistoryScreen(isMyRequested: Get.arguments ?? true),
      transition: Transition.fadeIn,
    ),
    //Cash out
    GetPage(
      name: cashOutScreen,
      transitionDuration: const Duration(milliseconds: 400),
      page: () => CashOutScreen(toUserInformation: Get.arguments),
      transition: Transition.fadeIn,
    ),
    GetPage(
      name: cashOutHistoryScreen,
      transitionDuration: const Duration(milliseconds: 400),
      page: () => const CashOutHistoryScreen(),
      transition: Transition.fadeIn,
    ),
    //Payment Money
    GetPage(
      name: paymentScreen,
      transitionDuration: const Duration(milliseconds: 400),
      page: () => PaymentScreen(toUserInformation: Get.arguments),
      transition: Transition.fadeIn,
    ),
    GetPage(
      name: paymentHistoryScreen,
      transitionDuration: const Duration(milliseconds: 400),
      page: () => const PaymentHistoryScreen(),
      transition: Transition.fadeIn,
    ),
    //Mobile Recharge
    GetPage(
      name: giftCardScreen,
      transitionDuration: const Duration(milliseconds: 400),
      page: () => const GiftScreen(),
      transition: Transition.fadeIn,
    ),
    GetPage(
      name: giftHistoryScreen,
      transitionDuration: const Duration(milliseconds: 400),
      page: () => const GiftHistoryScreen(),
      transition: Transition.fadeIn,
    ),
    //Airtime Recharge
    GetPage(
      name: airTimeScreen,
      transitionDuration: const Duration(milliseconds: 400),
      page: () => const AirtimeRechargeScreen(),
      transition: Transition.fadeIn,
    ),
    GetPage(
      name: airTimeHistoryScreen,
      transitionDuration: const Duration(milliseconds: 400),
      page: () => const AirtimeRechargeHistoryScreen(),
      transition: Transition.fadeIn,
    ),
    //Bill pay
    GetPage(
      name: billPayScreen,
      transitionDuration: const Duration(milliseconds: 400),
      page: () => const BillPayScreen(),
      transition: Transition.fadeIn,
    ),
    GetPage(
      name: billPayHistoryScreen,
      transitionDuration: const Duration(milliseconds: 400),
      page: () => const BillPayHistoryScreen(),
      transition: Transition.fadeIn,
    ),

    //Bank transfer
    GetPage(
      name: bankTransferScreen,
      transitionDuration: const Duration(milliseconds: 400),
      page: () => const BankTransferScreen(),
      transition: Transition.fadeIn,
    ),
    GetPage(
      name: bankTransferAddNewBankScreen,
      transitionDuration: const Duration(milliseconds: 400),
      page: () => const BankTransferAddNewBankAccountScreen(),
      transition: Transition.fadeIn,
    ),
    GetPage(
      name: bankTransferHistoryScreen,
      transitionDuration: const Duration(milliseconds: 400),
      page: () => const BankTransferHistoryScreen(),
      transition: Transition.fadeIn,
    ),
    //Education Fees
    GetPage(
      name: educationFeeScreen,
      transitionDuration: const Duration(milliseconds: 400),
      page: () => const EducationFeeScreen(),
      transition: Transition.fadeIn,
    ),
    GetPage(
      name: educationFeeHistoryScreen,
      transitionDuration: const Duration(milliseconds: 400),
      page: () => const EducationFeeHistoryScreen(),
      transition: Transition.fadeIn,
    ),
    //Donation Screen
    GetPage(
      name: donationScreen,
      transitionDuration: const Duration(milliseconds: 400),
      page: () => const DonationScreen(),
      transition: Transition.fadeIn,
    ),
    GetPage(
      name: donationHistoryScreen,
      transitionDuration: const Duration(milliseconds: 400),
      page: () => const DonationHistoryScreen(),
      transition: Transition.fadeIn,
    ),
    //Micro Finance Screen
    GetPage(
      name: microFinanceScreen,
      transitionDuration: const Duration(milliseconds: 400),
      page: () => const MicroFinanceScreen(),
      transition: Transition.fadeIn,
    ),
    GetPage(
      name: microFinanceHistoryScreen,
      transitionDuration: const Duration(milliseconds: 400),
      page: () => const MicroFinanceHistoryScreen(),
      transition: Transition.fadeIn,
    ),
    //Add money
    GetPage(
      name: addMoneyScreen,
      transitionDuration: const Duration(milliseconds: 400),
      page: () => const AddMoneyScreen(),
      transition: Transition.fadeIn,
    ),
    GetPage(
      name: addMoneyWebViewScreen,
      // transitionDuration: const Duration(milliseconds: 400),
      page: () {
        var arguments = Get.arguments as List?;
        return MyWebViewScreen(
          redirectUrl: arguments != null && arguments.isNotEmpty ? arguments[0] : "",
          successUrl: arguments != null && arguments.length > 1 ? arguments[1] : "",
          failedUrl: arguments != null && arguments.length > 2 ? arguments[2] : "",
        );
      },
      // transition: Transition.fadeIn,
    ),

    GetPage(
      name: addMoneyHistoryScreen,
      page: () => const AddMoneyHistoryScreen(),
      transitionDuration: const Duration(milliseconds: 400),
      transition: Transition.fadeIn,
    ),
    //Cards screen
    GetPage(
      name: virtualCardsScreen,
      transitionDuration: const Duration(milliseconds: 400),
      page: () => VirtualCardsScreen(),
      transition: Transition.fadeIn,
    ),
    GetPage(
      name: singleCardsScreen,
      transitionDuration: const Duration(milliseconds: 400),
      page: () => SingleCardsScreen(cardId: Get.arguments),
      transition: Transition.fadeIn,
    ),
    GetPage(
      name: createVccCardScreen,
      transitionDuration: const Duration(milliseconds: 400),
      page: () => CreateVcCardScreen(),
      transition: Transition.fadeIn,
    ),
    GetPage(
      name: investmentScreen,
      transitionDuration: const Duration(milliseconds: 400),
      page: () => InvestmentScreen(),
      transition: Transition.fadeIn,
    ),
    GetPage(
      name: investmentHistoryScreen,
      transitionDuration: const Duration(milliseconds: 400),
      page: () => InvestmentHistoryScreen(),
      transition: Transition.fadeIn,
    ),
  ];

  static Future<void> checkUserStatusAndGoToNextStep(
    UserModel? user, {
    bool isRemember = true,
    String accessToken = "",
    String tokenType = "",
  }) async {
    bool needEmailVerification = user?.ev == "1" ? false : true;
    bool needSmsVerification = user?.sv == '1' ? false : true;
    bool isTwoFactorEnable = user?.tv == '1' ? false : true;
    bool isNeedKycVerification = user?.kv == '1' ? false : true;
    bool isNeedProfileCompleteEnable = user?.profileComplete == '0' ? true : false;

    if (isRemember) {
      await SharedPreferenceService.setRememberMe(true);
    } else {
      await SharedPreferenceService.setRememberMe(false);
    }

    await SharedPreferenceService.setString(
      SharedPreferenceService.userEmailKey,
      user?.email ?? '',
    );
    await SharedPreferenceService.setString(
      SharedPreferenceService.userPhoneNumberKey,
      user?.mobile ?? '',
    );
    await SharedPreferenceService.setString(
      SharedPreferenceService.userNameKey,
      user?.username ?? '',
    );
    await SharedPreferenceService.setString(
      SharedPreferenceService.userIdKey,
      user?.id.toString() ?? '-1',
    );
    await SharedPreferenceService.setString(
      SharedPreferenceService.userFullNameKey,
      user?.getFullName() ?? '',
    );

    String imageUrl = "";
    var imageData = user?.image == null ? '' : '${user?.image}';

    if (imageData.isNotEmpty && imageData != 'null') {
      imageUrl = '${UrlContainer.domainUrl}/assets/images/user/profile/$imageData';
    }

    await SharedPreferenceService.setString(
      SharedPreferenceService.userImageKey,
      imageUrl,
    );

    if (accessToken.isNotEmpty) {
      await SharedPreferenceService.setString(
        SharedPreferenceService.accessTokenKey,
        accessToken,
      );
      await SharedPreferenceService.setString(
        SharedPreferenceService.accessTokenType,
        tokenType,
      );
      await PushNotificationService().sendUserToken();
      SharedPreferenceService.setIsLoggedIn(true);
    }

    String targetRoute = '';

    if (isNeedProfileCompleteEnable || needSmsVerification) {
      targetRoute = RouteHelper.registrationScreen;
    } else if (needEmailVerification) {
      targetRoute = RouteHelper.emailVerificationScreen;
    } else if (isTwoFactorEnable) {
      targetRoute = RouteHelper.twoFactorScreen;
    } else if (isNeedKycVerification) {
      targetRoute = RouteHelper.kycScreen;
    } else {
      targetRoute = RouteHelper.dashboardScreen;
      Get.offAllNamed(targetRoute, arguments: [true]);
      return; // Exit early if going to the dashboard
    }
    // Check and navigate if current route is different
    if (Get.currentRoute != targetRoute) {
      Get.offAllNamed(targetRoute, arguments: user);
    }
  }

  static void goToModuleRoute({String moduleName = ""}) {
    if (moduleName == "send_money") {
      Get.toNamed(RouteHelper.sendMoneyScreen);
    } else if (moduleName == "request_money") {
      Get.toNamed(RouteHelper.requestMoneyScreen);
    } else if (moduleName == "cash_out") {
      Get.toNamed(RouteHelper.cashOutScreen);
    } else if (moduleName == "make_payment") {
      Get.toNamed(RouteHelper.paymentScreen);
    } else if (moduleName == "gift_card") {
      Get.toNamed(RouteHelper.giftCardScreen);
    } else if (moduleName == "utility_bill") {
      Get.toNamed(RouteHelper.billPayScreen);
    } else if (moduleName == "bank_transfer") {
      Get.toNamed(RouteHelper.bankTransferScreen);
    } else if (moduleName == "education_fee") {
      Get.toNamed(RouteHelper.educationFeeScreen);
    } else if (moduleName == "donation") {
      Get.toNamed(RouteHelper.donationScreen);
    } else if (moduleName == "microfinance") {
      Get.toNamed(RouteHelper.microFinanceScreen);
    } else if (moduleName == "add_money") {
      Get.toNamed(RouteHelper.addMoneyScreen);
    } else if (moduleName == "virtual_card") {
      Get.toNamed(RouteHelper.virtualCardsScreen);
    } else if (moduleName == "mobile_recharge") {
      Get.toNamed(RouteHelper.airTimeScreen);
    }
  }
}
