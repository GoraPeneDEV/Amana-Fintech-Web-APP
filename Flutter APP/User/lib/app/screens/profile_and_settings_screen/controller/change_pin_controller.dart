import 'package:flutter/cupertino.dart';
import 'package:get/get.dart';
import 'package:ovopay/app/components/snack_bar/show_custom_snackbar.dart';
import 'package:ovopay/core/data/models/authorization/authorization_response_model.dart';
import 'package:ovopay/core/data/models/global/response_model/response_model.dart';

import 'package:ovopay/core/data/repositories/account/change_password_repo.dart';

import '../../../../core/utils/util_exporter.dart';

class ChangePinController extends GetxController {
  ChangePasswordRepo changePasswordRepo;
  ChangePinController({required this.changePasswordRepo});

  String? currentPass, password, confirmPass;

  bool isLoading = false;
  List<String> errors = [];

  TextEditingController passController = TextEditingController();
  TextEditingController currentPassController = TextEditingController();
  TextEditingController confirmPassController = TextEditingController();

  FocusNode currentPassFocusNode = FocusNode();
  FocusNode passwordFocusNode = FocusNode();
  FocusNode confirmPassFocusNode = FocusNode();

  void addError({required String error}) {
    if (!errors.contains(error)) {
      errors.add(error);
      update();
    }
  }

  void removeError({required String error}) {
    if (errors.contains(error)) {
      errors.remove(error);
      update();
    }
  }

  bool submitLoading = false;
  Future<void> changePassword({required VoidCallback onSuccess}) async {
    String currentPass = currentPassController.text.toString();
    String password = passController.text.toString();

    try {
      submitLoading = true;
      update();
      ResponseModel responseModel = await changePasswordRepo.changePassword(
        currentPass,
        password,
      );

      if (responseModel.statusCode == 200) {
        AuthorizationResponseModel model = AuthorizationResponseModel.fromJson(
          responseModel.responseJson,
        );
        if (model.status?.toLowerCase() == AppStatus.SUCCESS.toLowerCase()) {
          currentPassController.clear();
          passController.clear();
          confirmPassController.clear();

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

  void clearData() {
    isLoading = false;
    errors.clear();
    currentPassController.text = '';
    passController.text = '';
    confirmPassController.text = '';
  }
}
