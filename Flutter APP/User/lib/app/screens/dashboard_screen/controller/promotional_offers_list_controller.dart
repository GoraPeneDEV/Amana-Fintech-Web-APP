import 'dart:convert';

import 'package:get/get.dart';
import 'package:ovopay/app/components/snack_bar/show_custom_snackbar.dart';
import 'package:ovopay/core/data/models/global/response_model/response_model.dart';
import 'package:ovopay/core/data/models/home/offers_response_model.dart';
import 'package:ovopay/core/data/repositories/home/home_repo.dart';

import '../../../../core/utils/util_exporter.dart';

class PromotionalOffersListController extends GetxController {
  HomeRepo homeRepo = HomeRepo();

  bool isPageLoading = true;

  //History

  int currentIndex = 0;
  Future initialHistoryData() async {
    isHistoryLoading = true;
    page = 0;
    nextPageUrl = null;
    promotionalOffersList.clear();

    await getPromotionalOffersDataList();
  }

  bool isHistoryLoading = false;
  int page = 1;
  String? nextPageUrl;
  List<OfferModel> promotionalOffersList = [];
  Future<void> getPromotionalOffersDataList({bool forceLoad = true}) async {
    try {
      page = page + 1;
      isHistoryLoading = forceLoad;
      update();
      ResponseModel responseModel = await homeRepo.promotionalOffersList(page);

      if (responseModel.statusCode == 200) {
        final offersResponseModel = offersResponseModelFromJson(
          jsonEncode(responseModel.responseJson),
        );
        if (offersResponseModel.status == "success") {
          nextPageUrl = offersResponseModel.data?.offers?.nextPageUrl;
          promotionalOffersList.addAll(
            offersResponseModel.data?.offers?.data ?? [],
          );
        } else {
          CustomSnackBar.error(
            errorList: offersResponseModel.message ?? [MyStrings.somethingWentWrong],
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
