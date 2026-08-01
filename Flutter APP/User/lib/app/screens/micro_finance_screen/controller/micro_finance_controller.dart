import 'dart:convert';
import 'dart:io';

import 'package:flutter/material.dart';
import 'package:get/get.dart';
import 'package:ovopay/app/components/snack_bar/show_custom_snackbar.dart';
import 'package:ovopay/core/data/models/global/charges/global_charge_model.dart';
import 'package:ovopay/core/data/models/global/response_model/response_model.dart';
import 'package:ovopay/core/data/models/kyc/kyc_response_model.dart';
import 'package:ovopay/core/data/models/modules/global/module_transaction_model.dart';
import 'package:ovopay/core/data/models/modules/microfinance/micro_finance_history_response_model.dart';
import 'package:ovopay/core/data/models/modules/microfinance/micro_finance_response_model.dart';
import 'package:ovopay/core/data/models/modules/microfinance/micro_finance_submit_response_model.dart';
import 'package:ovopay/core/data/repositories/modules/micro_finance/micro_finance_repo.dart';
import 'package:ovopay/core/data/services/api_service.dart';

import '../../../../core/utils/util_exporter.dart';

class MicroFinanceController extends GetxController {
  MicroFinanceRepo microfinanceRepo;
  MicroFinanceController({required this.microfinanceRepo});
  String moduleName = "Microfinance";
  bool isPageLoading = false;

  TextEditingController organizationNameController = TextEditingController();
  TextEditingController amountController = TextEditingController();
  TextEditingController pinController = TextEditingController();

  // Get Phone or username or Amount
  String get getOrganizationNameOrType => organizationNameController.text;
  String get getAmount => amountController.text;
  //Otp Type
  List<String> otpType = [];
  String selectedOtpType = "";
  //current balance
  double userCurrentBalance = 0.0;
  //Charge
  GlobalChargeModel? globalChargeModel;

  //Ngo List
  List<MicrofinanceNgo> ngoListDataList = [];
  List<MicrofinanceNgo> filterNgoListDataList = [];
  MicrofinanceNgo? selectedNgo;
  //Latest history
  List<LatestMicrofinancePayHistoryModel> latestHistory = [];

  // Success Model
  ModuleGlobalSubmitTransactionModel? moduleGlobalSubmitTransactionModel;

  //Action ID
  String actionRemark = "microfinance";

  Future initController() async {
    isPageLoading = true;
    update();
    await loadMicrofinanceInfo();
    isPageLoading = false;
    update();
  }

  //Informations
  Future<void> loadMicrofinanceInfo() async {
    try {
      ResponseModel responseModel = await microfinanceRepo.microfinanceInfoData();
      if (responseModel.statusCode == 200) {
        final microFinanceResponseModel = microFinanceResponseModelFromJson(
          jsonEncode(responseModel.responseJson),
        );
        if (microFinanceResponseModel.status == "success") {
          final data = microFinanceResponseModel.data;
          if (data != null) {
            userCurrentBalance = data.getCurrentBalance();

            otpType = data.otpType ?? [];
            globalChargeModel = data.microfinanceGlobalCharge;

            if (data.allNgoList != null) {
              ngoListDataList = data.allNgoList ?? [];
              filterNgoListDataList = ngoListDataList;
            }
            if (data.latestMicrofinancePayHistory != null) {
              latestHistory = data.latestMicrofinancePayHistory ?? [];
            }
            update();
          }
        } else {
          CustomSnackBar.error(
            errorList: microFinanceResponseModel.message ?? [MyStrings.somethingWentWrong],
          );
        }
      } else {
        CustomSnackBar.error(errorList: [responseModel.message]);
      }
    } catch (e) {
      printE(e.toString());
    }
  }

  void filterNgoListName(String name) {
    selectedNgo = null;
    var filteredList = filterNgoListDataList
        .where(
          (ngo) => ngo.name?.toLowerCase().contains(name.toLowerCase()) ?? false,
        )
        .toList();
    if (name.trim().isNotEmpty) {
      filterNgoListDataList = filteredList;
    } else {
      filterNgoListDataList = ngoListDataList;
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

  //Select Ngo
  void selectedNgByTappingTransactionOnTap(LatestMicrofinancePayHistoryModel value) {
    selectedNgo = null;
    update();
    if (value.ngo != null) {
      MicrofinanceNgo? data = ngoListDataList.firstWhereOrNull(
        (element) => element.id == value.ngo?.id,
      );
      selectedNgo = data;
    }

    update();
  }

  void selectedNgoOnTap(MicrofinanceNgo value) {
    selectedNgo = null;
    update();
    selectedNgo = value;

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

  void changeInfoWidget() {
    mainAmount = double.tryParse(amountController.text) ?? 0.0;
    update();
    double percent = 0;
    double percentCharge = 0;
    double fixedCharge = 0;
    double tempTotalCharge = 0;
    //Charge calculation
    if (selectedNgo?.percentCharge == null) {
      percent = double.tryParse(globalChargeModel?.percentCharge ?? "0") ?? 0;
    } else {
      percent = double.tryParse(selectedNgo?.percentCharge ?? "0") ?? 0;
    }
    percentCharge = mainAmount * percent / 100;

    if (selectedNgo?.fixedCharge == null) {
      fixedCharge = double.tryParse(globalChargeModel?.fixedCharge ?? "0") ?? 0;
    } else {
      fixedCharge = double.tryParse(selectedNgo?.fixedCharge ?? "0") ?? 0;
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
    void Function(MicroFinanceSubmitResponseModel)? onSuccessCallback,
    void Function(MicroFinanceSubmitResponseModel)? onVerifyOtpCallback,
    required List<KycFormModel> dynamicFormList,
  }) async {
    try {
      isSubmitLoading = true;
      update();
      ResponseModel responseModel = await microfinanceRepo.billPayRequest(
        ngoId: selectedNgo?.id.toString() ?? "",
        amount: amountController.text,
        pin: pinController.text,
        otpType: selectedOtpType,
        dynamicFormList: dynamicFormList,
      );
      if (responseModel.statusCode == 200) {
        MicroFinanceSubmitResponseModel microFinanceSubmitResponseModel = MicroFinanceSubmitResponseModel.fromJson(
          responseModel.responseJson,
        );
        if (microFinanceSubmitResponseModel.status == "success") {
          if (microFinanceSubmitResponseModel.remark == "otp") {
            if (onVerifyOtpCallback != null) {
              onVerifyOtpCallback(microFinanceSubmitResponseModel);
            }
            update();
          } else {
            if (microFinanceSubmitResponseModel.remark == "pin") {
              if (onSuccessCallback != null) {
                onSuccessCallback(microFinanceSubmitResponseModel);
              }
            }
          }
        } else {
          CustomSnackBar.error(
            errorList: microFinanceSubmitResponseModel.message ?? [MyStrings.somethingWentWrong],
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
    void Function(MicroFinanceSubmitResponseModel)? onSuccessCallback,
  }) async {
    try {
      isSubmitLoading = true;
      update();
      ResponseModel responseModel = await microfinanceRepo.pinVerificationRequest(pin: pinController.text);
      if (responseModel.statusCode == 200) {
        MicroFinanceSubmitResponseModel sendMoneyResponseModel = MicroFinanceSubmitResponseModel.fromJson(
          responseModel.responseJson,
        );

        if (sendMoneyResponseModel.status == "success") {
          moduleGlobalSubmitTransactionModel = sendMoneyResponseModel.data?.microfinancePayData;
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
  //History

  int currentIndex = 0;
  void initialHistoryData() async {
    isHistoryLoading = true;
    page = 0;
    nextPageUrl = null;
    microFinanceHistoryList.clear();

    await getMicroFinanceHistoryDataList();
  }

  bool isHistoryLoading = false;
  int page = 1;
  String? nextPageUrl;
  List<LatestMicrofinancePayHistoryModel> microFinanceHistoryList = [];
  Future<void> getMicroFinanceHistoryDataList({bool forceLoad = true}) async {
    try {
      page = page + 1;
      isHistoryLoading = forceLoad;
      update();
      ResponseModel responseModel = await microfinanceRepo.microfinanceHistory(
        page,
      );
      if (responseModel.statusCode == 200) {
        final microFinanceHistoryResponseModel = microFinanceHistoryResponseModelFromJson(
          jsonEncode(responseModel.responseJson),
        );
        if (microFinanceHistoryResponseModel.status == "success") {
          nextPageUrl = microFinanceHistoryResponseModel.data?.history?.nextPageUrl;
          microFinanceHistoryList.addAll(
            microFinanceHistoryResponseModel.data?.history?.data ?? [],
          );
        } else {
          CustomSnackBar.error(
            errorList: microFinanceHistoryResponseModel.message ?? [MyStrings.somethingWentWrong],
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
    LatestMicrofinancePayHistoryModel item,
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
              url: "${UrlContainer.microfinanceDownloadEndPoint}/${item.id}",
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
