import 'dart:convert';

import 'package:flutter/material.dart';
import 'package:get/get.dart';
import 'package:ovopay/app/components/snack_bar/show_custom_snackbar.dart';
import 'package:ovopay/core/data/models/global/response_model/response_model.dart';
import 'package:ovopay/core/data/models/modules/donation/donation_history_response_model.dart';
import 'package:ovopay/core/data/models/modules/donation/donation_response_model.dart';
import 'package:ovopay/core/data/models/modules/donation/donation_submit_response_model.dart';
import 'package:ovopay/core/data/repositories/modules/donation/donation_repo.dart';

import '../../../../core/utils/util_exporter.dart';

class DonationController extends GetxController {
  DonationRepo donationRepo;
  DonationController({required this.donationRepo});

  bool isPageLoading = false;

  TextEditingController organizationNameController = TextEditingController();
  TextEditingController nameController = TextEditingController();
  TextEditingController emailController = TextEditingController();
  TextEditingController noteController = TextEditingController();
  TextEditingController amountController = TextEditingController();
  TextEditingController pinController = TextEditingController();
  bool isHideMyIdentities = false;

  // Get Phone or username or Amount
  String get getOrganizationNameOrType => organizationNameController.text;
  String get getAmount => amountController.text;
  //Otp Type
  List<String> otpType = [];
  String selectedOtpType = "";
  //current balance
  double userCurrentBalance = 0.0;

  //Ngo List
  List<DonationOrganizationModel> organizationListDataList = [];
  List<DonationOrganizationModel> filterOrganizationListDataList = [];
  DonationOrganizationModel? selectedOrganization;
  //Latest history
  List<DonationDataModel> latestHistory = [];

  // Success Model
  DonationDataModel? donationSubmitDataInfoModel;

  //Action ID
  String actionRemark = "donation";

  Future initController() async {
    isPageLoading = true;
    update();
    await loadDonationInfo();
    isPageLoading = false;
    update();
  }

  //Informations
  Future<void> loadDonationInfo() async {
    try {
      ResponseModel responseModel = await donationRepo.donationInfoData();
      if (responseModel.statusCode == 200) {
        final donationResponseModel = donationResponseModelFromJson(
          jsonEncode(responseModel.responseJson),
        );
        if (donationResponseModel.status == "success") {
          final data = donationResponseModel.data;
          if (data != null) {
            userCurrentBalance = data.getCurrentBalance();

            otpType = data.otpType ?? [];

            if (data.donationOrganizations != null) {
              organizationListDataList = data.donationOrganizations ?? [];
              filterOrganizationListDataList = organizationListDataList;
            }
            if (data.latestDonationHistory != null) {
              latestHistory = data.latestDonationHistory ?? [];
            }
            update();
          }
        } else {
          CustomSnackBar.error(
            errorList: donationResponseModel.message ?? [MyStrings.somethingWentWrong],
          );
        }
      } else {
        CustomSnackBar.error(errorList: [responseModel.message]);
      }
    } catch (e) {
      printE(e.toString());
    }
  }

  void filterOrganizationList(String name) {
    selectedOrganization = null;
    var filteredList = filterOrganizationListDataList
        .where(
          (ngo) => ngo.name?.toLowerCase().contains(name.toLowerCase()) ?? false,
        )
        .toList();
    if (name.trim().isNotEmpty) {
      filterOrganizationListDataList = filteredList;
    } else {
      filterOrganizationListDataList = organizationListDataList;
    }
    update();
  }

  //Toggle Identity
  void toggleIdentity() {
    isHideMyIdentities = !isHideMyIdentities;
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

  //Select Organization

  void selectOrganizationOnTap(DonationOrganizationModel value) {
    selectedOrganization = value;
    update();
  }

  //Amount text changes
  void onChangeAmountControllerText(String value) {
    amountController.text = value;
    update();
  }

  //Submit

  bool isSubmitLoading = false;
  Future<void> submitThisProcess({
    void Function(DonationSubmitResponseModel)? onSuccessCallback,
    void Function(DonationSubmitResponseModel)? onVerifyOtpCallback,
  }) async {
    try {
      isSubmitLoading = true;
      update();
      ResponseModel responseModel = await donationRepo.donationRequest(
        charityId: selectedOrganization?.id.toString() ?? "",
        amount: amountController.text,
        otpType: selectedOtpType,
        name: nameController.text,
        reference: noteController.text,
        email: emailController.text,
        hideIdentity: isHideMyIdentities == true ? "1" : "0",
      );
      if (responseModel.statusCode == 200) {
        DonationSubmitResponseModel donationSubmitResponseModel = DonationSubmitResponseModel.fromJson(responseModel.responseJson);

        if (donationSubmitResponseModel.status == "success") {
          if (donationSubmitResponseModel.remark == "otp") {
            if (onVerifyOtpCallback != null) {
              onVerifyOtpCallback(donationSubmitResponseModel);
            }
            update();
          } else {
            if (donationSubmitResponseModel.remark == "pin") {
              if (onSuccessCallback != null) {
                onSuccessCallback(donationSubmitResponseModel);
              }
            }
          }
        } else {
          CustomSnackBar.error(
            errorList: donationSubmitResponseModel.message ?? [MyStrings.somethingWentWrong],
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
    void Function(DonationSubmitResponseModel)? onSuccessCallback,
  }) async {
    try {
      isSubmitLoading = true;
      update();
      ResponseModel responseModel = await donationRepo.pinVerificationRequest(
        pin: pinController.text,
      );
      if (responseModel.statusCode == 200) {
        DonationSubmitResponseModel donationSubmitResponseModel = DonationSubmitResponseModel.fromJson(responseModel.responseJson);

        if (donationSubmitResponseModel.status == "success") {
          donationSubmitDataInfoModel = donationSubmitResponseModel.data?.donation;
          if (donationSubmitDataInfoModel != null) {
            if (onSuccessCallback != null) {
              onSuccessCallback(donationSubmitResponseModel);
            }
          }
        } else {
          CustomSnackBar.error(
            errorList: donationSubmitResponseModel.message ?? [MyStrings.somethingWentWrong],
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
    donationHistoryList.clear();

    await getDonationHistoryDataList();
  }

  bool isHistoryLoading = false;
  int page = 1;
  String? nextPageUrl;
  List<DonationDataModel> donationHistoryList = [];
  Future<void> getDonationHistoryDataList({bool forceLoad = true}) async {
    try {
      page = page + 1;
      isHistoryLoading = forceLoad;
      update();
      ResponseModel responseModel = await donationRepo.donationHistory(page);
      if (responseModel.statusCode == 200) {
        final donationHistoryResponseModel = donationHistoryResponseModelFromJson(
          jsonEncode(responseModel.responseJson),
        );
        if (donationHistoryResponseModel.status == "success") {
          nextPageUrl = donationHistoryResponseModel.data?.history?.nextPageUrl;
          donationHistoryList.addAll(
            donationHistoryResponseModel.data?.history?.data ?? [],
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
