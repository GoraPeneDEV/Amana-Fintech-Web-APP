import 'dart:convert';

import 'package:flutter/material.dart';
import 'package:get/get.dart';
import 'package:ovopay/app/components/snack_bar/show_custom_snackbar.dart';
import 'package:ovopay/core/data/models/authorization/authorization_response_model.dart';
import 'package:ovopay/core/data/models/global/charges/global_charge_model.dart';
import 'package:ovopay/core/data/models/global/response_model/response_model.dart';
import 'package:ovopay/core/data/models/modules/make_payment/make_payment_history_response_model.dart';
import 'package:ovopay/core/data/models/modules/make_payment/make_payment_response_model.dart';
import 'package:ovopay/core/data/models/user/user_model.dart';
import 'package:ovopay/core/data/repositories/modules/make_payment/make_payment_repo.dart';

import '../../../../core/utils/util_exporter.dart';

class PaymentController extends GetxController {
  MakePaymentRepo makePaymentRepo;
  PaymentController({required this.makePaymentRepo});

  bool isPageLoading = true;

  TextEditingController phoneNumberOrUserNameController = TextEditingController();
  TextEditingController amountController = TextEditingController();
  TextEditingController pinController = TextEditingController();
  TextEditingController referenceTextController = TextEditingController();

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
  //Latest Make Payment history
  List<LatestMakePaymentHistory> latestMakePaymentHistory = [];
  //Check user exist
  bool isMerchantExist = false;
  UserModel? existMerchantModel;
  //Make Payment Success Model
  MakePaymentSubmitInfo? makePaymentSubmitInfoModel;
  //Action ID
  String actionRemark = "make_payment";

  Future initController() async {
    isPageLoading = true;
    update();
    await loadMakePaymentInfo();
    isPageLoading = false;
    update();
  }

  //Informations
  Future<void> loadMakePaymentInfo() async {
    try {
      ResponseModel responseModel = await makePaymentRepo.makePaymentInfoData();
      if (responseModel.statusCode == 200) {
        final makePaymentResponseModel = makePaymentResponseModelFromJson(
          jsonEncode(responseModel.responseJson),
        );

        if (makePaymentResponseModel.status == "success") {
          final data = makePaymentResponseModel.data;
          if (data != null) {
            userCurrentBalance = data.getCurrentBalance();

            otpType = data.otpType ?? [];
            // globalChargeModel = data.sendMoneyCharge;
            if (data.latestMakePaymentHistory != null) {
              latestMakePaymentHistory = data.latestMakePaymentHistory ?? [];
            }
            update();
          }
        } else {
          CustomSnackBar.error(
            errorList: makePaymentResponseModel.message ?? [MyStrings.somethingWentWrong],
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
  Future<void> checkMerchantExist(
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
      ResponseModel responseModel = await makePaymentRepo.checkMerchantExist(
        usernameOrPhone: processedInput,
      );
      if (responseModel.statusCode == 200) {
        AuthorizationResponseModel authorizationResponseModel = AuthorizationResponseModel.fromJson(responseModel.responseJson);

        if (authorizationResponseModel.status == "success") {
          existMerchantModel = authorizationResponseModel.data?.merchant;
          if (existMerchantModel != null) {
            isMerchantExist = true;
            onSuccessCallback();
          }
        } else {
          isMerchantExist = false;
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

  Future<void> setMerchantFromQrScan(
    VoidCallback onSuccessCallback, {
    UserModel? existUserModel,
  }) async {
    try {
      isCheckingUserLoading = true;
      update();

      if (existUserModel != null) {
        existMerchantModel = existUserModel;

        isMerchantExist = true;
        onSuccessCallback();
      } else {
        isMerchantExist = false;
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
    void Function(MakePaymentResponseModel)? onSuccessCallback,
    void Function(MakePaymentResponseModel)? onVerifyOtpCallback,
  }) async {
    try {
      isSubmitLoading = true;
      update();
      ResponseModel responseModel = await makePaymentRepo.makePaymentRequest(
        user: existMerchantModel?.username ?? phoneNumberOrUserNameController.text,
        amount: amountController.text,
        pin: pinController.text,
        otpType: selectedOtpType,
        reference: referenceTextController.text,
      );
      if (responseModel.statusCode == 200) {
        MakePaymentResponseModel makePaymentResponseModel = MakePaymentResponseModel.fromJson(responseModel.responseJson);

        if (makePaymentResponseModel.status == "success") {
          if (makePaymentResponseModel.remark == "otp") {
            if (onVerifyOtpCallback != null) {
              onVerifyOtpCallback(makePaymentResponseModel);
            }
            update();
          } else {
            if (makePaymentResponseModel.remark == "pin") {
              if (onSuccessCallback != null) {
                onSuccessCallback(makePaymentResponseModel);
              }
            }
          }
        } else {
          CustomSnackBar.error(
            errorList: makePaymentResponseModel.message ?? [MyStrings.somethingWentWrong],
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
    void Function(MakePaymentResponseModel)? onSuccessCallback,
  }) async {
    try {
      isSubmitLoading = true;
      update();
      ResponseModel responseModel = await makePaymentRepo.pinVerificationRequest(pin: pinController.text);
      if (responseModel.statusCode == 200) {
        MakePaymentResponseModel sendMoneyResponseModel = MakePaymentResponseModel.fromJson(responseModel.responseJson);

        if (sendMoneyResponseModel.status == "success") {
          makePaymentSubmitInfoModel = sendMoneyResponseModel.data?.makePayment;
          if (makePaymentSubmitInfoModel != null) {
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
    paymentHistoryList.clear();

    await getPaymentHistoryDataList();
  }

  bool isHistoryLoading = false;
  int page = 1;
  String? nextPageUrl;
  List<LatestMakePaymentHistory> paymentHistoryList = [];
  Future<void> getPaymentHistoryDataList({bool forceLoad = true}) async {
    try {
      page = page + 1;
      isHistoryLoading = forceLoad;
      update();
      ResponseModel responseModel = await makePaymentRepo.makePaymentHistory(
        page,
      );
      if (responseModel.statusCode == 200) {
        final makePaymentHistoryResponseModel = makePaymentHistoryResponseModelFromJson(
          jsonEncode(responseModel.responseJson),
        );
        if (makePaymentHistoryResponseModel.status == "success") {
          nextPageUrl = makePaymentHistoryResponseModel.data?.history?.nextPageUrl;
          paymentHistoryList.addAll(
            makePaymentHistoryResponseModel.data?.history?.data ?? [],
          );
        } else {
          CustomSnackBar.error(
            errorList: makePaymentHistoryResponseModel.message ?? [MyStrings.somethingWentWrong],
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
