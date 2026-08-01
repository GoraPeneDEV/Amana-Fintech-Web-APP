import 'package:flutter/cupertino.dart';

import 'package:get/get.dart';

import 'package:ovopay/app/components/snack_bar/show_custom_snackbar.dart';
import 'package:ovopay/app/screens/auth/biometric/controller/biometric_controller.dart';
import 'package:ovopay/app/screens/global/controller/country_controller.dart';
import 'package:ovopay/core/data/models/auth/login/login_response_model.dart';
import 'package:ovopay/core/data/models/auth/sign_up_model/registration_response_model.dart';
import 'package:ovopay/core/data/models/authorization/authorization_response_model.dart';
import 'package:ovopay/core/data/models/country_model/country_model.dart';
import 'package:ovopay/core/data/models/global/response_model/response_model.dart';
import 'package:ovopay/core/data/models/user/user_model.dart';
import 'package:ovopay/core/data/repositories/auth/login_repo.dart';
import 'package:ovopay/core/route/route.dart';

import '../../../../../core/data/services/service_exporter.dart';
import '../../../../../core/utils/util_exporter.dart';

class LoginController extends BioMetricController {
  LoginRepo loginRepo;
  LoginController({required this.loginRepo});
  final FocusNode emailFocusNode = FocusNode();
  final FocusNode passwordFocusNode = FocusNode();

  CountryData? countryData;
  TextEditingController countryController = TextEditingController();
  TextEditingController mobileController = TextEditingController();
  TextEditingController pinController = TextEditingController();

  String? email;
  String? password;

  List<String> errors = [];

  @override
  void onInit() {
    initializeData();

    super.onInit();
  }

  void forgetPassword() {
    Get.toNamed(RouteHelper.forgotPinScreen);
  }

  bool isSubmitLoading = false;
  void loginUser() async {
    try {
      isSubmitLoading = true;
      update();

      // Call the login API
      ResponseModel model = await loginRepo.loginUser(
        countryData?.id?.toString() ?? "-1",
        mobileController.text.toString(),
        pinController.text.toString(),
      );
      // Check if the response status code is 200 (success)
      if (model.statusCode == 200) {
        // Parse the response into the LoginResponseModel
        LoginResponseModel loginModel = LoginResponseModel.fromJson(
          model.responseJson,
        );
        if (loginModel.remark == "user_not_found") {
          await SharedPreferenceService.setRememberMe(false);
        }
        // Check if the login status is successful
        if (loginModel.status.toString().toLowerCase() == AppStatus.SUCCESS.toLowerCase()) {
          // Extract access token, token type, and user details
          String accessToken = loginModel.data?.accessToken ?? "";
          String tokenType = loginModel.data?.tokenType ?? "";
          UserModel? user = loginModel.data?.user;

          // Handle the next steps based on user status
          await RouteHelper.checkUserStatusAndGoToNextStep(
            user,
            accessToken: accessToken,
            tokenType: tokenType,
            isRemember: true,
          );
        } else {
          if (loginModel.remark == "pin_not_exists") {
            await SharedPreferenceService.setRememberMe(false);
          }
          // Show an error if login failed
          CustomSnackBar.error(
            errorList: loginModel.message ?? [MyStrings.loginFailedTryAgain],
          );
        }
      } else {
        // Show an error if the status code is not 200
        CustomSnackBar.error(errorList: [model.message]);
      }
    } catch (e, stackTrace) {
      // Handle any unexpected errors that might occur
      printE('Error during login: $e');
      printE('Stacktrace: $stackTrace');

      // Show a generic error message
      CustomSnackBar.error(errorList: [MyStrings.somethingWentWrong]);
    } finally {
      // Reset the loading state
      isSubmitLoading = false;
      update();
    }
  }

  void registerUser() async {
    try {
      isSubmitLoading = true;
      update();

      // Call the login API
      ResponseModel model = await loginRepo.registerUser(
        countryData?.id?.toString() ?? "-1",
        mobileController.text.toString(),
      );

      // Check if the response status code is 200 (success)
      if (model.statusCode == 200) {
        // Parse the response into the LoginResponseModel
        RegistrationResponseModel regModel = RegistrationResponseModel.fromJson(
          model.responseJson,
        );

        if (regModel.remark == "pin_required") {
          await SharedPreferenceService.setRememberMe(true);
          await SharedPreferenceService.setString(
            SharedPreferenceService.userPhoneNumberKey,
            mobileController.text,
          );
        }
        // // Check if the login status is successful
        if (regModel.status.toString().toLowerCase() == AppStatus.SUCCESS.toLowerCase()) {
          String accessToken = regModel.data?.accessToken ?? "";
          String tokenType = regModel.data?.tokenType ?? "";
          UserModel? user = regModel.data?.user;
          await SharedPreferenceService.setString(
            SharedPreferenceService.userPhoneNumberKey,
            user?.mobile ?? '',
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
          }
          bool needSmsVerification = user?.sv == '1' ? false : true;
          bool needProfileCompleteVerification = user?.profileComplete == '1' ? false : true;

          // Handle the next steps based on user status

          if (needSmsVerification) {
            ResponseModel authReqModel = await loginRepo.sendAuthorizationRequest();

            AuthorizationResponseModel authorizationResponseModel = AuthorizationResponseModel.fromJson(authReqModel.responseJson);
            if (authorizationResponseModel.status.toString().toLowerCase() == AppStatus.SUCCESS.toLowerCase()) {
              Get.toNamed(RouteHelper.registrationScreen, arguments: user);
            } else {
              CustomSnackBar.error(
                errorList: authorizationResponseModel.message ?? [MyStrings.loginFailedTryAgain],
              );
            }
          }
          if (needProfileCompleteVerification) {
            Get.toNamed(RouteHelper.registrationScreen, arguments: user);
          }

          // If the user chose to "remember me", save the state
        } else {
          if (regModel.remark != "pin_required") {
            // Show an error if login failed
            CustomSnackBar.error(
              errorList: regModel.message ?? [MyStrings.loginFailedTryAgain],
            );
          }
        }
      } else {
        // Show an error if the status code is not 200
        CustomSnackBar.error(errorList: [model.message]);
      }
    } catch (e, stackTrace) {
      // Handle any unexpected errors that might occur
      printE('Error during login: $e');
      printE('Stacktrace: $stackTrace');

      // Show a generic error message
      CustomSnackBar.error(errorList: [MyStrings.somethingWentWrong]);
    } finally {
      // Reset the loading state
      isSubmitLoading = false;
      update();
    }
  }

  void bioMetricLogin() {
    // Biometric login code goes here
    SharedPreferenceService.setIsLoggedIn(true);
    Get.offAllNamed(RouteHelper.dashboardScreen);
  }

  void clearTextField() {
    pinController.text = '';
    mobileController.text = '';
    update();
  }

  CountryController countryDataController = CountryController();
  void initializeData() {
    mobileController.text = SharedPreferenceService.getUserPhoneNumber();
    //Country data
    countryDataController.initialize();
    if (countryDataController.filteredCountries.isNotEmpty) {
      countryData = countryDataController.selectedCountryData ?? countryDataController.filteredCountries.first;
      countryController.text = countryData?.name ?? "";
      SharedPreferenceService.setSelectedOperatingCountry(
        countryData ?? CountryData(),
      );
    }
    update();
  }

  void selectedCountryData(CountryData value) {
    countryData = value;
    countryController.text = value.name ?? "";
    SharedPreferenceService.setSelectedOperatingCountry(value);
    update();
  }
}
