import 'dart:convert';

import 'package:flutter/material.dart';
import 'package:get/get.dart';
import 'package:ovopay/app/components/snack_bar/show_custom_snackbar.dart';
import 'package:ovopay/core/data/models/authorization/authorization_response_model.dart';
import 'package:ovopay/core/data/models/global/charges/global_charge_model.dart';
import 'package:ovopay/core/data/models/global/response_model/response_model.dart';
import 'package:ovopay/core/data/models/modules/send_money/send_money_history_response_model.dart';
import 'package:ovopay/core/data/models/modules/send_money/send_money_response_model.dart';
import 'package:ovopay/core/data/models/user/user_model.dart';
import 'package:ovopay/core/data/repositories/modules/send_money/send_money_repo.dart';

import '../../../../core/data/services/service_exporter.dart';
import '../../../../core/utils/util_exporter.dart';

class SendMoneyController extends GetxController {
  SendMoneyRepo sendMoneyRepo;
  SendMoneyController({required this.sendMoneyRepo});

  bool isPageLoading = true;

  TextEditingController phoneNumberOrUserNameController = TextEditingController();
  TextEditingController amountController = TextEditingController();
  TextEditingController pinController = TextEditingController();

  // Get Phone or username or Amount
  String get getPhoneOrUsername => phoneNumberOrUserNameController.text;
  String get getAmount => amountController.text;
  //Otp Type
  List<String> otpType = [];
  String selectedOtpType = "";
  //current balance
  double userCurrentBalance = 0.0;
  //Charge
  GlobalChargeModel? globalChargeModel;
  //Latest send money history
  List<LatestSendMoneyHistory> latestSendMoneyHistory = [];
  //Check user exist
  bool isUserExist = false;
  UserModel? existUserModel;
  //Send Money Success Model
  SendMoneySubmitInfoModel? sendMoneySubmitInfoModel;
  //Action ID
  String actionRemark = "send_money";

  Future initController() async {
    isPageLoading = true;
    update();
    await loadSendMoneyInfo();
    isPageLoading = false;
    update();
  }

  //Informations
  Future<void> loadSendMoneyInfo() async {
    try {
      ResponseModel responseModel = await sendMoneyRepo.sendMoneyInfoData();
      if (responseModel.statusCode == 200) {
        final sendMoneyResponseModel = sendMoneyResponseModelFromJson(
          jsonEncode(responseModel.responseJson),
        );
        if (sendMoneyResponseModel.status == "success") {
          final data = sendMoneyResponseModel.data;
          if (data != null) {
            userCurrentBalance = data.getCurrentBalance();

            otpType = data.otpType ?? [];
            globalChargeModel = data.sendMoneyCharge;
            if (data.latestSendMoneyHistory != null) {
              latestSendMoneyHistory = data.latestSendMoneyHistory ?? [];
            }
            update();
          }
        } else {
          CustomSnackBar.error(
            errorList: sendMoneyResponseModel.message ?? [MyStrings.somethingWentWrong],
          );
        }
      } else {
        CustomSnackBar.error(errorList: [responseModel.message]);
      }
    } catch (e) {
      printE(e.toString());
    }
  }

  //Check if the user exists
  bool isCheckingUserLoading = false;
  Future<void> checkUserExist(
    VoidCallback onSuccessCallback, {
    String inputUserNameOrPhone = "",
  }) async {
    try {
      isCheckingUserLoading = true;
      update();
      // Use the provided input or default to the text in the controller
      final userInput = (inputUserNameOrPhone.isEmpty ? phoneNumberOrUserNameController.text : inputUserNameOrPhone).removeSpecialCharacters();

      // Determine if the input is a phone number or a username
      final processedInput = MyUtils.checkTextIsOnlyNumber(userInput) ? userInput.toFormattedPhoneNumber(digitsFromEnd: 100) : userInput;
      ResponseModel responseModel = await sendMoneyRepo.checkUserExist(
        usernameOrPhone: processedInput,
      );
      if (responseModel.statusCode == 200) {
        AuthorizationResponseModel authorizationResponseModel = AuthorizationResponseModel.fromJson(responseModel.responseJson);

        if (authorizationResponseModel.status == "success") {
          existUserModel = authorizationResponseModel.data?.user;
          if (existUserModel != null) {
            isUserExist = true;
            onSuccessCallback();
          }
        } else {
          isUserExist = false;
          CustomSnackBar.error(
            errorList: authorizationResponseModel.message ?? [MyStrings.somethingWentWrong],
          );
        }
        update();
      } else {
        CustomSnackBar.error(errorList: [responseModel.message]);
      }
    } catch (e) {
      printE(e.toString());
    } finally {
      isCheckingUserLoading = false;
      update();
    }
  }

  Future<void> setUserFromQrScan(
    VoidCallback onSuccessCallback, {
    UserModel? existUserDataModel,
  }) async {
    try {
      isCheckingUserLoading = true;
      update();

      if (existUserDataModel != null) {
        if (existUserDataModel.username != SharedPreferenceService.getUserName()) {
          existUserModel = existUserDataModel;
          isUserExist = true;
          onSuccessCallback();
        } else {
          CustomSnackBar.error(
            errorList: [MyStrings.youCannotPerformThisActionOnYourself],
          );
          isUserExist = false;
        }
      } else {
        isUserExist = false;
      }
    } catch (e) {
      printE(e.toString());
    } finally {
      isCheckingUserLoading = false;
      update();
    }
  }

  //Select Otp type
  void selectAnOtpType(String otpType) {
    selectedOtpType = otpType;
    update();
  }

  String getOtpType(String value) {
    return value == "email"
        ? MyStrings.email.tr
        : value == "sms"
            ? MyStrings.phone.tr
            : "";
  }

  //Amount text changes
  void onChangeAmountControllerText(String value) {
    amountController.text = value;
    changeInfoWidget();
    update();
  }

  //Charge calculation

  double mainAmount = 0;
  String totalCharge = "";
  String payableAmountText = "";

  void changeInfoWidget() {
    mainAmount = double.tryParse(amountController.text) ?? 0.0;
    update();
    double percent = double.tryParse(globalChargeModel?.percentCharge ?? "0") ?? 0;
    double percentCharge = mainAmount * percent / 100;

    double fixedCharge = double.tryParse(globalChargeModel?.fixedCharge ?? "0") ?? 0;
    double tempTotalCharge = percentCharge + fixedCharge;

    double capAmount = double.tryParse(globalChargeModel?.cap ?? "0") ?? 0;

    if (capAmount != -1.0 && capAmount != 1 && tempTotalCharge > capAmount) {
      tempTotalCharge = capAmount;
    }

    totalCharge = AppConverter.formatNumber('$tempTotalCharge', precision: 2);
    double payable = tempTotalCharge + mainAmount;
    payableAmountText = payableAmountText.length > 5 ? AppConverter.roundDoubleAndRemoveTrailingZero(payable.toString()) : AppConverter.formatNumber(payable.toString());
    update();
  }
  //Charge calculation end

  //Submit

  bool isSubmitLoading = false;
  Future<void> submitThisProcess({
    void Function(SendMoneyResponseModel)? onSuccessCallback,
    void Function(SendMoneyResponseModel)? onVerifyOtpCallback,
  }) async {
    try {
      isSubmitLoading = true;
      update();
      ResponseModel responseModel = await sendMoneyRepo.sendMoneyRequest(
        user: existUserModel?.username ?? phoneNumberOrUserNameController.text,
        amount: amountController.text,
        otpType: selectedOtpType,
      );
      if (responseModel.statusCode == 200) {
        SendMoneyResponseModel sendMoneyResponseModel = SendMoneyResponseModel.fromJson(responseModel.responseJson);

        if (sendMoneyResponseModel.status == "success") {
          if (sendMoneyResponseModel.remark == "otp") {
            if (onVerifyOtpCallback != null) {
              onVerifyOtpCallback(sendMoneyResponseModel);
            }
            update();
          } else {
            if (sendMoneyResponseModel.remark == "pin") {
              if (onSuccessCallback != null) {
                onSuccessCallback(sendMoneyResponseModel);
              }
            }
          }
        } else {
          CustomSnackBar.error(
            errorList: sendMoneyResponseModel.message ?? [MyStrings.somethingWentWrong],
          );
        }
        update();
      } else {
        CustomSnackBar.error(errorList: [responseModel.message]);
      }
    } catch (e) {
      printE(e.toString());
    } finally {
      isSubmitLoading = false;
      update();
    }
  }

  Future<void> pinVerificationProcess({
    void Function(SendMoneyResponseModel)? onSuccessCallback,
  }) async {
    try {
      isSubmitLoading = true;
      update();
      ResponseModel responseModel = await sendMoneyRepo.pinVerificationRequest(
        pin: pinController.text,
      );
      if (responseModel.statusCode == 200) {
        SendMoneyResponseModel sendMoneyResponseModel = SendMoneyResponseModel.fromJson(responseModel.responseJson);

        if (sendMoneyResponseModel.status == "success") {
          sendMoneySubmitInfoModel = sendMoneyResponseModel.data?.sendMoney;
          if (sendMoneySubmitInfoModel != null) {
            if (onSuccessCallback != null) {
              onSuccessCallback(sendMoneyResponseModel);
            }
          }
        } else {
          CustomSnackBar.error(
            errorList: sendMoneyResponseModel.message ?? [MyStrings.somethingWentWrong],
          );
        }
        update();
      } else {
        CustomSnackBar.error(errorList: [responseModel.message]);
      }
    } catch (e) {
      printE(e.toString());
    } finally {
      isSubmitLoading = false;
      update();
    }
  }
  //Submit end
  //History

  int currentIndex = 0;
  void initialHistoryData() async {
    isHistoryLoading = true;
    page = 0;
    nextPageUrl = null;
    sendMoneyHistoryList.clear();

    await getSendMoneyHistoryDataList();
  }

  bool isHistoryLoading = false;
  int page = 1;
  String? nextPageUrl;
  List<LatestSendMoneyHistory> sendMoneyHistoryList = [];
  Future<void> getSendMoneyHistoryDataList({bool forceLoad = true}) async {
    try {
      page = page + 1;
      isHistoryLoading = forceLoad;
      update();
      ResponseModel responseModel = await sendMoneyRepo.sendMoneyHistory(page);
      if (responseModel.statusCode == 200) {
        final sendMoneyHistoryResponseModel = sendMoneyHistoryResponseModelFromJson(
          jsonEncode(responseModel.responseJson),
        );
        if (sendMoneyHistoryResponseModel.status == "success") {
          nextPageUrl = sendMoneyHistoryResponseModel.data?.sendMoneysHistory?.nextPageUrl;
          sendMoneyHistoryList.addAll(
            sendMoneyHistoryResponseModel.data?.sendMoneysHistory?.data ?? [],
          );
        } else {
          CustomSnackBar.error(
            errorList: sendMoneyHistoryResponseModel.message ?? [MyStrings.somethingWentWrong],
          );
        }
        update();
        isHistoryLoading = false;
        update();
      } else {
        CustomSnackBar.error(errorList: [responseModel.message]);
      }
    } catch (e) {
      printE(e.toString());
    }
    isHistoryLoading = false;
    update();
  }

  bool hasNext() {
    return nextPageUrl != null && nextPageUrl!.isNotEmpty && nextPageUrl != 'null' ? true : false;
  }

  //History end
}
