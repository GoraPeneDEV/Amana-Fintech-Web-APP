import 'dart:convert';

import 'package:flutter/material.dart';
import 'package:get/get.dart';
import 'package:ovopay/app/components/snack_bar/show_custom_snackbar.dart';
import 'package:ovopay/core/data/models/global/charges/global_charge_model.dart';
import 'package:ovopay/core/data/models/global/formdata/dynamic_fom_submitted_value_model.dart';
import 'package:ovopay/core/data/models/global/response_model/response_model.dart';
import 'package:ovopay/core/data/models/kyc/kyc_response_model.dart';
import 'package:ovopay/core/data/models/modules/bank_transfer/bank_transfer_add_new_bank_submit_response_model.dart';
import 'package:ovopay/core/data/models/modules/bank_transfer/bank_transfer_history_response_model.dart';
import 'package:ovopay/core/data/models/modules/bank_transfer/bank_transfer_info_response_model.dart';
import 'package:ovopay/core/data/models/modules/bank_transfer/bank_transfer_submit_response_model.dart';
import 'package:ovopay/core/data/models/modules/global/module_transaction_model.dart';
import 'package:ovopay/core/data/repositories/modules/bank_transfer/bank_transfer_repo.dart';

import '../../../../core/utils/util_exporter.dart';

class BankTransferController extends GetxController {
  BankTransferRepo bankTransferRepo;
  BankTransferController({required this.bankTransferRepo});

  bool isPageLoading = false;

  TextEditingController bankNameController = TextEditingController();
  TextEditingController bankAccountNameController = TextEditingController();
  TextEditingController bankAccountNumberController = TextEditingController();
  TextEditingController amountController = TextEditingController();
  TextEditingController pinController = TextEditingController();

  // Get Phone or username or Amount
  String get getBankName => bankNameController.text;
  String get getAmount => amountController.text;
  //Otp Type
  List<String> otpType = [];
  String selectedOtpType = "";
  //current balance
  double userCurrentBalance = 0.0;
  //Charge
  GlobalChargeModel? globalChargeModel;

  //Ngo List
  List<BankDataModel> bankListDataList = [];
  List<BankDataModel> filterBankListDataList = [];
  BankDataModel? selectedBank;
  //Latest history
  List<MyAddedBank> mySavedBankList = [];
  MyAddedBank? selectedMyAccount;

  //Selected saved account data
  List<UsersDynamicFormSubmittedDataModel>? selectedBankDynamicFormAutofillData;

  // Success Model
  ModuleGlobalSubmitTransactionModel? moduleGlobalSubmitTransactionModel;

  //Action ID
  String actionRemark = "bank_transfer";

  Future initController({bool forceLoad = true}) async {
    isPageLoading = forceLoad;
    update();
    await loadBankTransferInfo();
    isPageLoading = false;
    update();
  }

  //Informations
  Future<void> loadBankTransferInfo() async {
    try {
      ResponseModel responseModel = await bankTransferRepo.bankTransferInfoData();
      if (responseModel.statusCode == 200) {
        final bankTransferInfoResponseModel = bankTransferInfoResponseModelFromJson(
          jsonEncode(responseModel.responseJson),
        );
        if (bankTransferInfoResponseModel.status == "success") {
          final data = bankTransferInfoResponseModel.data;
          if (data != null) {
            otpType = data.otpType ?? [];
            globalChargeModel = data.bankTransferCharge;
            userCurrentBalance = data.getCurrentBalance();

            if (data.allBanks != null) {
              bankListDataList = data.allBanks ?? [];
              filterBankListDataList = bankListDataList;
            }
            if (data.mySavedBanks != null) {
              mySavedBankList = data.mySavedBanks ?? [];
            }
            update();
          }
        } else {
          CustomSnackBar.error(
            errorList: bankTransferInfoResponseModel.message ?? [MyStrings.somethingWentWrong],
          );
        }
      } else {
        CustomSnackBar.error(errorList: [responseModel.message]);
      }
    } catch (e) {
      printE(e.toString());
    }
  }

  void filterBankListName(String name) {
    selectedMyAccount = null;
    var filteredList = filterBankListDataList
        .where(
          (ngo) => ngo.name?.toLowerCase().contains(name.toLowerCase()) ?? false,
        )
        .toList();
    if (name.trim().isNotEmpty) {
      filterBankListDataList = filteredList;
    } else {
      filterBankListDataList = bankListDataList;
    }
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

  List<MyAddedBank> getUniqueBankIdList() {
    final uniqueBankIds = <String>{}; // Set to track unique bank IDs
    return mySavedBankList
        .where(
          (item) => uniqueBankIds.add(item.bankId ?? ""),
        ) // Add only if the bankId is not already in the set
        .toList();
  }

  //Select Bank

  void selectBankAccount(MyAddedBank? value) {
    selectedMyAccount = null;
    update();
    selectedMyAccount = value;

    update();
  }

  void selectBankOnTap(BankDataModel value) {
    selectedBank = value;

    update();
  }

  //Select autofill data
  void selectedBankDynamicFormAutofillDataOnTap(
    List<UsersDynamicFormSubmittedDataModel>? value,
  ) {
    selectedBankDynamicFormAutofillData = null;
    update();
    selectedBankDynamicFormAutofillData = value;
    bankAccountNameController.text = selectedMyAccount?.accountHolder ?? "";
    bankAccountNumberController.text = selectedMyAccount?.accountNumber ?? "";
    update();
  }

  //Amount text changes
  void onChangeAmountControllerText(String value) {
    amountController.text = value;
    changeInfoWidget();
    update();
  }

  void clearFormData({VoidCallback? moreCallback}) {
    selectedBank = null;
    selectedMyAccount = null;
    amountController.clear();
    bankAccountNameController.clear();
    bankAccountNumberController.clear();
    if (moreCallback != null) {
      moreCallback();
    }
    update();
  }

  void clearTextEditingControllers() {
    bankAccountNameController.clear();
    bankAccountNumberController.clear();
    amountController.clear();
    pinController.clear();
  }
  //Charge calculation

  double mainAmount = 0;
  String totalCharge = "";
  String payableAmountText = "";

  void changeInfoWidget() {
    mainAmount = double.tryParse(amountController.text) ?? 0.0;
    update();
    double percent = 0;
    double percentCharge = 0;
    double fixedCharge = 0;
    double tempTotalCharge = 0;
    //Charge calculation
    if (selectedBank?.percentCharge == null) {
      percent = double.tryParse(globalChargeModel?.percentCharge ?? "0") ?? 0;
    } else {
      percent = double.tryParse(selectedBank?.percentCharge ?? "0") ?? 0;
    }
    percentCharge = mainAmount * percent / 100;
    if (selectedBank?.fixedCharge == null) {
      fixedCharge = double.tryParse(globalChargeModel?.fixedCharge ?? "0") ?? 0;
    } else {
      fixedCharge = double.tryParse(selectedBank?.fixedCharge ?? "0") ?? 0;
    }
    tempTotalCharge = percentCharge + fixedCharge;

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
    void Function(BankTransferSubmitResponseModel)? onSuccessCallback,
    void Function(BankTransferSubmitResponseModel)? onVerifyOtpCallback,
    required List<KycFormModel> dynamicFormList,
  }) async {
    try {
      isSubmitLoading = true;
      update();
      ResponseModel responseModel = await bankTransferRepo.bankTransferOneTimeRequest(
        bankId: selectedBank?.id.toString() ?? "",
        accountName: bankAccountNameController.text,
        accountNumber: bankAccountNumberController.text,
        amount: amountController.text,
        otpType: selectedOtpType,
        dynamicFormList: dynamicFormList,
      );
      if (responseModel.statusCode == 200) {
        BankTransferSubmitResponseModel bankTransferSubmitResponseModel = BankTransferSubmitResponseModel.fromJson(
          responseModel.responseJson,
        );

        if (bankTransferSubmitResponseModel.status == "success") {
          if (bankTransferSubmitResponseModel.remark == "otp") {
            if (onVerifyOtpCallback != null) {
              onVerifyOtpCallback(bankTransferSubmitResponseModel);
            }
            update();
          } else {
            if (bankTransferSubmitResponseModel.remark == "pin") {
              if (onSuccessCallback != null) {
                onSuccessCallback(bankTransferSubmitResponseModel);
              }
            }
          }
        } else {
          CustomSnackBar.error(
            errorList: bankTransferSubmitResponseModel.message ?? [MyStrings.somethingWentWrong],
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
    void Function(BankTransferSubmitResponseModel)? onSuccessCallback,
  }) async {
    try {
      isSubmitLoading = true;
      update();
      ResponseModel responseModel = await bankTransferRepo.pinVerificationRequest(pin: pinController.text);
      if (responseModel.statusCode == 200) {
        BankTransferSubmitResponseModel bankTransferSubmitResponseModel = BankTransferSubmitResponseModel.fromJson(
          responseModel.responseJson,
        );

        if (bankTransferSubmitResponseModel.status == "success") {
          moduleGlobalSubmitTransactionModel = bankTransferSubmitResponseModel.data?.bankTransfer;
          if (moduleGlobalSubmitTransactionModel != null) {
            if (onSuccessCallback != null) {
              onSuccessCallback(bankTransferSubmitResponseModel);
            }
          }
        } else {
          CustomSnackBar.error(
            errorList: bankTransferSubmitResponseModel.message ?? [MyStrings.somethingWentWrong],
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
  //Save Bank

  bool isSubmitSaveBankLoading = false;
  Future<void> submitSaveBankAccountProcess({
    required List<KycFormModel> dynamicFormList,
    VoidCallback? onSuccessCallback,
  }) async {
    try {
      isSubmitSaveBankLoading = true;
      update();
      ResponseModel responseModel = await bankTransferRepo.saveBankAccountRequest(
        bankId: selectedBank?.id.toString() ?? "",
        accountName: bankAccountNameController.text,
        accountNumber: bankAccountNumberController.text,
        dynamicFormList: dynamicFormList,
      );
      if (responseModel.statusCode == 200) {
        BankTransferAddNewBankSubmitResponseModel bankTransferAddNewBankSubmitResponseModel = BankTransferAddNewBankSubmitResponseModel.fromJson(
          responseModel.responseJson,
        );

        if (bankTransferAddNewBankSubmitResponseModel.status == "success") {
          if (onSuccessCallback != null) {
            onSuccessCallback();
          }
          CustomSnackBar.success(
            successList: bankTransferAddNewBankSubmitResponseModel.message ?? [MyStrings.requestSuccess],
          );
        } else {
          CustomSnackBar.error(
            errorList: bankTransferAddNewBankSubmitResponseModel.message ?? [MyStrings.somethingWentWrong],
          );
        }
        update();
      } else {
        CustomSnackBar.error(errorList: [responseModel.message]);
      }
    } catch (e) {
      printE(e.toString());
    } finally {
      isSubmitSaveBankLoading = false;
      update();
    }
  }

  bool isDeleteSaveBankLoading = false;
  String isDeleteSaveBankIDLoading = "-1";
  Future<void> deleteBankAccount({String bankAccountID = ""}) async {
    try {
      isSubmitSaveBankLoading = true;
      isDeleteSaveBankIDLoading = bankAccountID;
      update();
      ResponseModel responseModel = await bankTransferRepo.deleteBankAccount(
        bankAccountID,
      );
      if (responseModel.statusCode == 200) {
        BankTransferAddNewBankSubmitResponseModel bankTransferAddNewBankSubmitResponseModel = BankTransferAddNewBankSubmitResponseModel.fromJson(
          responseModel.responseJson,
        );

        if (bankTransferAddNewBankSubmitResponseModel.status == "success") {
          CustomSnackBar.success(
            successList: bankTransferAddNewBankSubmitResponseModel.message ?? [MyStrings.requestSuccess],
          );
          initController(forceLoad: false);
        } else {
          CustomSnackBar.error(
            errorList: bankTransferAddNewBankSubmitResponseModel.message ?? [MyStrings.somethingWentWrong],
          );
        }
        update();
      } else {
        CustomSnackBar.error(errorList: [responseModel.message]);
      }
    } catch (e) {
      printE(e.toString());
    } finally {
      isDeleteSaveBankIDLoading = "-1";
      isSubmitSaveBankLoading = false;
      update();
    }
  }
  //Save Bank end

  //History

  int currentIndex = 0;
  void initialHistoryData() async {
    isHistoryLoading = true;
    page = 0;
    nextPageUrl = null;
    bankTransferHistoryList.clear();

    await getBankTransferHistoryDataList();
  }

  bool isHistoryLoading = false;
  int page = 1;
  String? nextPageUrl;
  List<BankTransferDataModel> bankTransferHistoryList = [];
  Future<void> getBankTransferHistoryDataList({bool forceLoad = true}) async {
    try {
      page = page + 1;
      isHistoryLoading = forceLoad;
      update();
      ResponseModel responseModel = await bankTransferRepo.bankTransferHistory(
        page,
      );
      if (responseModel.statusCode == 200) {
        final bankTransferHistoryResponseModel = bankTransferHistoryResponseModelFromJson(
          jsonEncode(responseModel.responseJson),
        );
        if (bankTransferHistoryResponseModel.status == "success") {
          nextPageUrl = bankTransferHistoryResponseModel.data?.history?.nextPageUrl;
          bankTransferHistoryList.addAll(
            bankTransferHistoryResponseModel.data?.history?.data ?? [],
          );
        } else {
          CustomSnackBar.error(
            errorList: bankTransferHistoryResponseModel.message ?? [MyStrings.somethingWentWrong],
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
