import 'dart:convert';

import 'package:flutter/material.dart';
import 'package:get/get.dart';
import 'package:ovopay/app/components/snack_bar/show_custom_snackbar.dart';
import 'package:ovopay/core/data/models/global/response_model/response_model.dart';
import 'package:ovopay/core/data/models/modules/airtime_recharge/airtime_recharge_history_response_model.dart';
import 'package:ovopay/core/data/models/modules/airtime_recharge/airtime_recharge_operators_response_model.dart';
import 'package:ovopay/core/data/models/modules/airtime_recharge/airtime_recharge_response_model.dart';
import 'package:ovopay/core/data/models/modules/airtime_recharge/airtime_recharge_submit_response_model.dart';
import 'package:ovopay/core/data/models/modules/global/module_transaction_model.dart';
import 'package:ovopay/core/data/repositories/modules/airtime_recharge/airtime_recharge_repo.dart';

import '../../../../core/utils/util_exporter.dart';

class AirTimeRechargeController extends GetxController {
  AirtimeRechargeRepo airtimeRechargeRepo;
  AirTimeRechargeController({required this.airtimeRechargeRepo});

  bool isPageLoading = false;
  TextEditingController countryController = TextEditingController();
  TextEditingController operatorController = TextEditingController();
  TextEditingController phoneNumberOrUserNameController = TextEditingController();
  TextEditingController amountController = TextEditingController();
  TextEditingController pinController = TextEditingController();

  // Get Phone or username or Amount
  String get getPhoneNumber => phoneNumberOrUserNameController.text;
  String get getAmount => amountController.text;
  //Otp Type
  List<String> otpType = [];
  String selectedOtpType = "";
  //current balance
  double userCurrentBalance = 0.0;

  //Country List
  List<CountryListModel> countryDataList = [];
  CountryListModel? selectedCountry;

  //Operator List
  List<OperatorData> operatorDataList = [];
  OperatorData? selectedOperator;
  List<String> suggestedAmounts = [];
  int selectedAmountIndex = -1;
  String selectedAmount = "";

  // Success Model
  ModuleGlobalSubmitTransactionModel? moduleGlobalSubmitTransactionModel;

  //Action ID
  String actionRemark = "mobile_recharge";

  Future initController() async {
    isPageLoading = true;
    update();
    await loadAirTimeRechargeInfo();
    isPageLoading = false;
    update();
  }

  //Informations
  Future<void> loadAirTimeRechargeInfo() async {
    try {
      ResponseModel responseModel = await airtimeRechargeRepo.airtimeRechargeInfoData();
      if (responseModel.statusCode == 200) {
        final airTimeRechargeResponseModel = airTimeRechargeResponseModelFromJson(
          jsonEncode(responseModel.responseJson),
        );
        if (airTimeRechargeResponseModel.status == "success") {
          final data = airTimeRechargeResponseModel.data;
          if (data != null) {
            userCurrentBalance = data.getCurrentBalance();

            otpType = data.otpType ?? [];
            // globalChargeModel = data.mobileRechargeCharge;

            if (data.countries != null) {
              countryDataList = data.countries ?? [];
            }
            update();
          }
        } else {
          CustomSnackBar.error(
            errorList: airTimeRechargeResponseModel.message ?? [MyStrings.somethingWentWrong],
          );
        }
      } else {
        CustomSnackBar.error(errorList: [responseModel.message]);
      }
    } catch (e) {
      printE(e.toString());
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

  //Select country
  Future<void> selectAnCountryOnTap(CountryListModel value) async {
    selectedCountry = value;
    countryController.text = value.name ?? "";
    selectedOperator = null;
    operatorController.text = "";
    suggestedAmounts = [];

    operatorDataList = value.operators ?? [];
    update();
  }

  //Select operator
  void selectAnOperatorOnTap(OperatorData value) {
    selectedOperator = value;
    operatorController.text = value.name ?? "";
    suggestedAmounts = value.suggestedAmounts ?? [];

    update();
  }

  //select amount and index
  void onSelectedAmountAndIndex(int index, String amount) {
    selectedAmountIndex = index;
    selectedAmount = amount;
    onChangeAmountControllerText(amount);
    update();
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
  String deliveryAmount = "";
  void changeInfoWidget() {
    mainAmount = double.tryParse(amountController.text) ?? 0.0;
    update();
    double tempTotalCharge = 0;

    totalCharge = AppConverter.formatNumber('$tempTotalCharge', precision: 2);
    double payable = tempTotalCharge + mainAmount;
    payableAmountText = payableAmountText.length > 5 ? AppConverter.roundDoubleAndRemoveTrailingZero(payable.toString()) : AppConverter.formatNumber(payable.toString());
    if (selectedOperator?.rate != null) {
      double rate = double.tryParse(selectedOperator?.rate ?? "0") ?? 0;
      deliveryAmount = "${mainAmount * rate}";
    }
    update();
  }
  //Charge calculation end

  //Submit

  bool isSubmitLoading = false;
  Future<void> submitThisProcess({
    void Function(AirTimeRechargeResponseModel)? onSuccessCallback,
    void Function(AirTimeRechargeResponseModel)? onVerifyOtpCallback,
  }) async {
    try {
      isSubmitLoading = true;
      update();
      ResponseModel responseModel = await airtimeRechargeRepo.airTimeTopUpRequest(
        mobile: getPhoneNumber,
        countryID: selectedCountry?.id.toString() ?? "-1",
        callingCode: selectedCountry?.callingCodes?.firstOrNull?.toString() ?? "-1",
        operator: selectedOperator?.id.toString() ?? "-1",
        amount: amountController.text,
        otpType: selectedOtpType,
      );
      if (responseModel.statusCode == 200) {
        AirTimeRechargeResponseModel airtimeRechargeSubmitModel = AirTimeRechargeResponseModel.fromJson(responseModel.responseJson);

        if (airtimeRechargeSubmitModel.status == "success") {
          if (airtimeRechargeSubmitModel.remark == "otp") {
            if (onVerifyOtpCallback != null) {
              onVerifyOtpCallback(airtimeRechargeSubmitModel);
            }
            update();
          } else {
            if (airtimeRechargeSubmitModel.remark == "pin") {
              if (onSuccessCallback != null) {
                onSuccessCallback(airtimeRechargeSubmitModel);
              }
            }
          }
        } else {
          CustomSnackBar.error(
            errorList: airtimeRechargeSubmitModel.message ?? [MyStrings.somethingWentWrong],
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
    void Function(AirTimeRechargeSubmitResponseModel)? onSuccessCallback,
  }) async {
    try {
      isSubmitLoading = true;
      update();
      ResponseModel responseModel = await airtimeRechargeRepo.pinVerificationRequest(pin: pinController.text);
      if (responseModel.statusCode == 200) {
        AirTimeRechargeSubmitResponseModel rechargeResponseModel = AirTimeRechargeSubmitResponseModel.fromJson(
          responseModel.responseJson,
        );

        if (rechargeResponseModel.status == "success") {
          moduleGlobalSubmitTransactionModel = rechargeResponseModel.data?.airtime;
          if (moduleGlobalSubmitTransactionModel != null) {
            if (onSuccessCallback != null) {
              onSuccessCallback(rechargeResponseModel);
            }
          }
        } else {
          CustomSnackBar.error(
            errorList: rechargeResponseModel.message ?? [MyStrings.somethingWentWrong],
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
    airtimeRechargeHistoryList.clear();

    await getAirTimeRechargeHistoryDataList();
  }

  bool isHistoryLoading = false;
  int page = 1;
  String? nextPageUrl;
  List<AirtimeDataModel> airtimeRechargeHistoryList = [];
  Future<void> getAirTimeRechargeHistoryDataList({
    bool forceLoad = true,
  }) async {
    try {
      page = page + 1;
      isHistoryLoading = forceLoad;
      update();
      ResponseModel responseModel = await airtimeRechargeRepo.airtimeRechargeHistory(page);
      if (responseModel.statusCode == 200) {
        final airtimeRechargeHistoryResponseModel = airtimeRechargeHistoryResponseModelFromJson(
          jsonEncode(responseModel.responseJson),
        );
        if (airtimeRechargeHistoryResponseModel.status == "success") {
          nextPageUrl = airtimeRechargeHistoryResponseModel.data?.topUps?.nextPageUrl;
          airtimeRechargeHistoryList.addAll(
            airtimeRechargeHistoryResponseModel.data?.topUps?.data ?? [],
          );
        } else {
          CustomSnackBar.error(
            errorList: airtimeRechargeHistoryResponseModel.message ?? [MyStrings.somethingWentWrong],
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
