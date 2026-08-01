import 'dart:convert';

import 'package:flutter/material.dart';
import 'package:get/get.dart';
import 'package:ovopay/app/components/snack_bar/show_custom_snackbar.dart';
import 'package:ovopay/core/data/models/authorization/authorization_response_model.dart';
import 'package:ovopay/core/data/models/global/charges/global_charge_model.dart';
import 'package:ovopay/core/data/models/global/response_model/response_model.dart';
import 'package:ovopay/core/data/models/modules/request_money/request_money_history_model.dart';
import 'package:ovopay/core/data/models/modules/request_money/request_money_info_response_model.dart';
import 'package:ovopay/core/data/models/modules/request_money/request_money_response_model.dart';
import 'package:ovopay/core/data/models/user/user_model.dart';
import 'package:ovopay/core/data/repositories/modules/request_money/request_money_repo.dart';

import '../../../../core/utils/util_exporter.dart';

class RequestMoneyController extends GetxController {
  RequestMoneyRepo requestMoneyRepo;
  RequestMoneyController({required this.requestMoneyRepo});

  bool isPageLoading = true;

  TextEditingController phoneNumberOrUserNameController = TextEditingController();
  TextEditingController amountController = TextEditingController();
  TextEditingController pinController = TextEditingController();
  TextEditingController noteController = TextEditingController();

  // Get Phone or username or Amount
  String get getPhoneOrUsername => phoneNumberOrUserNameController.text;
  String get getAmount => amountController.text;
  //Otp Type
  List<String> otpType = [];
  String selectedOtpType = "";

  //current balance
  double userCurrentBalance = 0.0;
  //Pending Request Counter
  String pendingRequestCounter = "0";
  //Charge
  GlobalChargeModel? globalChargeModel;
  //Latest send money history
  List<RequestMoneyHistoryDataModel> latestRequestMoneyHistory = [];
  //Check user exist
  bool isUserExist = false;
  UserModel? existUserModel;
  //Send Money Success Model
  MoneyRequestSubmitInfoModel? sendMoneySubmitInfoModel;
  //Action ID
  String actionRemark = "request_money";
  String actionRemark2 = "request_money_received";

  Future initController() async {
    isPageLoading = true;
    update();
    await loadRequestMoneyInfo();
    isPageLoading = false;
    update();
  }

  //Informations
  Future<void> loadRequestMoneyInfo() async {
    try {
      ResponseModel responseModel = await requestMoneyRepo.requestMoneyInfoData();
      if (responseModel.statusCode == 200) {
        final requestMoneyInfoResponseModel = requestMoneyInfoResponseModelFromJson(
          jsonEncode(responseModel.responseJson),
        );
        if (requestMoneyInfoResponseModel.status == "success") {
          final data = requestMoneyInfoResponseModel.data;
          if (data != null) {
            userCurrentBalance = data.getCurrentBalance();
            pendingRequestCounter = data.pendingRequestCounter ?? "0";
            otpType = data.otpType ?? [];
            globalChargeModel = data.charge;
            if (data.latestRequestMoney != null) {
              latestRequestMoneyHistory = data.latestRequestMoney ?? [];
            }
            update();
          }
        } else {
          CustomSnackBar.error(
            errorList: requestMoneyInfoResponseModel.message ?? [MyStrings.somethingWentWrong],
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
      ResponseModel responseModel = await requestMoneyRepo.checkUserExist(
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

  //Approve Request Money user select
  RequestMoneyHistoryDataModel? requestMoneyHistoryDataModelData;
  void selectRequestMoneyUserAndBalance({
    required RequestMoneyHistoryDataModel requestMoneyHistoryDataModel,
  }) {
    existUserModel = requestMoneyHistoryDataModel.requestSender;
    amountController.text = AppConverter.formatNumber(
      requestMoneyHistoryDataModel.amount ?? "",
    );
    requestMoneyHistoryDataModelData = requestMoneyHistoryDataModel;
    changeInfoWidget();
    update();
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
    void Function(RequestMoneyResponseModel)? onSuccessCallback,
    Function(RequestMoneyResponseModel)? onVerifyOtpCallback,
  }) async {
    try {
      isSubmitLoading = true;
      update();
      ResponseModel responseModel = await requestMoneyRepo.requestMoneyRequest(
        user: existUserModel?.username ?? phoneNumberOrUserNameController.text,
        amount: amountController.text,
        otpType: selectedOtpType,
      );
      if (responseModel.statusCode == 200) {
        RequestMoneyResponseModel sendMoneyResponseModel = RequestMoneyResponseModel.fromJson(responseModel.responseJson);

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

  Future<void> submitRequestMoneyProcess({
    String requestID = "",
    void Function(RequestMoneyResponseModel)? onSuccessCallback,
    Function(RequestMoneyResponseModel)? onVerifyOtpCallback,
  }) async {
    try {
      isSubmitLoading = true;
      update();
      ResponseModel responseModel = await requestMoneyRepo.requestMoneyApproveRequest(
        requestID: requestID,
        user: existUserModel?.username ?? phoneNumberOrUserNameController.text,
        amount: amountController.text,
        otpType: selectedOtpType,
      );
      if (responseModel.statusCode == 200) {
        RequestMoneyResponseModel sendMoneyResponseModel = RequestMoneyResponseModel.fromJson(responseModel.responseJson);

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
    void Function(RequestMoneyResponseModel)? onSuccessCallback,
    bool isRequestSent = true,
  }) async {
    try {
      isSubmitLoading = true;
      update();
      ResponseModel responseModel = await requestMoneyRepo.pinVerificationRequest(
        pin: pinController.text,
        note: noteController.text,
        remark: isRequestSent ? actionRemark : actionRemark2,
      );
      if (responseModel.statusCode == 200) {
        RequestMoneyResponseModel sendMoneyResponseModel = RequestMoneyResponseModel.fromJson(responseModel.responseJson);

        if (sendMoneyResponseModel.status == "success") {
          sendMoneySubmitInfoModel = sendMoneyResponseModel.data?.moneyRequest;
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

  //Approve request

  bool isSubmitApproveLoading = false;
  Future<void> approveRequestMoneyProcess({
    required RequestMoneyHistoryDataModel item,
    void Function(RequestMoneyResponseModel)? onSuccessCallback,
  }) async {
    try {
      isSubmitApproveLoading = true;
      update();
      ResponseModel responseModel = await requestMoneyRepo.approveMoneyRequest(
        id: item.id.toString(),
        pin: pinController.text,
      );
      if (responseModel.statusCode == 200) {
        RequestMoneyResponseModel sendMoneyResponseModel = RequestMoneyResponseModel.fromJson(responseModel.responseJson);

        if (sendMoneyResponseModel.status == "success") {
          if (onSuccessCallback != null) {
            onSuccessCallback(sendMoneyResponseModel);
            item.status = "1";
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
      isSubmitApproveLoading = false;
      update();
    }
  }
  //Approve Request end

  //Reject request
  bool isSubmitRejectLoading = false;
  Future<void> rejectRequestMoneyProcess({
    required RequestMoneyHistoryDataModel item,
    void Function(RequestMoneyResponseModel)? onSuccessCallback,
  }) async {
    try {
      isSubmitRejectLoading = true;
      update();
      ResponseModel responseModel = await requestMoneyRepo.rejectMoneyRequest(
        id: item.id.toString(),
      );
      if (responseModel.statusCode == 200) {
        RequestMoneyResponseModel sendMoneyResponseModel = RequestMoneyResponseModel.fromJson(responseModel.responseJson);

        if (sendMoneyResponseModel.status == "success") {
          if (onSuccessCallback != null) {
            onSuccessCallback(sendMoneyResponseModel);
            item.status = "2";
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
      isSubmitRejectLoading = false;
      update();
    }
  }
  //Reject Request end

  //History

  // Shared history states for receiver and sender
  bool isReceiverHistoryLoading = false;
  bool isSenderHistoryLoading = false;

  int receiverPage = 1;
  int senderPage = 1;

  String? receiverNextPageUrl;
  String? senderNextPageUrl;

  List<RequestMoneyHistoryDataModel> receiverHistoryList = [];
  List<RequestMoneyHistoryDataModel> senderHistoryList = [];

  void initialHistoryData(bool isReceiver) async {
    if (isReceiver) {
      isReceiverHistoryLoading = true;
      receiverPage = 0;
      receiverNextPageUrl = null;
      receiverHistoryList.clear();
    } else {
      isSenderHistoryLoading = true;
      senderPage = 0;
      senderNextPageUrl = null;
      senderHistoryList.clear();
    }

    await getHistoryData(isReceiver: isReceiver);
  }

  Future<void> getHistoryData({
    required bool isReceiver,
    bool forceLoad = true,
  }) async {
    try {
      if (isReceiver) {
        receiverPage = receiverPage + 1;
        isReceiverHistoryLoading = forceLoad;
      } else {
        senderPage = senderPage + 1;
        isSenderHistoryLoading = forceLoad;
      }
      update();

      final int page = isReceiver ? receiverPage : senderPage;
      ResponseModel responseModel = isReceiver
          ? await requestMoneyRepo.requestMoneyByMyFriends(
              page,
            ) // Replace with actual API call
          : await requestMoneyRepo.requestMoneyByMeHistory(
              page,
            ); // Replace with actual API call

      if (responseModel.statusCode == 200) {
        final requestMoneyHistoryResponseModel = requestMoneyHistoryResponseModelFromJson(
          jsonEncode(responseModel.responseJson),
        );

        if (requestMoneyHistoryResponseModel.status == "success") {
          final receiverData = requestMoneyHistoryResponseModel.data?.requestedMoneys?.data ?? [];
          final sentRequestData = requestMoneyHistoryResponseModel.data?.requestMoneys?.data ?? [];

          if (isReceiver) {
            receiverNextPageUrl = requestMoneyHistoryResponseModel.data?.requestedMoneys?.nextPageUrl;
            receiverHistoryList.addAll(receiverData);
          } else {
            senderNextPageUrl = requestMoneyHistoryResponseModel.data?.requestMoneys?.nextPageUrl;
            senderHistoryList.addAll(sentRequestData);
          }
        } else {
          CustomSnackBar.error(
            errorList: requestMoneyHistoryResponseModel.message ?? [MyStrings.somethingWentWrong],
          );
        }
        update();
      } else {
        CustomSnackBar.error(errorList: [responseModel.message]);
      }
    } catch (e) {
      printE(e.toString());
    }

    if (isReceiver) {
      isReceiverHistoryLoading = false;
    } else {
      isSenderHistoryLoading = false;
    }
    update();
  }

  bool hasNext(bool isReceiver) {
    final nextPageUrl = isReceiver ? receiverNextPageUrl : senderNextPageUrl;
    return nextPageUrl != null && nextPageUrl.isNotEmpty && nextPageUrl != 'null';
  }

  //History end
}
