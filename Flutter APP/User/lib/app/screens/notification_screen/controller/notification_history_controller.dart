import 'dart:convert';

import 'package:get/get.dart';
import 'package:ovopay/app/components/snack_bar/show_custom_snackbar.dart';
import 'package:ovopay/core/data/models/global/response_model/response_model.dart';
import 'package:ovopay/core/data/models/modules/notification/notification_response_model.dart';

import '../../../../core/data/repositories/modules/notification/notification_history_repo.dart';
import '../../../../core/utils/util_exporter.dart';

class NotificationHistoryController extends GetxController {
  NotificationRepo notificationRepo;
  NotificationHistoryController({required this.notificationRepo});

  bool isPageLoading = true;

  //History

  int currentIndex = 0;
  Future initialHistoryData() async {
    isHistoryLoading = true;
    page = 0;
    nextPageUrl = null;
    notificationHistoryList.clear();

    await getNotificationHistoryDataList();
  }

  bool isHistoryLoading = false;
  int page = 1;
  String? nextPageUrl;
  List<NotificationsDataModel> notificationHistoryList = [];
  Future<void> getNotificationHistoryDataList({bool forceLoad = true}) async {
    try {
      page = page + 1;
      isHistoryLoading = forceLoad;
      update();
      ResponseModel responseModel = await notificationRepo.notificationHistory(
        page,
      );

      if (responseModel.statusCode == 200) {
        final notificationResponseModel = notificationResponseModelFromJson(
          jsonEncode(responseModel.responseJson),
        );
        if (notificationResponseModel.status == "success") {
          nextPageUrl = notificationResponseModel.data?.notifications?.nextPageUrl;
          notificationHistoryList.addAll(
            notificationResponseModel.data?.notifications?.data ?? [],
          );
        } else {
          CustomSnackBar.error(
            errorList: notificationResponseModel.message ?? [MyStrings.somethingWentWrong],
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

  void resetFilter() {
    page = 0;

    update();

    initialHistoryData();
  }
}
