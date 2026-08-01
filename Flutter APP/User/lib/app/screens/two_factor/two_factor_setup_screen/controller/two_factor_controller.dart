import 'dart:convert';

import 'package:flutter/material.dart';
import 'package:get/get.dart';
import 'package:ovopay/app/screens/profile_and_settings_screen/controller/profile_controller.dart';
import 'package:ovopay/core/data/models/auth/two_factor/two_factor_data_model.dart';
import 'package:ovopay/core/data/repositories/auth/two_factor_repo.dart';

import '../../../../../core/data/models/authorization/authorization_response_model.dart';
import '../../../../../core/data/models/global/response_model/response_model.dart';
import '../../../../../core/utils/util_exporter.dart';
import '../../../../components/snack_bar/show_custom_snackbar.dart';

class TwoFactorController extends GetxController {
  TwoFactorRepo repo;
  TwoFactorController({required this.repo});

  bool submitLoading = false;
  String currentText = '';

  void onOtpBoxValueChange(String value) {
    currentText = value;
    update();
  }

  void verify2FACode({required void Function() onSuccess}) async {
    if (currentText.isEmpty) {
      CustomSnackBar.error(errorList: [MyStrings.otpFieldEmptyMsg]);
      return;
    }

    try {
      submitLoading = true;
      update();

      ResponseModel responseModel = await repo.verify(currentText);

      if (responseModel.statusCode == 200) {
        AuthorizationResponseModel model = AuthorizationResponseModel.fromJson(
          responseModel.responseJson,
        );

        if (model.status == AppStatus.SUCCESS) {
          onSuccess();
        } else {
          CustomSnackBar.error(
            errorList: model.message ?? [MyStrings.requestFail],
          );
        }
      } else {
        CustomSnackBar.error(errorList: [responseModel.message]);
      }
    } catch (e) {
      printE(e);
    }
    submitLoading = false;
    update();
  }

  void enable2fa(
    String key,
    String code, {
    required VoidCallback onSuccess,
  }) async {
    if (code.isEmpty) {
      CustomSnackBar.error(errorList: [MyStrings.otpFieldEmptyMsg]);
      return;
    }

    submitLoading = true;
    update();

    ResponseModel responseModel = await repo.enable2fa(key, code);

    if (responseModel.statusCode == 200) {
      AuthorizationResponseModel model = AuthorizationResponseModel.fromJson(
        responseModel.responseJson,
      );
      if (model.status.toString() == AppStatus.SUCCESS.toString().toLowerCase()) {
        // CustomSnackBar.success(successList: model.message ?? [MyStrings.requestSuccess]);
        onSuccess();
        await Get.find<ProfileController>().loadProfileInfo();
      } else {
        CustomSnackBar.error(
          errorList: model.message ?? [MyStrings.requestFail],
        );
      }
    } else {
      CustomSnackBar.error(errorList: [responseModel.message]);
    }
    submitLoading = false;
    update();
  }

  void disable2fa(String code) async {
    if (code.isEmpty) {
      CustomSnackBar.error(errorList: [MyStrings.otpFieldEmptyMsg]);
      return;
    }

    submitLoading = true;
    update();

    ResponseModel responseModel = await repo.disable2fa(code);

    if (responseModel.statusCode == 200) {
      AuthorizationResponseModel model = AuthorizationResponseModel.fromJson(
        responseModel.responseJson,
      );

      if (model.status.toString() == AppStatus.SUCCESS.toString().toLowerCase()) {
        CustomSnackBar.success(
          successList: model.message ?? [MyStrings.requestSuccess],
        );
        await Get.find<ProfileController>().loadProfileInfo();
      } else {
        CustomSnackBar.error(
          errorList: model.message ?? [MyStrings.requestFail],
        );
      }
    } else {
      CustomSnackBar.error(errorList: [responseModel.message]);
    }
    submitLoading = false;
    update();
  }

  bool isLoading = false;
  TwoFactorCodeModel twoFactorCodeModel = TwoFactorCodeModel();

  void get2FaCode() async {
    isLoading = true;
    update();

    ResponseModel responseModel = await repo.get2FaData();

    if (responseModel.statusCode == 200) {
      TwoFactorCodeModel model = twoFactorCodeModelFromJson(
        jsonEncode(responseModel.responseJson),
      );

      if (model.status.toString() == AppStatus.SUCCESS.toString().toLowerCase()) {
        twoFactorCodeModel = model;
        isLoading = false;
        update();
      } else {
        CustomSnackBar.error(
          errorList: model.message ?? [MyStrings.requestFail],
        );
      }
    } else {
      CustomSnackBar.error(errorList: [responseModel.message]);
    }
    isLoading = false;
    update();
  }
}
