import 'dart:convert';

import 'package:flutter/material.dart';
import 'package:get/get.dart';
import 'package:ovopay/app/components/snack_bar/show_custom_snackbar.dart';
import 'package:ovopay/core/data/models/global/charges/global_charge_model.dart';
import 'package:ovopay/core/data/models/global/response_model/response_model.dart';
import 'package:ovopay/core/data/models/modules/investment/investment_history_response_model.dart';
import 'package:ovopay/core/data/models/modules/investment/investment_plan_response_model.dart';
import 'package:ovopay/core/data/models/modules/investment/investment_submit_response_model.dart';
import 'package:ovopay/core/data/repositories/investment/investment_repo.dart';

import '../../../../core/utils/util_exporter.dart';

class InvestmentController extends GetxController {
  InvestmentRepo investmentRepo;
  InvestmentController({required this.investmentRepo});
  bool isPageLoading = false;
  double currentValue = 0.0;
  int selectedPlanIndex = 0;
  List<String> otpType = [];
  String selectedOtpType = "";

  //Charge
  GlobalChargeModel? globalChargeModel;
  //Make Payment Success Model
  InvestmentModel? makeInvestmentSubmitInfoModel;
  //Action ID
  String actionRemark = "investment";
  //current balance
  double userCurrentBalance = 0.0;
  final TextEditingController amountController = TextEditingController();
  TextEditingController pinController = TextEditingController();
  String get getAmount => amountController.text;

  Map<int, double> sliderValues = {};

  int currentIndex = 0;

  void updateExpandIndex(int index, bool expanded) {
    selectedPlanIndex = expanded ? index : -1;
    printX('Selected Plan Index updated to: $selectedPlanIndex');
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

  //Plan Data
  bool isPlanLoading = false;
  int page = 1;
  String? nextPageUrl;
  List<PlanData> investmentPlanList = [];
  PlanData? selectedPlanData;

  Future initialPlanData() async {
    isPlanLoading = true;
    page = 0;
    nextPageUrl = null;
    investmentPlanList.clear();

    // Clear slider values when refreshing data
    sliderValues.clear();

    await getInvesmentDataList();
  }

  Future<void> getInvesmentDataList({
    bool forceLoad = true,
  }) async {
    try {
      page = page + 1;
      isPlanLoading = forceLoad;
      update();
      ResponseModel responseModel = await investmentRepo.investmentPlanRepo(page);
      if (responseModel.statusCode == 200) {
        final investmentPlanResponseModel = investmentPlanResponseModelFromJson(
          jsonEncode(responseModel.responseJson),
        );
        if (investmentPlanResponseModel.status == "success") {
          final data = investmentPlanResponseModel.data;
          if (data != null) {
            userCurrentBalance = data.getCurrentBalance();

            otpType = data.supportedOtpTypes ?? [];
          }
          nextPageUrl = investmentPlanResponseModel.data?.plans?.nextPageUrl;
          investmentPlanList.addAll(
            investmentPlanResponseModel.data?.plans?.data ?? [],
          );
        } else {
          CustomSnackBar.error(
            errorList: investmentPlanResponseModel.message ?? [MyStrings.somethingWentWrong],
          );
        }
        update();
        isPlanLoading = false;
        update();
      } else {
        CustomSnackBar.error(errorList: [responseModel.message]);
      }
    } catch (e) {
      printE(e.toString());
    }
    isPlanLoading = false;
    update();
  }

  bool hasNext() {
    return nextPageUrl != null && nextPageUrl!.isNotEmpty && nextPageUrl != 'null' ? true : false;
  }
  //Plan History Data

  bool isHistoryLoading = false;
  int pageHistory = 1;
  String? nextPageHistoryUrl;
  List<InvestmentData> investmentHistoryPlanList = [];
  Future initialHistoryData() async {
    isHistoryLoading = true;
    pageHistory = 0;
    nextPageUrl = null;
    investmentHistoryPlanList.clear();

    await getInvestmentHistoryDataList();
  }

  Future<void> getInvestmentHistoryDataList({
    bool forceLoad = true,
  }) async {
    try {
      pageHistory = pageHistory + 1;
      isHistoryLoading = forceLoad;
      update();

      ResponseModel responseModel = await investmentRepo.investmentHistoryRepo(pageHistory);
      if (responseModel.statusCode == 200) {
        final investmentPlanHistoryResponseModel = investmentHistoryResponseModelFromJson(
          jsonEncode(responseModel.responseJson),
        );
        if (investmentPlanHistoryResponseModel.status == "success") {
          nextPageHistoryUrl = investmentPlanHistoryResponseModel.data?.investments?.nextPageUrl;

          investmentHistoryPlanList.addAll(
            investmentPlanHistoryResponseModel.data?.investments?.data ?? [],
          );
          update();
        } else {
          CustomSnackBar.error(
            errorList: investmentPlanHistoryResponseModel.message ?? [MyStrings.somethingWentWrong],
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

  bool hasNextHistory() {
    return nextPageHistoryUrl != null && nextPageHistoryUrl!.isNotEmpty && nextPageHistoryUrl != 'null' ? true : false;
  }

  Map<int, double> perInterestAmount = {};
  Map<int, double> totalReturns = {};
  void updateSliderValue(int index, double value) {
    double selectedAmount = AppConverter.formatNumberDouble((value).toString(), precision: 0);
    sliderValues[index] = selectedAmount;
    calculateReturns(index, selectedAmount);
    update();
  }

  //Investment Submit Process
  void selectAPlanData(Function()? onUpdate) {
    selectedPlanData = investmentPlanList[selectedPlanIndex];
    amountController.text = sliderValues[selectedPlanIndex]?.toString() ?? '0';
    onUpdate?.call();
    update();
  }

  //Submit
  bool isSubmitLoading = false;
  Future<void> submitThisProcess({
    void Function(InvestmentSubmitResponseModel)? onSuccessCallback,
    void Function(InvestmentSubmitResponseModel)? onVerifyOtpCallback,
  }) async {
    try {
      isSubmitLoading = true;
      update();
      ResponseModel responseModel = await investmentRepo.makeInvestmentRequest(
        planId: investmentPlanList[selectedPlanIndex].id.toString(),
        investAmount: amountController.text,
        pin: pinController.text,
        otpType: selectedOtpType,
        remark: actionRemark,
      );
      if (responseModel.statusCode == 200) {
        InvestmentSubmitResponseModel investmentResponseModel = InvestmentSubmitResponseModel.fromJson(responseModel.responseJson);

        if (investmentResponseModel.status == "success") {
          if (investmentResponseModel.remark == "otp") {
            if (onVerifyOtpCallback != null) {
              onVerifyOtpCallback(investmentResponseModel);
            }
            update();
          } else {
            if (investmentResponseModel.remark == "pin") {
              if (onSuccessCallback != null) {
                onSuccessCallback(investmentResponseModel);
              }
            }
          }
        } else {
          CustomSnackBar.error(
            errorList: investmentResponseModel.message ?? [MyStrings.somethingWentWrong],
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
    void Function(InvestmentSubmitResponseModel)? onSuccessCallback,
  }) async {
    try {
      isSubmitLoading = true;
      update();
      ResponseModel responseModel = await investmentRepo.pinVerificationRequest(pin: pinController.text);
      if (responseModel.statusCode == 200) {
        InvestmentSubmitResponseModel investmentSubmitResponseModel = InvestmentSubmitResponseModel.fromJson(responseModel.responseJson);

        if (investmentSubmitResponseModel.status == "success") {
          makeInvestmentSubmitInfoModel = investmentSubmitResponseModel.data?.investment;
          if (makeInvestmentSubmitInfoModel != null) {
            if (onSuccessCallback != null) {
              onSuccessCallback(investmentSubmitResponseModel);
            }
          }
        } else {
          CustomSnackBar.error(
            errorList: investmentSubmitResponseModel.message ?? [MyStrings.somethingWentWrong],
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

  void calculateReturns(int index, double value) {
    var plan = investmentPlanList[index];

    double interest = double.tryParse(plan.interestAmount.toString()) ?? 0.0;
    double repeatTime = double.tryParse(plan.repeatTimes.toString()) ?? 0.0;

    double amount = 0.0;

    if (plan.investType == "0") {
      amount = value * interest / 100;
    } else {
      amount = investmentPlanList[index].interestType == "1" ? interest : value * interest / 100;
    }

    perInterestAmount[index] = amount;
    totalReturns[index] = amount * repeatTime;
  } //Amount text changes

  void onChangeAmountControllerText(String value) {
    amountController.text = value;

    update();
  }
}
