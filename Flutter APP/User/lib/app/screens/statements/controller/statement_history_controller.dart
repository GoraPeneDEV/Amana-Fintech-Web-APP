import 'dart:convert';

import 'package:get/get.dart';
import 'package:intl/intl.dart';
import 'package:ovopay/app/components/snack_bar/show_custom_snackbar.dart';
import 'package:ovopay/core/data/models/global/response_model/response_model.dart';
import 'package:ovopay/core/data/models/transaction_history/statement_history_model.dart';
import 'package:ovopay/core/data/repositories/transaction_history/transaction_history_repo.dart';

import '../../../../core/utils/util_exporter.dart';

class StatementHistoryController extends GetxController {
  TransactionHistoryRepo transactionHistoryRepo;
  StatementHistoryController({required this.transactionHistoryRepo});

  //History
  String month = "";
  String monthInText = "";
  String year = "";

  int currentIndex = 0;
  Future initialHistoryData({bool forceLoad = true}) async {
    isStatementLoading = forceLoad;
    DateTime now = DateTime.now();

    month = now.month.toString(); // Numeric month
    monthInText = DateFormat(
      'MMMM',
    ).format(now); // Full month name (e.g., September)

    year = now.year.toString(); // Year as 2024

    await getStatementsHistoryDataList(forceLoad: forceLoad);
  }

  bool isStatementLoading = false;
  StatementsData? statementsData;
  Future<void> getStatementsHistoryDataList({
    bool forceLoad = true,
    bool forceDate = false,
    bool isIncrease = true,
  }) async {
    try {
      isStatementLoading = forceLoad;
      if (forceDate) {
        DateTime now = DateTime(
          int.parse(year),
          int.parse(month),
        ); // Create a date object from current month and year

        if (isIncrease) {
          now = DateTime(now.year, now.month + 1); // Increase month
          monthInText = DateFormat(
            'MMMM',
          ).format(now); // Full month name (e.g., September)
        } else {
          now = DateTime(now.year, now.month - 1); // Decrease month
          monthInText = DateFormat(
            'MMMM',
          ).format(now); // Full month name (e.g., September)
        }

        month = now.month.toString(); // Update month
        year = now.year.toString(); // Update year
      }

      update();

      ResponseModel responseModel = await transactionHistoryRepo.statementsHistory(month: month, year: year);

      if (responseModel.statusCode == 200) {
        final statementHistoryResponseModel = statementHistoryResponseModelFromJson(
          jsonEncode(responseModel.responseJson),
        );
        if (statementHistoryResponseModel.status == "success") {
          statementsData = statementHistoryResponseModel.data;
        } else {
          CustomSnackBar.error(
            errorList: statementHistoryResponseModel.message ?? [MyStrings.somethingWentWrong],
          );
        }
        update();
        isStatementLoading = false;
        update();
      } else {
        CustomSnackBar.error(errorList: [responseModel.message]);
      }
    } catch (e) {
      printE(e.toString());
    }
    isStatementLoading = false;
    update();
  }
}
