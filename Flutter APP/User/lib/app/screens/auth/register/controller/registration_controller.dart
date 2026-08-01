import 'dart:async';

import 'package:flutter/material.dart';
import 'package:get/get.dart';
import 'package:ovopay/app/components/snack_bar/show_custom_snackbar.dart';
import 'package:ovopay/core/data/models/auth/sign_up_model/registration_response_model.dart';
import 'package:ovopay/core/data/models/authorization/authorization_response_model.dart';
import 'package:ovopay/core/data/models/global/response_model/response_model.dart';
import 'package:ovopay/core/data/models/profile_complete/profile_complete_post_model.dart';
import 'package:ovopay/core/data/models/user/user_model.dart';

import 'package:ovopay/core/data/repositories/auth/signup_repo.dart';
import 'package:ovopay/environment.dart';

import '../../../../../core/data/services/service_exporter.dart';
import '../../../../../core/route/route.dart';
import '../../../../../core/utils/util_exporter.dart';

class RegistrationController extends GetxController {
  RegistrationRepo registrationRepo;

  RegistrationController({required this.registrationRepo});

  bool isLoading = true;

  final TextEditingController otpController = TextEditingController();

  final TextEditingController pinController = TextEditingController();
  final TextEditingController cPinController = TextEditingController();

  final TextEditingController uNameController = TextEditingController();
  final TextEditingController emailController = TextEditingController();
  final TextEditingController fNameController = TextEditingController();
  final TextEditingController lNameController = TextEditingController();

  final TextEditingController addressController = TextEditingController();
  final TextEditingController stateController = TextEditingController();
  final TextEditingController zipCodeController = TextEditingController();
  final TextEditingController cityController = TextEditingController();

  bool submitLoading = false;
  bool startTime = false;
  Timer? _timer;
  @override
  void onClose() {
    _timer?.cancel();
    super.onClose();
  }

  //VERIFY SMS START
  void onChangeOtpWidgetText({String value = ""}) {
    otpController.text = value;
    update();
  }

  Future<void> sendAuthorizeCode() async {
    isLoading = true;
    update();
    await registrationRepo.sendAuthorizationRequest();

    int expirationTime = Environment.otpResendDuration;
    time = expirationTime;
    startTime = true;

    // Start the countdown timer
    startCountdownTimer();
    update();

    isLoading = false;
    update();
    return;
  }

  Future<void> verifyYourSms({required void Function() onSuccess}) async {
    if (otpController.text.trim().isEmpty) {
      CustomSnackBar.error(errorList: [MyStrings.otpFieldEmptyMsg.tr]);
      return;
    }

    submitLoading = true;
    update();

    try {
      ResponseModel responseModel = await registrationRepo.verifySmsOtp(
        otpController.text,
      );

      if (responseModel.statusCode == 200) {
        AuthorizationResponseModel model = AuthorizationResponseModel.fromJson(
          responseModel.responseJson,
        );

        if (model.status == AppStatus.SUCCESS) {
          CustomSnackBar.success(
            successList: model.message ?? ['${MyStrings.sms.tr} ${MyStrings.verificationSuccess.tr}'],
          );
          UserModel? userModel = model.data?.user;
          if (userModel != null) {
            await SharedPreferenceService.setString(
              SharedPreferenceService.userIdKey,
              userModel.id.toString(),
            );

            await SharedPreferenceService.setString(
              SharedPreferenceService.userEmailKey,
              userModel.email ?? '',
            );
            await SharedPreferenceService.setString(
              SharedPreferenceService.userNameKey,
              userModel.username ?? '',
            );
            await SharedPreferenceService.setString(
              SharedPreferenceService.userPhoneNumberKey,
              userModel.mobile ?? '',
            );

            bool isNeedProfileCompleteEnable = userModel.profileComplete == '0' ? true : false;
            bool needEmailVerification = userModel.ev == "1" ? false : true;
            bool isTwoFactorEnable = userModel.tv == '1' ? false : true;
            bool isNeedKycVerification = userModel.kv == '1' ? false : true;

            if (isNeedProfileCompleteEnable == false) {
              if (needEmailVerification) {
                Get.offAllNamed(RouteHelper.emailVerificationScreen);
              } else if (isTwoFactorEnable) {
                Get.offAllNamed(RouteHelper.twoFactorScreen);
              } else if (isNeedKycVerification) {
                Get.offAllNamed(RouteHelper.kycScreen);
              } else {
                Get.offAllNamed(RouteHelper.dashboardScreen, arguments: [true]);
              }
            } else {
              onSuccess();
            }
          }
        } else {
          CustomSnackBar.error(
            errorList: model.message ?? ['${MyStrings.sms.tr} ${MyStrings.verificationFailed}'],
          );
        }
      } else {
        CustomSnackBar.error(errorList: [responseModel.message]);
      }
    } catch (e) {
      CustomSnackBar.error(errorList: [e.toString()]);
    }

    submitLoading = false;
    update();
  }

  bool resendLoading = false;
  Future<void> resendOtp() async {
    resendLoading = true;
    update();
    try {
      ResponseModel response = await registrationRepo.resendVerifyCode(
        isEmail: false,
      );
      if (response.statusCode == 200) {
        AuthorizationResponseModel model = AuthorizationResponseModel.fromJson(
          (response.responseJson),
        );
        if (model.status?.toLowerCase() == 'success') {
          otpController.text = "";
          CustomSnackBar.success(
            successList: model.message ?? [MyStrings.successfullyCodeResend],
          );
          makeOtpExpired(false);
        } else {
          CustomSnackBar.error(
            errorList: model.message ?? [MyStrings.resendCodeFail],
          );
        }
      } else {
        CustomSnackBar.error(errorList: [response.message]);
      }
    } catch (e) {
      printE(e);
    }
    resendLoading = false;
    update();
  }

  //VERIFY SMS END

  //PROFILE COMPLETE SEction
  bool submitProfileCompleteLoading = false;
  Future<void> profileCompleteSubmit() async {
    submitProfileCompleteLoading = true;
    update();
    try {
      ProfileCompletePostModel model = ProfileCompletePostModel(
        email: emailController.text,
        username: uNameController.text,
        firstName: fNameController.text,
        lastName: lNameController.text,
        address: addressController.text,
        state: stateController.text,
        zip: zipCodeController.text,
        city: cityController.text,
        image: null,
        pin: pinController.text,
        cPin: pinController.text,
      );

      ResponseModel responseModel = await registrationRepo.completeProfile(
        model,
      );

      if (responseModel.statusCode == 200) {
        RegistrationResponseModel model = RegistrationResponseModel.fromJson(
          responseModel.responseJson,
        );
        if (model.status?.toLowerCase() == AppStatus.SUCCESS.toLowerCase()) {
          RouteHelper.checkUserStatusAndGoToNextStep(
            model.data?.user,
            accessToken: model.data?.accessToken ?? "",
            isRemember: true,
          );
        } else {
          CustomSnackBar.error(
            errorList: model.message ?? [MyStrings.requestFail],
          );
        }
      } else {
        CustomSnackBar.error(errorList: [responseModel.message]);
      }
    } catch (e) {
      printE(e.toString());
    } finally {
      submitProfileCompleteLoading = false;
      update();
    }
  }

  void startCountdownTimer() {
    // Cancel any existing timer
    _timer?.cancel();

    // Create a new timer that fires every second
    _timer = Timer.periodic(const Duration(seconds: 1), (timer) {
      if (time > 0) {
        time--;
        update();
      } else {
        printE("object");
        // Timer reached zero, mark OTP as expired
        makeOtpExpired(true);
        timer.cancel();
      }
    });
  }

  bool isOtpExpired = false;
  int time = Environment.otpResendDuration;

  void makeOtpExpired(bool status) {
    isOtpExpired = status;
    if (status == false) {
      int expirationTime = Environment.otpResendDuration;
      time = expirationTime;
      // Restart the timer if OTP is not expired
      startCountdownTimer();
    } else {
      time = 0;
      _timer?.cancel();
    }
    update();
  }
}
