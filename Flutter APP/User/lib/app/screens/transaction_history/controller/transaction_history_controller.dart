import 'dart:convert';

import 'package:flutter/material.dart';
import 'package:get/get.dart';
import 'package:ovopay/app/components/snack_bar/show_custom_snackbar.dart';
import 'package:ovopay/core/data/models/global/response_model/response_model.dart';
import 'package:ovopay/core/data/models/transaction_history/transaction_history_model.dart';
import 'package:ovopay/core/data/repositories/transaction_history/transaction_history_repo.dart';

import '../../../../core/utils/util_exporter.dart';

class TransactionHistoryController extends GetxController {
  TransactionHistoryRepo transactionHistoryRepo;
  TransactionHistoryController({required this.transactionHistoryRepo});

  bool isPageLoading = true;

  String getRemarkText(String? remark) {
    switch (remark) {
      case "balance_add":
        return "Balance Added";
      case "balance_subtract":
        return "Balance Subtracted";
      case "add_money":
        return "Add Money";
      case "withdraw":
        return "Money Withdrawn";
      case "cash_in":
        return "Cash In";

      case "send_money":
        return "Send Money";
      case "gift_card_purchase":
        return "Gift Card Purchase";
      case "receive_money":
        return "Money Received";
      case "cash_out":
        return "Cash Out";
      case "make_payment":
        return "Payment";
      case "utility_bill":
        return "Utility Bill Paid";
      case "mobile_recharge":
        return "Mobile Recharge";
      case "donation":
        return "Donation";
      case "bank_transfer":
        return "Bank Transfer";
      case "education_fee":
        return "Education Fee Paid";
      case "request_money_accept":
        return "Requested Money Sent";
      case "requested_money_fund_added":
        return "Requested Money Received";
      case "microfinance":
        return "Microfinance";
      case "reject_mobile_recharge":
        return "Mobile Recharge Rejected";
      case "reject_bank_transfer":
        return "Bank Transfer Rejected";
      case "reject_microfinance":
        return "Microfinance Rejected";
      case "reject_education_fee":
        return "Education Fee Rejected";
      case "reject_utility_bill":
        return "Rejected Utility Bill";
      case "virtual_card_add_fund":
        return "Virtual Card";
      case "top_up":
        return "Top Up";
      case "cashback":
        return "Cash Back";
      case "investment":
        return "Investment";
      case "investment_interest":
        return "Investment Interest";
      case "investment_capital_back":
        return "Investment Capital Back";
      case null:
        return "Select A Remark";
      default:
        return "Unknown Remark";
    }
  }

  //History

  int currentIndex = 0;
  Future initialHistoryData() async {
    isHistoryLoading = true;
    page = 0;
    nextPageUrl = null;
    transactionHistoryList.clear();

    await getTransactionHistoryDataList();
  }

  bool isHistoryLoading = false;
  int page = 1;
  String? nextPageUrl;
  List<TransactionHistoryModel> transactionHistoryList = [];
  Future<void> getTransactionHistoryDataList({bool forceLoad = true}) async {
    try {
      page = page + 1;
      isHistoryLoading = forceLoad;
      update();
      ResponseModel responseModel = await transactionHistoryRepo.transactionHistory(
        page,
        trxType: selectTrxType == "Plus" ? "plus" : (selectTrxType == "Minus" ? "minus" : ""),
        orderBy: (selectOrderBy == "Oldest" ? "asc" : "desc"),
        remark: selectedRemark?.remark ?? "",
        search: searchTrxNoController.text,
      );

      if (responseModel.statusCode == 200) {
        final transactionHistoryResponseModel = transactionHistoryResponseModelFromJson(
          jsonEncode(responseModel.responseJson),
        );
        if (transactionHistoryResponseModel.status == "success") {
          nextPageUrl = transactionHistoryResponseModel.data?.transactions?.nextPageUrl;
          transactionHistoryRemarkList = transactionHistoryResponseModel.data?.remarks ?? [];
          transactionHistoryList.addAll(
            transactionHistoryResponseModel.data?.transactions?.historyData ?? [],
          );
        } else {
          CustomSnackBar.error(
            errorList: transactionHistoryResponseModel.message ?? [MyStrings.somethingWentWrong],
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

  //Filters
  TextEditingController searchTrxNoController = TextEditingController();
  List<TransactionsRemark> transactionHistoryRemarkList = [];
  TransactionsRemark? selectedRemark;
  void onSelectRemarkChanges(TransactionsRemark? value) {
    selectedRemark = value;
    update();
  }

  List<String> dropdownItemsOrderBy = ['Latest', 'Oldest'];
  String? selectOrderBy = "Select order by";
  void onSelectOrderByChanges(String? value) {
    selectOrderBy = value;
    update();
  }

  List<String> dropdownItemsTrxType = ['Plus', 'Minus'];
  String? selectTrxType = "Select trx type";
  void onSelectTrxTypeChanges(String? value) {
    selectTrxType = value;
    update();
  }

  void resetFilter() {
    page = 0;
    selectOrderBy = "Select order by";
    selectTrxType = "Select trx type";
    searchTrxNoController.text = "";
    selectedRemark = null;
    update();

    initialHistoryData();
  }
}
