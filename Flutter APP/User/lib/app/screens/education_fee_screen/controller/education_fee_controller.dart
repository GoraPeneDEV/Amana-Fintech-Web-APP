import 'dart:convert';
import 'dart:io';

import 'package:flutter/material.dart';
import 'package:get/get.dart';
import 'package:ovopay/app/components/snack_bar/show_custom_snackbar.dart';
import 'package:ovopay/core/data/models/global/charges/global_charge_model.dart';
import 'package:ovopay/core/data/models/global/response_model/response_model.dart';
import 'package:ovopay/core/data/models/kyc/kyc_response_model.dart';
import 'package:ovopay/core/data/models/modules/education/education_history_response_model.dart';
import 'package:ovopay/core/data/models/modules/education/education_response_model.dart';
import 'package:ovopay/core/data/models/modules/education/education_submit_response_model.dart';
import 'package:ovopay/core/data/models/modules/global/module_transaction_model.dart';
import 'package:ovopay/core/data/repositories/modules/education/education_repo.dart';
import 'package:ovopay/core/data/services/api_service.dart';

import '../../../../core/utils/util_exporter.dart';

class EducationFeeController extends GetxController {
  EducationRepo educationRepo;
  EducationFeeController({required this.educationRepo});
  String moduleName = "Education Fee";
  bool isPageLoading = true;

  TextEditingController instituteNameController = TextEditingController();
  TextEditingController amountController = TextEditingController();
  TextEditingController pinController = TextEditingController();

  // Get Phone or username or Amount
  String get getOrganizationNameOrType => instituteNameController.text;
  String get getAmount => amountController.text;
  //Otp Type
  List<String> otpType = [];
  String selectedOtpType = "";
  //current balance
  double userCurrentBalance = 0.0;
  //Charge
  GlobalChargeModel? globalChargeModel;
  //Utility category List
  List<EducationCategory> educationCategoryDataList = [];
  EducationCategory? selectedEducationCategory;
  //Utility Company List
  List<InstituteModel> educationInstituteDataList = [];
  List<InstituteModel> filterEducationInstituteDataList = [];
  InstituteModel? selectedEducationInstitute;
  //Latest Education history
  List<LatestEducationFeeHistory> latestEducationHistory = [];

  //Education Success Model
  ModuleGlobalSubmitTransactionModel? moduleGlobalSubmitTransactionModel;
  //Action ID
  String actionRemark = "education_fee";

  Future initController() async {
    isPageLoading = true;
    update();
    await loadEducationInfo();
    isPageLoading = false;
    update();
  }

  //Informations
  Future<void> loadEducationInfo() async {
    try {
      ResponseModel responseModel = await educationRepo.educationInfoData();
      if (responseModel.statusCode == 200) {
        final educationResponseModel = educationResponseModelFromJson(
          jsonEncode(responseModel.responseJson),
        );
        if (educationResponseModel.status == "success") {
          final data = educationResponseModel.data;
          if (data != null) {
            userCurrentBalance = data.getCurrentBalance();

            otpType = data.otpType ?? [];
            globalChargeModel = data.educationFeeGlobalCharge;
            educationCategoryDataList = data.categories ?? [];

            if (educationCategoryDataList.isNotEmpty) {
              educationInstituteDataList.clear();
              for (var item in educationCategoryDataList) {
                educationInstituteDataList.addAll(item.institute ?? []);
              }
              filterEducationInstituteDataList = educationInstituteDataList;
            }

            update();
          }
        } else {
          CustomSnackBar.error(
            errorList: educationResponseModel.message ?? [MyStrings.somethingWentWrong],
          );
        }
      } else {
        CustomSnackBar.error(errorList: [responseModel.message]);
      }
    } catch (e) {
      printE(e.toString());
    }
  }

  void filterEducationInstitute(String name) {
    selectedEducationCategory = null;

    var filteredList1 = educationCategoryDataList
        .where(
          (category) => category.name?.toLowerCase().contains(name.toLowerCase()) ?? false,
        )
        .toList();

    if (filteredList1.isNotEmpty) {
      List<InstituteModel> tempData = [];
      for (var category in filteredList1) {
        for (InstituteModel institute in category.institute ?? []) {
          tempData.add(institute);
        }
      }
      filterEducationInstituteDataList = tempData;
    } else {
      var filteredList2 = educationInstituteDataList
          .where(
            (institute) =>
                institute.name?.toLowerCase().contains(
                      name.toLowerCase(),
                    ) ??
                false,
          )
          .toList();

      if (name.trim().isNotEmpty) {
        filterEducationInstituteDataList = filteredList2;
      } else {
        filterEducationInstituteDataList = educationInstituteDataList;
      }
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

  //Select category
  void selectEducationCategoryOnTap(EducationCategory value) {
    if (selectedEducationCategory == value) {
      selectedEducationCategory = null;
      update();
      return;
    }
    selectedEducationCategory = value;
    update();
  }

  //Select Company
  void selectAnEducationInstituteOnTap(InstituteModel value) {
    selectedEducationInstitute = null;
    update();
    selectedEducationInstitute = value;

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
    if (selectedEducationInstitute?.percentCharge == null) {
      percent = double.tryParse(globalChargeModel?.percentCharge ?? "0") ?? 0;
    } else {
      percent = double.tryParse(selectedEducationInstitute?.percentCharge ?? "0") ?? 0;
    }
    percentCharge = mainAmount * percent / 100;
    if (selectedEducationInstitute?.fixedCharge == null) {
      fixedCharge = double.tryParse(globalChargeModel?.fixedCharge ?? "0") ?? 0;
    } else {
      fixedCharge = double.tryParse(selectedEducationInstitute?.fixedCharge ?? "0") ?? 0;
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
    void Function(EducationSubmitResponseModel)? onSuccessCallback,
    void Function(EducationSubmitResponseModel)? onVerifyOtpCallback,
    required List<KycFormModel> dynamicFormList,
  }) async {
    try {
      isSubmitLoading = true;
      update();
      printE(selectedEducationInstitute?.toJson().toString());
      ResponseModel responseModel = await educationRepo.educationRequest(
        instituteID: selectedEducationInstitute?.id.toString() ?? "",
        amount: amountController.text,
        otpType: selectedOtpType,
        dynamicFormList: dynamicFormList,
      );
      if (responseModel.statusCode == 200) {
        EducationSubmitResponseModel educationSubmitResponseModel = EducationSubmitResponseModel.fromJson(responseModel.responseJson);
        if (educationSubmitResponseModel.status == "success") {
          if (educationSubmitResponseModel.remark == "otp") {
            if (onVerifyOtpCallback != null) {
              onVerifyOtpCallback(educationSubmitResponseModel);
            }
            update();
          } else {
            if (educationSubmitResponseModel.remark == "pin") {
              if (onSuccessCallback != null) {
                onSuccessCallback(educationSubmitResponseModel);
              }
            }
          }
        } else {
          CustomSnackBar.error(
            errorList: educationSubmitResponseModel.message ?? [MyStrings.somethingWentWrong],
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
    void Function(EducationSubmitResponseModel)? onSuccessCallback,
  }) async {
    try {
      isSubmitLoading = true;
      update();
      ResponseModel responseModel = await educationRepo.pinVerificationRequest(
        pin: pinController.text,
      );
      if (responseModel.statusCode == 200) {
        EducationSubmitResponseModel sendMoneyResponseModel = EducationSubmitResponseModel.fromJson(responseModel.responseJson);

        if (sendMoneyResponseModel.status == "success") {
          moduleGlobalSubmitTransactionModel = sendMoneyResponseModel.data?.educationFee;
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
    educationHistoryList.clear();

    await getEducationHistoryDataList();
  }

  bool isHistoryLoading = false;
  int page = 1;
  String? nextPageUrl;
  List<LatestEducationFeeHistory> educationHistoryList = [];
  Future<void> getEducationHistoryDataList({bool forceLoad = true}) async {
    try {
      page = page + 1;
      isHistoryLoading = forceLoad;
      update();
      ResponseModel responseModel = await educationRepo.educationHistory(page);
      if (responseModel.statusCode == 200) {
        final educationHistoryResponseModel = educationHistoryResponseModelFromJson(
          jsonEncode(responseModel.responseJson),
        );
        if (educationHistoryResponseModel.status == "success") {
          nextPageUrl = educationHistoryResponseModel.data?.history?.nextPageUrl;
          educationHistoryList.addAll(
            educationHistoryResponseModel.data?.history?.data ?? [],
          );
        } else {
          CustomSnackBar.error(
            errorList: educationHistoryResponseModel.message ?? [MyStrings.somethingWentWrong],
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
    LatestEducationFeeHistory item,
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
              url: "${UrlContainer.educationDownloadEndPoint}/${item.id}",
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
