import 'dart:convert';
import 'dart:io';

import 'package:file_picker/file_picker.dart';
import 'package:flutter/material.dart';
import 'package:get/get.dart';
import 'package:ovopay/app/components/snack_bar/show_custom_snackbar.dart';
import 'package:ovopay/app/screens/global/controller/country_controller.dart';
import 'package:ovopay/core/data/models/country_model/country_model.dart';
import 'package:ovopay/core/data/models/global/charges/global_charge_model.dart';
import 'package:ovopay/core/data/models/global/response_model/response_model.dart';
import 'package:ovopay/core/data/models/modules/virtual_cards/virtual_cards_history_response_model.dart';
import 'package:ovopay/core/data/models/modules/virtual_cards/virtual_cards_holder_response_model.dart';
import 'package:ovopay/core/data/models/modules/virtual_cards/virtual_cards_response_model.dart';
import 'package:ovopay/core/data/models/modules/virtual_cards/virtual_cards_single_response_model.dart';
import 'package:ovopay/core/data/repositories/modules/virtual_cards/virtual_cards_repo.dart';
import 'package:ovopay/core/data/services/shared_pref_service.dart';

import '../../../../core/data/models/authorization/authorization_response_model.dart';
import '../../../../core/utils/util_exporter.dart';

class VirtualCardsController extends GetxController {
  VirtualCardsRepo virtualCardsRepo;
  VirtualCardsController({required this.virtualCardsRepo});

  bool isPageLoading = true;

  TextEditingController phoneNumberOrUserNameController = TextEditingController();
  TextEditingController amountController = TextEditingController();
  TextEditingController pinController = TextEditingController();

  // Get Phone or username or Amount
  String get getPhoneOrUsername => phoneNumberOrUserNameController.text;
  String get getAmount => amountController.text;

  String cardHolderType = "1";
  String cardUsabilityType = "0";

  //current balance
  double userCurrentBalance = 0.0;
  //Charge
  GlobalChargeModel? globalChargeModel;

  //Card List
  List<CardDataModel> myCardsList = [];
  //Card Holder
  List<CardHolder> cardHolderList = [];
  CardHolder? selectedCardHolder;
  File? govDocFile1;
  File? govDocFile2;

  // String fileExtensions = "jpg,jpeg,png";
  List<String> fileExtensions = ["jpg", "jpeg", "png", "pdf"];
  //Single card Data
  CardDataModel? singleCardInfoData;
  List<VirtualCardTransactionModel> singleTransactions = [];

  //All Transaction History
  List<VirtualCardTransactionModel> virtualCardTransactionsList = [];
  // Add these properties
  int currentStep = 0;
  Future initController({bool forceLoad = true}) async {
    isPageLoading = forceLoad;
    update();
    initializeCountryData();
    await Future.wait([loadVirtualCardInfo(), loadVirtualCardHolderInfo()]);
    isPageLoading = false;
    update();
  }

  //Informations
  Future<void> loadVirtualCardInfo() async {
    try {
      ResponseModel responseModel = await virtualCardsRepo.cardInfoData();
      if (responseModel.statusCode == 200) {
        final virtualCardInfoResponseModel = virtualCardInfoResponseModelFromJson(
          jsonEncode(responseModel.responseJson),
        );
        if (virtualCardInfoResponseModel.status == "success") {
          final data = virtualCardInfoResponseModel.data;
          if (data != null) {
            if (data.cards != null) {
              myCardsList = data.cards ?? [];
            }
            update();
          }
        } else {
          CustomSnackBar.error(
            errorList: virtualCardInfoResponseModel.message ?? [MyStrings.somethingWentWrong],
          );
        }
      } else {
        CustomSnackBar.error(errorList: [responseModel.message]);
      }
    } catch (e) {
      printE(e.toString());
    }
  }

  Future<void> loadVirtualCardHolderInfo() async {
    try {
      ResponseModel responseModel = await virtualCardsRepo.cardHolderListData();
      if (responseModel.statusCode == 200) {
        final virtualCardHolderResponseModel = virtualCardHolderResponseModelFromJson(
          jsonEncode(responseModel.responseJson),
        );
        if (virtualCardHolderResponseModel.status == "success") {
          final data = virtualCardHolderResponseModel.data;
          fileExtensions = data?.supportedFileFormat ?? ["jpg", "jpeg", "png", "pdf"];
          if (data != null) {
            if (data.cardHolders != null) {
              cardHolderList = data.cardHolders ?? [];
            }
            update();
          }
        } else {
          CustomSnackBar.error(
            errorList: virtualCardHolderResponseModel.message ?? [MyStrings.somethingWentWrong],
          );
        }
      } else {
        CustomSnackBar.error(errorList: [responseModel.message]);
      }
    } catch (e) {
      printE(e.toString());
    }
  }

  bool isSingleCardLoading = false;
  Future<void> loadSingleVirtualCardInfo(
    String cardID, {
    bool forceLoad = true,
  }) async {
    try {
      isSingleCardLoading = forceLoad;
      update();
      ResponseModel responseModel = await virtualCardsRepo.singleCardInfoData(
        cardID: cardID,
      );
      if (responseModel.statusCode == 200) {
        final virtualCardInfoResponseModel = virtualCardSingleResponseModelFromJson(
          jsonEncode(responseModel.responseJson),
        );
        if (virtualCardInfoResponseModel.status == "success") {
          final data = virtualCardInfoResponseModel.data;
          if (data != null) {
            singleCardInfoData = data.card;
            userCurrentBalance = data.getCurrentBalance();
            globalChargeModel = data.charge;
            isSingleCardLoading = false;
            singleTransactions = data.transactions ?? [];
            update();
          }
        } else {
          CustomSnackBar.error(
            errorList: virtualCardInfoResponseModel.message ?? [MyStrings.somethingWentWrong],
          );
        }
      } else {
        CustomSnackBar.error(errorList: [responseModel.message]);
      }
    } catch (e) {
      printE(e.toString());
    } finally {
      isSingleCardLoading = false;
      update();
    }
  }

  // Add this method to update the step
  void setCurrentStep(int step, int totalSteps) {
    // Ensure currentStep is within the valid range: 0 to totalSteps - 1
    currentStep = step.clamp(0, totalSteps - 1);
    update();
  }

  //Select card holder
  void selectCardHolderType(String cardHolderType) {
    this.cardHolderType = cardHolderType;
    selectCardHolder(CardHolder());
    update();
  }

  //Select card usability
  void selectCardUsabilityType(String cardUsabilityType) {
    this.cardUsabilityType = cardUsabilityType;
    update();
  }

  //clear Creation data
  void clearConfidentialData() {
    singleCardInfoData?.cardNumber = null;
    singleCardInfoData?.cvcNumber = null;
    update();
  }

  void clearCreationData() {
    phoneNumberOrUserNameController.clear();
    amountController.clear();
    pinController.clear();
    selectCardHolder(CardHolder());
    cardHolderType = "1";
    cardUsabilityType = "0";
    mainAmount = 0;
    govDocFile1 = null;
    govDocFile2 = null;
    update();
  }

  //Selected card holder
  void selectCardHolder(CardHolder cardHolder) {
    selectedCardHolder = cardHolder;
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
    double percent = double.tryParse(globalChargeModel?.percentCharge ?? "0") ?? 0;
    double percentCharge = mainAmount * percent / 100;

    double fixedCharge = double.tryParse(globalChargeModel?.fixedCharge ?? "0") ?? 0;
    double tempTotalCharge = percentCharge + fixedCharge;
    //Future Implementations (May be)
    // double capAmount = double.tryParse(globalChargeModel?.cap ?? "0") ?? 0;

    // if (capAmount != -1.0 && capAmount != 1 && tempTotalCharge > capAmount) {
    //   tempTotalCharge = capAmount;
    // }
    totalCharge = AppConverter.formatNumber('$tempTotalCharge', precision: 2);
    double payable = tempTotalCharge + mainAmount;
    payableAmountText = payableAmountText.length > 5 ? AppConverter.roundDoubleAndRemoveTrailingZero(payable.toString()) : AppConverter.formatNumber(payable.toString());
    update();
  }
  //Charge calculation end

  //Submit
  bool isCardCreateLoadings = false;
  void createVirtualCard({void Function(String)? onSuccessCallback}) async {
    try {
      isCardCreateLoadings = true;
      update();
      ResponseModel responseModel = await virtualCardsRepo.createVirtualCardRequest(
        cardHolderType: cardHolderType,
        usabilityType: cardUsabilityType,
        cardHolder: selectedCardHolder ?? CardHolder(),
        govFile1: govDocFile1,
        govFile2: govDocFile2,
        countryID: countryData?.id?.toString() ?? "",
      );
      if (responseModel.statusCode == 200) {
        AuthorizationResponseModel virtualCardsResponseModel = AuthorizationResponseModel.fromJson(responseModel.responseJson);
        if (virtualCardsResponseModel.status == "success") {
          // CustomSnackBar.success(successList: virtualCardsResponseModel.message ?? [MyStrings.requestSuccess]);
          if (onSuccessCallback != null) {
            onSuccessCallback(
              virtualCardsResponseModel.message?.isNotEmpty ?? false ? (virtualCardsResponseModel.message?.first ?? "") : "",
            );
          }
          update();
        } else {
          CustomSnackBar.error(
            errorList: virtualCardsResponseModel.message ?? [MyStrings.somethingWentWrong],
            dismissAll: false,
          );
        }
        update();
      }
    } catch (e) {
      printE(e.toString());
    } finally {
      isCardCreateLoadings = false;
      update();
    }
  }

  bool isViewConfidentialDataLoading = false;

  Future<void> viewConfidentialVirtualCardInfo({
    required String cardID,
    required String pin,
  }) async {
    try {
      isViewConfidentialDataLoading = true;
      update();
      ResponseModel responseModel = await virtualCardsRepo.singleCardConfidentialInfoInfoData(cardID: cardID, pin: pin);
      if (responseModel.statusCode == 200) {
        final virtualCardInfoResponseModel = virtualCardSingleResponseModelFromJson(
          jsonEncode(responseModel.responseJson),
        );
        if (virtualCardInfoResponseModel.status == "success") {
          final data = virtualCardInfoResponseModel.data;
          if (data != null) {
            singleCardInfoData?.cardNumber = data.decodedAccountNumber();
            singleCardInfoData?.cvcNumber = data.decodedCvc();
            update();
            Get.back();
          }
        } else {
          CustomSnackBar.error(
            errorList: virtualCardInfoResponseModel.message ?? [MyStrings.somethingWentWrong],
          );
        }
      } else {
        CustomSnackBar.error(errorList: [responseModel.message]);
      }
    } catch (e) {
      printE(e.toString());
    } finally {
      isViewConfidentialDataLoading = false;
      update();
    }
  }

  bool isAddBalanceLoading = false;
  Future<void> addVirtualCardBalance({required String cardID}) async {
    try {
      isAddBalanceLoading = true;
      update();
      ResponseModel responseModel = await virtualCardsRepo.addCardBalance(
        cardID: cardID,
        amount: getAmount,
      );
      if (responseModel.statusCode == 200) {
        AuthorizationResponseModel virtualCardsResponseModel = AuthorizationResponseModel.fromJson(responseModel.responseJson);
        if (virtualCardsResponseModel.status == "success") {
          CustomSnackBar.success(
            successList: virtualCardsResponseModel.message ?? [MyStrings.requestSuccess],
          );
          await loadSingleVirtualCardInfo(cardID, forceLoad: false);
          Get.back();
        } else {
          CustomSnackBar.error(
            errorList: virtualCardsResponseModel.message ?? [MyStrings.somethingWentWrong],
          );
        }
      } else {
        CustomSnackBar.error(errorList: [responseModel.message]);
      }
    } catch (e) {
      printE(e.toString());
    } finally {
      isAddBalanceLoading = false;
      update();
    }
  }

  bool isCancelVirtualCard = false;
  Future<void> cancelVirtualCardBalance({required String cardID}) async {
    try {
      isCancelVirtualCard = true;
      update();
      ResponseModel responseModel = await virtualCardsRepo.cancelVirtualCard(
        cardID: cardID,
      );
      if (responseModel.statusCode == 200) {
        AuthorizationResponseModel virtualCardsResponseModel = AuthorizationResponseModel.fromJson(responseModel.responseJson);
        if (virtualCardsResponseModel.status == "success") {
          CustomSnackBar.success(
            successList: virtualCardsResponseModel.message ?? [MyStrings.requestSuccess],
          );
          await loadSingleVirtualCardInfo(cardID, forceLoad: false);
        } else {
          CustomSnackBar.error(
            errorList: virtualCardsResponseModel.message ?? [MyStrings.somethingWentWrong],
          );
        }
      } else {
        CustomSnackBar.error(errorList: [responseModel.message]);
      }
    } catch (e) {
      printE(e.toString());
    } finally {
      Get.back();
      isCancelVirtualCard = false;
      update();
    }
  }

  //Submit end
  //History

  int currentIndex = 0;
  Future initialHistoryData() async {
    isHistoryLoading = true;
    page = 0;
    nextPageUrl = null;
    virtualCardTransactionsList.clear();

    await getVirtualCardHistoryDataList();
  }

  bool isHistoryLoading = false;
  int page = 1;
  String? nextPageUrl;
  Future<void> getVirtualCardHistoryDataList({bool forceLoad = true}) async {
    try {
      page = page + 1;
      isHistoryLoading = forceLoad;
      update();
      ResponseModel responseModel = await virtualCardsRepo.virtualCardAllHistory(page);
      if (responseModel.statusCode == 200) {
        final virtualCardHistoryResponseModel = virtualCardHistoryResponseModelFromJson(
          jsonEncode(responseModel.responseJson),
        );
        if (virtualCardHistoryResponseModel.status == "success") {
          nextPageUrl = virtualCardHistoryResponseModel.data?.transactions?.nextPageUrl;
          virtualCardTransactionsList.addAll(
            virtualCardHistoryResponseModel.data?.transactions?.data ?? [],
          );
        } else {
          CustomSnackBar.error(
            errorList: virtualCardHistoryResponseModel.message ?? [MyStrings.somethingWentWrong],
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

  bool isImageFile({required List<String> extensions}) {
    // List of non-image file extensions
    const nonImageExtensions = [
      'pdf',
      'doc',
      'docx',
      'csv',
      'txt',
      'xls',
      'xlsx',
    ];

    for (var ext in extensions) {
      if (nonImageExtensions.contains(ext.toLowerCase())) {
        return false; // Return false if any non-image extension is found
      }
    }
    return true; // Return true if no non-image extensions are found
  }

  Future<PlatformFile?> pickFile({List<String>? extention}) async {
    FilePickerResult? result = await FilePicker.platform.pickFiles(
      allowMultiple: false,
      type: FileType.custom,
      allowedExtensions: extention ??
          [
            'jpg',
            'png',
            'jpeg',
            'pdf',
            'doc',
            'docx',
            'csv',
            'txt',
            'docx',
            'xls',
            'xlsx',
          ],
    );

    if (result != null) {
      PlatformFile file = result.files.first;
      return file;
    }

    return null;
  }

  //COUNTRY DATA
  TextEditingController countryController = TextEditingController();
  CountryData? countryData;
  CountryController countryDataController = CountryController();
  void initializeCountryData() {
    //Country data
    countryDataController.initialize();
    if (countryDataController.filteredCountries.isNotEmpty) {
      countryData = countryDataController.selectedCountryData ?? countryDataController.filteredCountries.first;
      countryController.text = countryData?.name ?? "";
      SharedPreferenceService.setSelectedOperatingCountry(
        countryData ?? CountryData(),
      );
    }
    update();
  }

  void selectedCountryData(CountryData value) {
    countryData = value;
    countryController.text = value.name ?? "";
    update();
  }
}
