import 'dart:async';
import 'package:flutter/widgets.dart';
import 'package:get/get.dart';
import 'package:ovopay/core/data/models/authorization/authorization_response_model.dart';
import 'package:ovopay/core/data/models/global/response_model/response_model.dart';
import 'package:ovopay/core/data/repositories/opt_verification_repo/opt_verification_repo.dart';
import 'package:ovopay/core/data/services/service_exporter.dart';
import 'package:ovopay/environment.dart';
import '../../../../app/components/snack_bar/show_custom_snackbar.dart';
import '../../../utils/util_exporter.dart';

class OtpVerificationController extends GetxController {
  OtpVerificationRepo repo = OtpVerificationRepo();
  OtpVerificationController();
  TextEditingController otpController = TextEditingController();
  bool submitLoading = false;
  bool resendLoading = false;
  String actionRemark = '';
  String nextRoute = '';
  String otpType = '';
  bool startTime = false;
  Timer? _timer;

  @override
  void onClose() {
    _timer?.cancel();
    super.onClose();
  }

  void initializeOtpSteps(String actionRemarkValue, String otpType) {
    actionRemark = actionRemarkValue;
    this.otpType = otpType;
    int expirationTime = SharedPreferenceService.getOtpExpireDuration();
    time = expirationTime;
    startTime = true;

    // Start the countdown timer
    startCountdownTimer();
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
      int expirationTime = SharedPreferenceService.getOtpExpireDuration();
      time = expirationTime;
      // Restart the timer if OTP is not expired
      startCountdownTimer();
    } else {
      time = 0;
      _timer?.cancel();
    }
    update();
  }

  Future<void> verifyOtp({Function(dynamic v1)? onSuccess}) async {
    if (otpController.text.trim().isEmpty) {
      CustomSnackBar.error(errorList: [MyStrings.otpFieldEmptyMsg.tr]);
      return;
    }
    submitLoading = true;
    update();
    ResponseModel responseModel = await repo.verify(
      otpController.text,
      actionRemark,
    );
    if (responseModel.statusCode == 200) {
      AuthorizationResponseModel model = AuthorizationResponseModel.fromJson(
        (responseModel.responseJson),
      );
      if (model.status?.toLowerCase() == AppStatus.SUCCESS.toLowerCase()) {
        // if (nextRoute.isNotEmpty) {
        //   Get.offAndToNamed(nextRoute, arguments: [responseModel]);
        // } else {
        //   Get.back();
        // }
        onSuccess!(responseModel.responseJson);
        // CustomSnackBar.success(successList: model.message ?? [(MyStrings.requestSuccess)]);
      } else {
        CustomSnackBar.error(
          errorList: model.message ?? [(MyStrings.requestFail)],
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
      ResponseModel response = await repo.resendVerifyCode(actionRemark);
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

  void onChangeOtpWidgetText({required String value}) {
    otpController.text = value;
    update();
  }
}
