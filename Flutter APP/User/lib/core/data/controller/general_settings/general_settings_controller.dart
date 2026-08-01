import 'package:get/get.dart';
import 'package:ovopay/core/data/models/authorization/authorization_response_model.dart';
import 'package:ovopay/core/data/models/global/response_model/response_model.dart';
import 'package:ovopay/core/data/models/user/user_model.dart';
import 'package:ovopay/core/data/repositories/auth/general_setting_repo.dart';
import 'package:ovopay/core/utils/util_exporter.dart';

import '../../../../app/components/snack_bar/show_custom_snackbar.dart';

class GeneralSettingsController extends GetxController {
  GeneralSettingRepo repo;

  GeneralSettingsController({required this.repo});
  bool isLoading = false;
  UserModel? userModel;
  bool en = false;
  bool pn = false;
  bool sn = false;
  bool pmn = false;
  // Toggler methods
  void toggleEn() {
    en = !en;
    update(); // Notify listeners
  }

  void togglePn() {
    pn = !pn;
    update(); // Notify listeners
  }

  void toggleSn() {
    sn = !sn;
    update(); // Notify listeners
  }

  void togglePmn() {
    pmn = !pmn;
    update(); // Notify listeners
  }

  Future<void> loadNotificationSettingsStatusInfo({bool forceLoad = true}) async {
    if (forceLoad) {
      isLoading = true;
      update();
    }
    try {
      ResponseModel responseModel = await repo.getNotificationSettingsInfo();
      if (responseModel.statusCode == 200) {
        AuthorizationResponseModel model = AuthorizationResponseModel.fromJson(
          responseModel.responseJson,
        );

        if (model.status == AppStatus.SUCCESS) {
          userModel = model.data?.user;
          en = userModel?.en == "1" ? true : false;
          pn = userModel?.pn == "1" ? true : false;
          sn = userModel?.sn == "1" ? true : false;
          pmn = userModel?.isAllowPromotionalNotify == "1" ? true : false;
          update();
        } else {
          CustomSnackBar.error(
            errorList: model.message ?? [(MyStrings.somethingWentWrong)],
          );
        }
      } else {
        CustomSnackBar.error(errorList: [responseModel.message]);
      }
    } catch (e) {
      printE(e.toString());
    } finally {
      isLoading = false;
      update();
    }
  }

  Future<void> saveNotificationSettingsStatusInfo() async {
    try {
      ResponseModel responseModel = await repo.setNotificationSettings(
        en: en == true ? "1" : "0",
        pn: pn == true ? "1" : "0",
        sn: sn == true ? "1" : "0",
        isAllowPromotionalNotify: pmn == true ? "1" : "0",
      );
      if (responseModel.statusCode == 200) {
        AuthorizationResponseModel model = AuthorizationResponseModel.fromJson(
          responseModel.responseJson,
        );

        if (model.status == AppStatus.SUCCESS) {
          userModel = model.data?.user;
          en = userModel?.en == "1" ? true : false;
          pn = userModel?.pn == "1" ? true : false;
          pmn = userModel?.isAllowPromotionalNotify == "1" ? true : false;
          update();
          CustomSnackBar.success(
            successList: model.message ?? [(MyStrings.requestSuccess)],
          );
        } else {
          CustomSnackBar.error(
            errorList: model.message ?? [(MyStrings.somethingWentWrong)],
          );
        }
      } else {
        CustomSnackBar.error(errorList: [responseModel.message]);
      }
    } catch (e) {
      printE(e.toString());
    } finally {
      isLoading = false;
      update();
    }
  }
}
