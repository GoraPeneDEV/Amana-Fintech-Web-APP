import 'dart:convert';

import 'package:flutter/material.dart';
import 'package:get/get.dart';
import 'package:ovopay/app/components/snack_bar/show_custom_snackbar.dart';
import 'package:ovopay/core/data/models/global/charges/global_charge_model.dart';
import 'package:ovopay/core/data/models/global/response_model/response_model.dart';
import 'package:ovopay/core/data/models/modules/global/module_transaction_model.dart';

import '../../../../core/data/models/country_model/country_model.dart';
import '../../../../core/data/models/modules/gift_card/all_gift_card_response_model.dart';
import '../../../../core/data/models/modules/gift_card/gift_card_history_response_model.dart';
import '../../../../core/data/models/modules/gift_card/gift_submit_response_model.dart';
import '../../../../core/data/repositories/modules/gift_card/gift_card_repo.dart';
import '../../../../core/data/services/shared_pref_service.dart';
import '../../../../core/utils/util_exporter.dart';

class GiftController extends GetxController {
  GiftCardRepo giftCardRepo;
  GiftController({required this.giftCardRepo});

  bool isPageLoading = false;
  TextEditingController phoneNumberOrUserNameController = TextEditingController();
  TextEditingController amountController = TextEditingController();
  TextEditingController emailController = TextEditingController();
  TextEditingController nameController = TextEditingController();
  TextEditingController quantityController = TextEditingController();
  TextEditingController pinController = TextEditingController();
  TextEditingController searchController = TextEditingController();

  // Get Phone or username or Amount
  String get getPhoneNumber => phoneNumberOrUserNameController.text;
  String get getAmount => amountController.text;
  //Otp Type
  List<String> otpType = [];
  String selectedOtpType = "";
  //current balance
  double userCurrentBalance = 0.0;
  //Charge
  GlobalChargeModel? globalChargeModel;

  //Ngo List

  // Success Model
  ModuleGlobalSubmitTransactionModel? moduleGlobalSubmitTransactionModel;

  //Action ID
  String actionRemark = "gift_card";

  //Gift card

  double availableBalance = 0.00;

  GiftCard selectedGiftCard = GiftCard(id: -1);
  CountryData? selectedCountry = CountryData(id: -1);
  GiftCategory selectedCategory = GiftCategory(id: -1);

  List<GiftCard> allGiftCardList = [];
  List<CountryData> allCountryList = [];
  List<GiftCategory> giftCategoryList = [];

  bool isLoading = true;
  bool isFilterLoading = false;
  String nextPageUrl = "";
  int currentPage = 1;
  String historyNextPageUrl = "";

  String currencySymbol = "";
  String currency = "";

  Future<void> loadGiftCardInfo({bool isPagination = false, bool shouldLoad = true, bool filterLoad = false}) async {
    currencySymbol = SharedPreferenceService.getCurrencySymbol();
    currency = SharedPreferenceService.getCurrencySymbol(isFullText: true);

    nameController.text = SharedPreferenceService.getUserFullName();
    quantityController.text = "1";

    if (isPagination == false) {
      currentPage = 0;
      isLoading = shouldLoad;
      isFilterLoading = filterLoad;
      update();
    }

    currentPage = currentPage + 1;

    String pram = "";

    pram += "?page=$currentPage";

    if (searchController.text.isNotEmpty) {
      pram += "&search=${searchController.text}";
    } else {
      if (selectedCategory.id != -1) {
        pram += "&category_id=${selectedCategory.id ?? ""}";
      }
      if (selectedCountry?.id != -1) {
        pram += "&country_id=${selectedCountry?.id ?? ""}";
      }
    }

    try {
      ResponseModel responseModel = await giftCardRepo.allProductInfoData(pram);
      if (responseModel.statusCode == 200) {
        final giftCardResponseModel = allGiftCardResponseModelFromJson(jsonEncode(responseModel.responseJson));
        if (giftCardResponseModel.status == "success") {
          final data = giftCardResponseModel.data;
          if (data != null) {
            availableBalance = double.tryParse(data.currentBalance ?? "0.00") ?? 0.00;

            otpType = data.supportedOtpTypes ?? [];

            allCountryList = data.countries ?? [];
            giftCategoryList = data.categories ?? [];

            historyNextPageUrl = data.products?.nextPageUrl ?? "";

            if (currentPage == 1) {
              allGiftCardList = data.products?.data ?? [];
            } else {
              if (data.products?.data != null && data.products!.data!.isNotEmpty) {
                allGiftCardList.addAll(data.products?.data ?? []);
              }
            }

            update();
          }
        } else {
          CustomSnackBar.error(
            errorList: giftCardResponseModel.message ?? [MyStrings.somethingWentWrong],
          );
        }
      } else {
        CustomSnackBar.error(errorList: [responseModel.message]);
      }
    } catch (e) {
      printE(e.toString());
    }

    isLoading = false;
    isFilterLoading = false;
    update();
  }

  bool hasNext() {
    return historyNextPageUrl.isNotEmpty && historyNextPageUrl != 'null' ? true : false;
  }

  void selectedCountryData(CountryData value) {
    selectedCountry = (selectedCountry?.id == value.id) ? CountryData(id: -1) : value;
    loadGiftCardInfo(shouldLoad: false, filterLoad: true);
    update();
  }

  void selectedCategoryData(GiftCategory category) {
    selectedCategory = (selectedCategory.id == category.id) ? GiftCategory(id: -1) : category;
    loadGiftCardInfo(shouldLoad: false, filterLoad: true);
    update();
  }

  //Select gift
  void selectGiftOnTap(GiftCard value) {
    selectedGiftCard = value;
    amountController.clear();
    quantityController.text = "1";
    getConvertedAmountAndCharge(value);
    update();
  }

  String getGiftAmount({required GiftCard giftCard}) {
    String amount = "";

    String currencyCode = giftCard.recipientCurrencyCode ?? "";

    if (giftCard.denominationType == "FIXED") {
      for (var i = 0; i < giftCard.fixedRecipientDenominations!.length; i++) {
        amount += "${AppConverter.formatNumber(giftCard.fixedRecipientDenominations?[i] ?? "")} $currencyCode";
        if (i != giftCard.fixedRecipientDenominations!.length - 1) {
          amount += ", ";
        }
      }
    } else {
      amount = "${AppConverter.formatNumber(giftCard.minRecipientDenomination ?? "")} $currencyCode - ${AppConverter.formatNumber(giftCard.maxRecipientDenomination ?? "")} $currencyCode";
    }
    return amount;
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

  //Charge calculation

  String subTotal = "";
  String totalCharge = "";
  String total = "";
  String unitPrice = "";

  void getConvertedAmountAndCharge(GiftCard giftCard) {
    double amount = double.tryParse(amountController.text) ?? 0.00;
    int quantity = int.tryParse(quantityController.text) ?? 0;
    double exchangeRate = double.tryParse(giftCard.recipientToSenderExchangeRate ?? "") ?? 0.00;
    double senderFee = double.tryParse(giftCard.senderFee ?? "") ?? 0.00;
    double senderFeePercentage = double.tryParse(giftCard.senderFeePercentage ?? "") ?? 0.00;
    double convertedAmount = exchangeRate * amount;

    unitPrice = AppConverter.formatNumber(convertedAmount.toString());
    subTotal = AppConverter.formatNumber("${convertedAmount * quantity}");
    totalCharge = AppConverter.formatNumber("${((senderFee + (amount * (senderFeePercentage / 100))) * quantity)}");

    double totalAmount = (convertedAmount * quantity) + (double.tryParse(totalCharge) ?? 0.00);

    total = AppConverter.formatNumber(totalAmount.toString());
    update();
  }

  //Submit

  bool isSubmitLoading = false;
  Future<void> submitThisProcess({
    void Function(GiftSubmitResponseModel)? onSuccessCallback,
    void Function(GiftSubmitResponseModel)? onVerifyOtpCallback,
  }) async {
    try {
      isSubmitLoading = true;
      update();
      ResponseModel responseModel = await giftCardRepo.giftPurchaseRequest(
        giftId: selectedGiftCard.id.toString(),
        amount: amountController.text,
        email: emailController.text,
        name: nameController.text,
        quantity: quantityController.text,
        otpType: selectedOtpType,
      );
      if (responseModel.statusCode == 200) {
        GiftSubmitResponseModel rechargeSubmitResponseModel = GiftSubmitResponseModel.fromJson(responseModel.responseJson);
        if (rechargeSubmitResponseModel.status == "success") {
          if (rechargeSubmitResponseModel.remark == "otp") {
            if (onVerifyOtpCallback != null) {
              onVerifyOtpCallback(rechargeSubmitResponseModel);
            }
            update();
          } else {
            if (rechargeSubmitResponseModel.remark == "pin") {
              if (onSuccessCallback != null) {
                onSuccessCallback(rechargeSubmitResponseModel);
              }
            }
          }
        } else {
          CustomSnackBar.error(
            errorList: rechargeSubmitResponseModel.message ?? [MyStrings.somethingWentWrong],
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
    void Function(GiftSubmitResponseModel)? onSuccessCallback,
  }) async {
    try {
      isSubmitLoading = true;
      update();
      ResponseModel responseModel = await giftCardRepo.pinVerificationRequest(pin: pinController.text);
      if (responseModel.statusCode == 200) {
        GiftSubmitResponseModel giftSubmitResponseModel = GiftSubmitResponseModel.fromJson(responseModel.responseJson);

        if (giftSubmitResponseModel.status == "success") {
          moduleGlobalSubmitTransactionModel = giftSubmitResponseModel.data?.transaction;
          if (moduleGlobalSubmitTransactionModel != null) {
            if (onSuccessCallback != null) {
              onSuccessCallback(giftSubmitResponseModel);
            }
          }
        } else {
          CustomSnackBar.error(
            errorList: giftSubmitResponseModel.message ?? [MyStrings.somethingWentWrong],
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
    // nextPageUrl = null;
    giftCardHistoryList.clear();

    await getMobileRechargeHistoryDataList();
  }

  bool isHistoryLoading = false;
  int page = 1;

  String? nextPageUrlForHistory;
  List<GiftCardHistory> giftCardHistoryList = [];
  Future<void> getMobileRechargeHistoryDataList({bool forceLoad = true}) async {
    try {
      page = page + 1;
      isHistoryLoading = forceLoad;
      update();
      ResponseModel responseModel = await giftCardRepo.giftCardHistory(page);
      if (responseModel.statusCode == 200) {
        final giftCardHistoryResponseModel = giftCardHistoryResponseModelFromJson(
          jsonEncode(responseModel.responseJson),
        );
        if (giftCardHistoryResponseModel.status == "success") {
          nextPageUrlForHistory = giftCardHistoryResponseModel.data?.giftCardPurchases?.nextPageUrl;
          giftCardHistoryList.addAll(
            giftCardHistoryResponseModel.data?.giftCardPurchases?.data ?? [],
          );
        } else {
          CustomSnackBar.error(
            errorList: giftCardHistoryResponseModel.message ?? [MyStrings.somethingWentWrong],
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

  bool hasNextForHistory() {
    return nextPageUrlForHistory != null && nextPageUrlForHistory!.isNotEmpty && nextPageUrlForHistory != 'null' ? true : false;
  }

  bool isSearch = false;
  void onSearch() {
    isSearch = !isSearch;
    update();
  }

  bool isDownloadLoading = false;
}
