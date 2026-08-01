import 'dart:async';
import 'package:flutter/material.dart';
import 'package:get/get.dart';
import 'package:ovopay/app/components/snack_bar/show_custom_snackbar.dart';
import 'package:ovopay/core/data/models/authorization/authorization_response_model.dart';
import 'package:ovopay/core/data/models/global/response_model/response_model.dart';
import 'package:ovopay/core/data/models/user/user_model.dart';
import 'package:ovopay/core/data/repositories/auth/sms_email_verification_repo.dart';
import 'package:ovopay/environment.dart';

import '../../../../../core/utils/util_exporter.dart';

class EmailVerificationController extends GetxController {
  SmsEmailVerificationRepo repo;
  EmailVerificationController({required this.repo});

  bool needTwoFactor = false;
  bool submitLoading = false;
  bool isLoading = true;
  bool resendLoading = false;

  bool startTime = false;
  Timer? _timer;
  @override
  void onClose() {
    _timer?.cancel();
    super.onClose();
  }

  final TextEditingController otpController = TextEditingController();

  void onOtpBoxValueChange(String value) {
    otpController.text = value;
    update();
  }

  Future<void> sendAuthorizeCode() async {
    isLoading = true;
    update();
    await repo.sendAuthorizationRequest();

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

  UserModel? userModel;
  Future verifyYourEmail({required void Function() onSuccess}) async {
    if (otpController.text.trim().isEmpty) {
      CustomSnackBar.error(errorList: [MyStrings.otpFieldEmptyMsg]);
      return;
    }

    submitLoading = true;
    update();

    ResponseModel responseModel = await repo.verify(
      otpController.text,
      isEmail: true,
    );

    if (responseModel.statusCode == 200) {
      AuthorizationResponseModel model = AuthorizationResponseModel.fromJson(
        responseModel.responseJson,
      );
      userModel = model.data?.user;
      update();

      if (model.status == AppStatus.SUCCESS) {
        onSuccess();
      } else {
        CustomSnackBar.error(
          errorList: model.message ?? [(MyStrings.emailVerificationFailed)],
        );
      }
    } else {
      CustomSnackBar.error(errorList: [responseModel.message]);
    }

    submitLoading = false;
    update();
  }

  Future<void> resendOtp() async {
    resendLoading = true;
    update();
    try {
      ResponseModel response = await repo.resendVerifyCode(isEmail: true);
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
