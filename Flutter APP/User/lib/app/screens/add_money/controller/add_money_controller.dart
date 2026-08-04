import 'dart:convert';

import 'package:flutter/material.dart';
import 'package:get/get.dart';
import 'package:ovopay/app/components/snack_bar/show_custom_snackbar.dart';
import 'package:ovopay/core/data/models/deposit/deposit_history_response_model.dart';
import 'package:ovopay/core/data/models/deposit/deposit_insert_response_model.dart';
import 'package:ovopay/core/data/models/deposit/deposit_method_response_model.dart';
import 'package:ovopay/core/data/models/global/response_model/response_model.dart';
import 'package:ovopay/core/data/repositories/add_money/add_money_repo.dart';
import 'package:ovopay/core/data/services/shared_pref_service.dart';
import 'package:ovopay/core/route/route.dart';

import '../../../../core/utils/util_exporter.dart';

class AddMoneyController extends GetxController {
  AddMoneyRepo depositRepo;
  AddMoneyController({required this.depositRepo});

  bool isLoading = true;

  final TextEditingController selectedPaymentMethodController = TextEditingController();
  TextEditingController amountController = TextEditingController();
  // Get Phone or username or Amount
  String get getAmount => amountController.text;
  String selectedValue = "";
  String depositLimit = "";

  List<DepositMethods> methodList = [];
  List<DepositMethods> methods = [];
  String imagePath = UrlContainer.domainUrl;
  DepositMethods? selectedAddMoneyMethod = DepositMethods(
    name: MyStrings.selectOne,
    id: -1,
  );

  double rate = 1;
  double mainAmount = 0;

  void setPaymentMethod(DepositMethods? method) {
    String amt = amountController.text.toString();
    mainAmount = amt.isEmpty ? 0 : double.tryParse(amt) ?? 0;
    selectedAddMoneyMethod = method;

    depositLimit = '${AppConverter.formatNumber(method?.minAmount?.toString() ?? '-1')} - ${AppConverter.formatNumber(method?.maxAmount?.toString() ?? '-1')} $currency';
    changeInfoWidgetValue();
    update();
  }

  Future<void> getDepositMethod() async {
    currency = SharedPreferenceService.getCurrencySymbol(isFullText: true);
    methodList.clear();
    selectedPaymentMethodController.text = selectedAddMoneyMethod?.name ?? '';
    ResponseModel responseModel = await depositRepo.getDepositMethods();

    if (responseModel.statusCode == 200) {
      DepositMethodResponseModel methodsModel = DepositMethodResponseModel.fromJson(responseModel.responseJson);

      if (methodsModel.message != null && methodsModel.message != null) {
        List<DepositMethods> tempList = methodsModel.data?.methods ?? [];
        if (tempList.isNotEmpty) {
          methodList.addAll(tempList);
        }
      }
      imagePath = '${UrlContainer.domainUrl}/${methodsModel.data?.imagePath}';
    } else {
      CustomSnackBar.error(errorList: [responseModel.message]);
      return;
    }

    isLoading = false;
    update();
  }

  bool submitLoading = false;
  Future<void> submitDeposit() async {
    if (selectedAddMoneyMethod?.id.toString() == '-1') {
      CustomSnackBar.error(errorList: [MyStrings.selectPaymentMethod.tr]);
      return;
    }

    String amount = amountController.text.toString();
    if (amount.isEmpty) {
      CustomSnackBar.error(errorList: [MyStrings.enterAmount.tr]);
      return;
    }

    submitLoading = true;
    update();

    try {
      ResponseModel responseModel = await depositRepo.insertDeposit(
        amount: amount,
        methodCode: selectedAddMoneyMethod?.methodCode ?? "",
        currency: selectedAddMoneyMethod?.currency ?? "",
      );

      if (responseModel.statusCode == 200) {
        DepositInsertResponseModel insertResponseModel = DepositInsertResponseModel.fromJson(responseModel.responseJson);

        if (insertResponseModel.status.toString().toLowerCase() == "success") {
          showWebView(
            insertResponseModel.data?.redirectUrl ?? "",
            insertResponseModel.data?.deposit?.successUrl ?? "",
            insertResponseModel.data?.deposit?.failedUrl ?? "",
          );
        } else {
          CustomSnackBar.error(
            errorList: insertResponseModel.message ?? [MyStrings.somethingWentWrong],
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

  //Amount text changes
  void onChangeAmountControllerText(String value) {
    amountController.text = value;
    changeInfoWidgetValue();
    update();
  }

  //Charge calculation

  String charge = "";
  String payable = "";
  String amount = "";
  String fixedCharge = "";
  String currency = '';
  String payableText = '';
  String conversionRate = '';
  String inMethodPayable = '';

  void changeInfoWidgetValue() {
    if (selectedAddMoneyMethod?.id.toString() == '-1') {
      return;
    }

    mainAmount = double.tryParse(amountController.text) ?? 0.0;

    // mainAmount = amount;
    double percent = double.tryParse(selectedAddMoneyMethod?.percentCharge ?? '0') ?? 0;
    double percentCharge = (mainAmount * percent) / 100;
    double temCharge = double.tryParse(selectedAddMoneyMethod?.fixedCharge ?? '0') ?? 0;
    double totalCharge = percentCharge + temCharge;
    charge = '${AppConverter.formatNumber('$totalCharge')} $currency';
    double payable = totalCharge + mainAmount;
    payableText = '$payable $currency';

    rate = double.tryParse(selectedAddMoneyMethod?.rate ?? '0') ?? 0;
    conversionRate = '1 $currency = $rate ${selectedAddMoneyMethod?.currency ?? ''}';
    inMethodPayable = AppConverter.formatNumber('${payable * rate}');

    update();
    return;
  }

  void clearData() {
    depositLimit = '';
    charge = '';
    amountController.text = '';
    isLoading = false;
    methodList.clear();
  }

  bool isShowRate() {
    if (rate > 1 && currency.toLowerCase() != selectedAddMoneyMethod?.currency?.toLowerCase()) {
      return true;
    } else {
      return false;
    }
  }

  void showWebView(String redirectUrl, String successUrl, String failedUrl) {
    Get.toNamed(
      RouteHelper.addMoneyWebViewScreen,
      arguments: [redirectUrl, successUrl, failedUrl],
    );
  }

  //History

  int currentIndex = 0;
  void initialHistoryData() async {
    isHistoryLoading = true;
    page = 0;
    nextPageUrl = null;
    depositHistoryList.clear();

    await getDonationHistoryDataList();
  }

  bool isHistoryLoading = false;
  int page = 1;
  String? nextPageUrl;
  List<DepositHistoryListModel> depositHistoryList = [];
  Future<void> getDonationHistoryDataList({bool forceLoad = true}) async {
    try {
      page = page + 1;
      isHistoryLoading = forceLoad;
      update();
      ResponseModel responseModel = await depositRepo.getDepositHistory(
        page: page,
        searchText: "",
      );
      if (responseModel.statusCode == 200) {
        final donationHistoryResponseModel = depositHistoryResponseModelFromJson(
          jsonEncode(responseModel.responseJson),
        );
        if (donationHistoryResponseModel.status == "success") {
          nextPageUrl = donationHistoryResponseModel.data?.histories?.nextPageUrl;
          depositHistoryList.addAll(
            donationHistoryResponseModel.data?.histories?.data ?? [],
          );
        } else {
          CustomSnackBar.error(
            errorList: donationHistoryResponseModel.message ?? [MyStrings.somethingWentWrong],
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
