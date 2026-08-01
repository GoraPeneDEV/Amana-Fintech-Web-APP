import 'dart:convert';
import 'dart:io';

import 'package:flutter/material.dart';
import 'package:get/get.dart';
import 'package:ovopay/app/components/snack_bar/show_custom_snackbar.dart';
import 'package:ovopay/core/data/models/global/charges/global_charge_model.dart';
import 'package:ovopay/core/data/models/global/formdata/dynamic_fom_submitted_value_model.dart';
import 'package:ovopay/core/data/models/global/response_model/response_model.dart';
import 'package:ovopay/core/data/models/modules/airtime_recharge/airtime_recharge_response_model.dart';
import 'package:ovopay/core/data/models/modules/bill_pay/bill_pay_history_response_model.dart';
import 'package:ovopay/core/data/models/modules/bill_pay/bill_pay_response_model.dart';
import 'package:ovopay/core/data/models/modules/bill_pay/bill_pay_submit_response_model.dart';
import 'package:ovopay/core/data/models/modules/global/module_transaction_model.dart';
import 'package:ovopay/core/data/repositories/modules/bill_pay/bill_pay_repo.dart';
import 'package:ovopay/core/data/services/api_service.dart';

import '../../../../core/utils/util_exporter.dart';

class BillPayController extends GetxController {
  BillPayRepo billPayRepo;
  BillPayController({required this.billPayRepo});

  String moduleName = "Bill Pay";
  bool isPageLoading = true;

  TextEditingController countryController = TextEditingController();
  TextEditingController companyNameController = TextEditingController();
  TextEditingController amountController = TextEditingController();
  TextEditingController fixedAmountIDController = TextEditingController();
  TextEditingController pinController = TextEditingController();
  TextEditingController uniqueIDController = TextEditingController();
  TextEditingController referenceController = TextEditingController();
  bool saveInformation = false;

  // Get Phone or username or Amount
  String get getOrganizationNameOrType => companyNameController.text;
  String get getAmount => amountController.text;
  //Otp Type
  List<String> otpType = [];
  String selectedOtpType = "";
  //current balance
  double userCurrentBalance = 0.0;
  //Charge
  GlobalChargeModel? globalChargeModel;
  //Utility category List
  List<BillPayCategory> utilityCategoryDataList = [];
  BillPayCategory? selectedUtilityCategory;
  //Utility Company List
  List<BillPayCompany> utilityBillCompanyDataList = [];
  List<BillPayCompany> filterUtilityBillCompanyDataList = [];
  BillPayCompany? selectedUtilityCompany;
  //Selected company saved account
  List<UsersDynamicFormSubmittedDataModel>? selectedCompanyAutofillData;
  //Saved company data
  List<UserSavedCompany> savedUtilityCompanyDataList = [];
  UserSavedCompany? selectedSavedUtilityCompany;

  //Country List
  List<CountryListModel> countryDataList = [];
  CountryListModel? selectedCountry;

  //Bill pay Success Model
  ModuleGlobalSubmitTransactionModel? moduleGlobalSubmitTransactionModel;
  //Action ID
  String actionRemark = "utility_bill";

  Future initController({bool forceLoad = true}) async {
    isPageLoading = forceLoad;
    update();
    await loadBillPayInfo();
    isPageLoading = false;
    update();
  }

  //Informations
  Future<void> loadBillPayInfo() async {
    try {
      ResponseModel responseModel = await billPayRepo.billPayInfoData();
      if (responseModel.statusCode == 200) {
        final billPayCategoryAndCompanyModel = billPayCategoryAndCompanyModelFromJson(
          jsonEncode(responseModel.responseJson),
        );
        if (billPayCategoryAndCompanyModel.status == "success") {
          final data = billPayCategoryAndCompanyModel.data;
          if (data != null) {
            userCurrentBalance = data.getCurrentBalance();

            otpType = data.otpType ?? [];

            globalChargeModel = data.utilityCharge;
            countryDataList = data.counties ?? [];
            utilityCategoryDataList = data.billCategory ?? [];
            savedUtilityCompanyDataList = data.userCompanies ?? [];

            if (utilityCategoryDataList.isNotEmpty) {
              utilityBillCompanyDataList.clear();
              for (var item in utilityCategoryDataList) {
                utilityBillCompanyDataList.addAll(item.company ?? []);
              }
              filterUtilityBillCompanyDataList = utilityBillCompanyDataList;
            }

            update();
          }
        } else {
          CustomSnackBar.error(
            errorList: billPayCategoryAndCompanyModel.message ?? [MyStrings.somethingWentWrong],
          );
        }
      } else {
        CustomSnackBar.error(errorList: [responseModel.message]);
      }
    } catch (e) {
      printE(e.toString());
    }
  }

  void filterUtilityBillByCompanyName(String name) {
    selectedUtilityCategory = null;
    var filteredList1 = utilityCategoryDataList
        .where(
          (category) => category.name?.toLowerCase().contains(name.toLowerCase()) ?? false,
        )
        .toList();

    if (filteredList1.isNotEmpty) {
      List<BillPayCompany> tempData = [];
      for (var category in filteredList1) {
        for (BillPayCompany company in category.company ?? []) {
          tempData.add(company);
        }
      }
      filterUtilityBillCompanyDataList = tempData;
    } else {
      var filteredList2 = utilityBillCompanyDataList
          .where(
            (company) => company.name?.toLowerCase().contains(name.toLowerCase()) ?? false,
          )
          .toList();

      if (name.trim().isNotEmpty) {
        filterUtilityBillCompanyDataList = filteredList2;
      } else {
        filterUtilityBillCompanyDataList = utilityBillCompanyDataList;
      }
    }

    update();
  }

  List<BillPayCompany> getfilterUtilityBillCompanyDataList() {
    final isCountrySelected = selectedCountry != null;
    final isCategorySelected = selectedUtilityCategory != null;

    if (!isCountrySelected && !isCategorySelected) {
      return filterUtilityBillCompanyDataList;
    }

    return filterUtilityBillCompanyDataList.where((company) {
      final matchCountry = !isCountrySelected || company.countryId == selectedCountry?.id.toString();
      final matchCategory = !isCategorySelected || company.categoryId == selectedUtilityCategory?.id.toString();
      return matchCountry && matchCategory;
    }).toList();
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

// Toggle Information
  void toggleSaveInformation() {
    saveInformation = !saveInformation;
    update();
  }

  //Select country
  Future<void> selectAnCountryOnTap(CountryListModel value) async {
    if (selectedCountry == value) {
      selectedCountry = null;
      countryController.clear();
    } else {
      selectedCountry = value;
      countryController.text = value.name ?? "";
    }
    update();
  }

  List<UserSavedCompany> getUniqueCompanyIdList() {
    final uniqueCompanyIds = <String>{}; // Set to track unique company IDs
    return savedUtilityCompanyDataList
        .where(
          (item) => uniqueCompanyIds.add(item.companyId ?? ""),
        ) // Add only if the companyId is not already in the set
        .toList();
  }

  //Select category
  void selectedUtilityCategoryOnTap(BillPayCategory value) {
    if (selectedUtilityCategory == value) {
      selectedUtilityCategory = null;
      update();
      return;
    }
    selectedUtilityCategory = value;
    update();
  }

  //Select Company
  void selectedUtilityCompanyOnTap(BillPayCompany? value) {
    selectedUtilityCompany = null;
    update();
    selectedUtilityCompany = value;

    update();
  }

  //Select Company autofill data
  Future<void> selectedUtilityCompanyAutofillDataOnTap(UserSavedCompany? value) async {
    selectedSavedUtilityCompany = value;
    uniqueIDController.text = value?.uniqueId ?? "";
    selectedCompanyAutofillData = value?.userData ?? [];
    update();
  }

  //Amount text changes
  void onChangeAmountControllerText(String value) {
    amountController.text = value;
    fixedAmountIDController.clear();
    changeInfoWidget();
    update();
  }

  // Fixed Amount ID
  void onChangeFixedAmountControllerText(String value, String id) {
    amountController.text = value;
    fixedAmountIDController.text = id;
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
    double percent = 0;
    double percentCharge = 0;
    double fixedCharge = 0;
    double tempTotalCharge = 0;
    //Charge calculation
    if (selectedUtilityCompany?.percentCharge == null) {
      percent = double.tryParse(globalChargeModel?.percentCharge ?? "0") ?? 0;
    } else {
      percent = double.tryParse(selectedUtilityCompany?.percentCharge ?? "0") ?? 0;
    }
    percentCharge = mainAmount * percent / 100;

    if (selectedUtilityCompany?.fixedCharge == null) {
      fixedCharge = double.tryParse(globalChargeModel?.fixedCharge ?? "0") ?? 0;
    } else {
      fixedCharge = double.tryParse(selectedUtilityCompany?.fixedCharge ?? "0") ?? 0;
    }
    tempTotalCharge = percentCharge + fixedCharge;

    double capAmount = double.tryParse(globalChargeModel?.cap ?? "0") ?? 0;

    if (capAmount != -1.0 && capAmount != 1 && tempTotalCharge > capAmount) {
      tempTotalCharge = capAmount;
    }

    totalCharge = AppConverter.formatNumber('$tempTotalCharge', precision: 2);
    double payable = tempTotalCharge + mainAmount;
    payableAmountText = payableAmountText.length > 5 ? AppConverter.roundDoubleAndRemoveTrailingZero(payable.toString()) : AppConverter.formatNumber(payable.toString());
    if (selectedUtilityCompany?.rate != null) {
      double rate = double.tryParse(selectedUtilityCompany?.rate ?? "0") ?? 0;
      deliveryAmount = "${mainAmount * rate}";
    }
    update();
  }
  //Charge calculation end

  //Submit

  bool isSubmitLoading = false;
  Future<void> submitThisProcess({
    void Function(BillPaySubmitResponseModel)? onSuccessCallback,
    void Function(BillPaySubmitResponseModel)? onVerifyOtpCallback,
  }) async {
    try {
      isSubmitLoading = true;
      update();
      printE(selectedUtilityCompany?.toJson().toString());
      ResponseModel responseModel = await billPayRepo.billPayRequest(
        utilityID: selectedUtilityCompany?.id.toString() ?? "",
        userSavedUtilityID: selectedSavedUtilityCompany?.id?.toString() ?? "",
        uniqueID: uniqueIDController.text.toString(),
        amount: amountController.text,
        otpType: selectedOtpType,
        reference: referenceController.text,
        saveInformation: saveInformation,
        fixedAmountID: selectedUtilityCompany?.denominationType == AppStatus.fixed ? fixedAmountIDController.text : "",
      );
      if (responseModel.statusCode == 200) {
        BillPaySubmitResponseModel billPayResponseModel = BillPaySubmitResponseModel.fromJson(responseModel.responseJson);

        if (billPayResponseModel.status == "success") {
          if (billPayResponseModel.remark == "otp") {
            if (onVerifyOtpCallback != null) {
              onVerifyOtpCallback(billPayResponseModel);
            }
            update();
          } else {
            if (billPayResponseModel.remark == "pin") {
              if (onSuccessCallback != null) {
                onSuccessCallback(billPayResponseModel);
              }
            }
          }
        } else {
          CustomSnackBar.error(
            errorList: billPayResponseModel.message ?? [MyStrings.somethingWentWrong],
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
    void Function(BillPaySubmitResponseModel)? onSuccessCallback,
  }) async {
    try {
      isSubmitLoading = true;
      update();
      ResponseModel responseModel = await billPayRepo.pinVerificationRequest(
        pin: pinController.text,
      );
      if (responseModel.statusCode == 200) {
        BillPaySubmitResponseModel sendMoneyResponseModel = BillPaySubmitResponseModel.fromJson(responseModel.responseJson);

        if (sendMoneyResponseModel.status == "success") {
          moduleGlobalSubmitTransactionModel = sendMoneyResponseModel.data?.billData;
          if (moduleGlobalSubmitTransactionModel != null) {
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

  bool isDeleteSaveAccountLoading = false;
  String isDeleteSaveAccountIDLoading = "-1";
  Future<void> deleteSavedAccount({String accountID = ""}) async {
    try {
      isDeleteSaveAccountLoading = true;
      isDeleteSaveAccountIDLoading = accountID;
      update();
      ResponseModel responseModel = await billPayRepo.deleteSavedAccount(
        accountID,
      );
      if (responseModel.statusCode == 200) {
        BillPaySubmitResponseModel billPayDeleteAccountSubmitResponseModel = BillPaySubmitResponseModel.fromJson(responseModel.responseJson);

        if (billPayDeleteAccountSubmitResponseModel.status == "success") {
          CustomSnackBar.success(
            successList: billPayDeleteAccountSubmitResponseModel.message ?? [MyStrings.requestSuccess],
          );
          initController(forceLoad: false);
        } else {
          CustomSnackBar.error(
            errorList: billPayDeleteAccountSubmitResponseModel.message ?? [MyStrings.somethingWentWrong],
          );
        }
        update();
      } else {
        CustomSnackBar.error(errorList: [responseModel.message]);
      }
    } catch (e) {
      printE(e.toString());
    } finally {
      isDeleteSaveAccountIDLoading = "-1";
      isDeleteSaveAccountLoading = false;
      update();
    }
  }

  //History

  int currentIndex = 0;
  void initialHistoryData() async {
    isHistoryLoading = true;
    page = 0;
    nextPageUrl = null;
    billPayHistoryList.clear();

    await getBillPayHistoryDataList();
  }

  bool isHistoryLoading = false;
  int page = 1;
  String? nextPageUrl;
  List<LatestPayBillHistoryModel> billPayHistoryList = [];
  Future<void> getBillPayHistoryDataList({bool forceLoad = true}) async {
    try {
      page = page + 1;
      isHistoryLoading = forceLoad;
      update();
      ResponseModel responseModel = await billPayRepo.billPayHistory(page);
      if (responseModel.statusCode == 200) {
        final billPayHistoryResponseModel = billPayHistoryResponseModelFromJson(
          jsonEncode(responseModel.responseJson),
        );
        if (billPayHistoryResponseModel.status == "success") {
          nextPageUrl = billPayHistoryResponseModel.data?.history?.nextPageUrl;
          billPayHistoryList.addAll(
            billPayHistoryResponseModel.data?.history?.data ?? [],
          );
        } else {
          CustomSnackBar.error(
            errorList: billPayHistoryResponseModel.message ?? [MyStrings.somethingWentWrong],
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

  bool isDownloadLoading = false;
  int selectedDownloadIndex = -1;
  Future<void> downloadBillPdf(
    LatestPayBillHistoryModel item,
    int index,
    String extension,
  ) async {
    // Update UI to indicate loading state
    selectedDownloadIndex = index;
    isDownloadLoading = true;
    update();

    try {
      // Check storage permissions
      if (await MyUtils().checkAndRequestStoragePermission()) {
        Directory downloadsDirectory = await MyUtils.getDefaultDownloadDirectory();
        var fileName = '${moduleName}_${DateTime.now().millisecondsSinceEpoch}.$extension';

        if (downloadsDirectory.existsSync()) {
          final downloadPath = '${downloadsDirectory.path}/$fileName';

          // Try downloading the file
          try {
            ResponseModel responseModel = await ApiService.downloadFile(
              url: "${UrlContainer.billPayDownloadEndPoint}/${item.id}",
              savePath: downloadPath,
            );
            await MyUtils().openFile(downloadPath, extension);
            CustomSnackBar.success(successList: [responseModel.message]);
          } catch (e) {
            CustomSnackBar.error(errorList: ["Failed to download file: $e"]);
          }
        } else {
          CustomSnackBar.error(
            errorList: ["Download directory does not exist."],
          );
        }
      } else {
        CustomSnackBar.error(
          errorList: ["Storage permission is required to download files."],
        );
      }
    } catch (e) {
      printE(e.toString());
    }
    // Reset UI loading state
    selectedDownloadIndex = -1;
    isDownloadLoading = false;
    update();
  }
}
