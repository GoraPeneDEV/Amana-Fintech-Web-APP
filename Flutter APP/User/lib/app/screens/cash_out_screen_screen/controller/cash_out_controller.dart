import 'dart:convert';

import 'package:flutter/material.dart';
import 'package:get/get.dart';
import 'package:ovopay/app/components/snack_bar/show_custom_snackbar.dart';
import 'package:ovopay/core/data/models/authorization/authorization_response_model.dart';
import 'package:ovopay/core/data/models/global/charges/global_charge_model.dart';
import 'package:ovopay/core/data/models/global/response_model/response_model.dart';
import 'package:ovopay/core/data/models/modules/cash_out/cash_out_history_response_model.dart';
import 'package:ovopay/core/data/models/modules/cash_out/cash_out_response_model.dart';
import 'package:ovopay/core/data/models/user/user_model.dart';
import 'package:ovopay/core/data/repositories/modules/cash_out/cash_out_repo.dart';

import '../../../../core/utils/util_exporter.dart';

class CashOutController extends GetxController {
  CashOutRepo cashOutRepo;
  CashOutController({required this.cashOutRepo});

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
  List<LatestCashOutHistory> latestCashOutHistory = [];
  //Check user exist
  bool isAgentExist = false;
  UserModel? existAgentModel;
  //Send Money Success Model
  CashOutSubmitInfo? cashOutSubmitInfoModel;
  //Action ID
  String actionRemark = "cash_out";

  Future initController() async {
    isPageLoading = true;
    update();
    await loadCashOutInfo();
    isPageLoading = false;
    update();
  }

  //Informations
  Future<void> loadCashOutInfo() async {
    try {
      ResponseModel responseModel = await cashOutRepo.cashOutInfoData();
      if (responseModel.statusCode == 200) {
        final cashOutResponseModel = cashOutResponseModelFromJson(
          jsonEncode(responseModel.responseJson),
        );
        if (cashOutResponseModel.status == "success") {
          final data = cashOutResponseModel.data;
          if (data != null) {
            userCurrentBalance = data.getCurrentBalance();

            otpType = data.otpType ?? [];
            globalChargeModel = data.cashOutCharge;
            if (data.latestCashOutHistory != null) {
              latestCashOutHistory = data.latestCashOutHistory ?? [];
            }
            update();
          }
        } else {
          CustomSnackBar.error(
            errorList: cashOutResponseModel.message ?? [MyStrings.somethingWentWrong],
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
  Future<void> checkAgentExist(
    VoidCallback onSuccessCallback, {
    String inputUserNameOrPhone = "",
  }) async {
    try {
      isCheckingUserLoading = true;
      update();
      // Use the provided input or default to the text in the controller
      final userInput = (inputUserNameOrPhone.isEmpty ? phoneNumberOrUserNameController.text : inputUserNameOrPhone).removeSpecialCharacters();

      // Determine if the input is a phone number or a username
      final processedInput = MyUtils.checkTextIsOnlyNumber(userInput) ? userInput : userInput;
      ResponseModel responseModel = await cashOutRepo.checkAgentExist(
        usernameOrPhone: processedInput,
      );
      if (responseModel.statusCode == 200) {
        AuthorizationResponseModel authorizationResponseModel = AuthorizationResponseModel.fromJson(responseModel.responseJson);

        if (authorizationResponseModel.status == "success") {
          existAgentModel = authorizationResponseModel.data?.agent;
          if (existAgentModel != null) {
            isAgentExist = true;
            onSuccessCallback();
          }
        } else {
          isAgentExist = false;
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

  Future<void> setAgentFromQrScan(
    VoidCallback onSuccessCallback, {
    UserModel? existUserModel,
  }) async {
    try {
      isCheckingUserLoading = true;
      update();

      if (existUserModel != null) {
        existAgentModel = existUserModel;

        isAgentExist = true;
        onSuccessCallback();
      } else {
        isAgentExist = false;
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
    void Function(CashOutResponseModel)? onSuccessCallback,
    void Function(CashOutResponseModel)? onVerifyOtpCallback,
  }) async {
    try {
      isSubmitLoading = true;
      update();
      ResponseModel responseModel = await cashOutRepo.cashOutRequest(
        user: existAgentModel?.username ?? phoneNumberOrUserNameController.text,
        amount: amountController.text,
        pin: pinController.text,
        otpType: selectedOtpType,
      );
      if (responseModel.statusCode == 200) {
        CashOutResponseModel cashOutResponseModel = CashOutResponseModel.fromJson(responseModel.responseJson);

        if (cashOutResponseModel.status == "success") {
          if (cashOutResponseModel.remark == "otp") {
            if (onVerifyOtpCallback != null) {
              onVerifyOtpCallback(cashOutResponseModel);
            }
            update();
          } else {
            if (cashOutResponseModel.remark == "pin") {
              if (onSuccessCallback != null) {
                onSuccessCallback(cashOutResponseModel);
              }
            }
          }
        } else {
          CustomSnackBar.error(
            errorList: cashOutResponseModel.message ?? [MyStrings.somethingWentWrong],
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
    void Function(CashOutResponseModel)? onSuccessCallback,
  }) async {
    try {
      isSubmitLoading = true;
      update();
      ResponseModel responseModel = await cashOutRepo.pinVerificationRequest(
        pin: pinController.text,
      );
      if (responseModel.statusCode == 200) {
        CashOutResponseModel cashOutResponseModel = CashOutResponseModel.fromJson(responseModel.responseJson);

        if (cashOutResponseModel.status == "success") {
          cashOutSubmitInfoModel = cashOutResponseModel.data?.cashOut;
          if (cashOutSubmitInfoModel != null) {
            if (onSuccessCallback != null) {
              onSuccessCallback(cashOutResponseModel);
            }
          }
        } else {
          CustomSnackBar.error(
            errorList: cashOutResponseModel.message ?? [MyStrings.somethingWentWrong],
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
    cashOutHistoryList.clear();

    await getCashOutHistoryDataList();
  }

  bool isHistoryLoading = false;
  int page = 1;
  String? nextPageUrl;
  List<LatestCashOutHistory> cashOutHistoryList = [];
  Future<void> getCashOutHistoryDataList({bool forceLoad = true}) async {
    try {
      page = page + 1;
      isHistoryLoading = forceLoad;
      update();
      ResponseModel responseModel = await cashOutRepo.cashOutHistory(page);
      if (responseModel.statusCode == 200) {
        final cashOutHistoryResponseModel = cashOutHistoryResponseModelFromJson(
          jsonEncode(responseModel.responseJson),
        );
        if (cashOutHistoryResponseModel.status == "success") {
          nextPageUrl = cashOutHistoryResponseModel.data?.history?.nextPageUrl;
          cashOutHistoryList.addAll(
            cashOutHistoryResponseModel.data?.history?.data ?? [],
          );
        } else {
          CustomSnackBar.error(
            errorList: cashOutHistoryResponseModel.message ?? [MyStrings.somethingWentWrong],
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
